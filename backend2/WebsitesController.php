<?php
class WebsitesController extends Controller
{
	public function get($id = null) {
		$query = [];
		if($id && strlen($id) == 24) $query['_id'] = new MongoId($id);
		$cursor = $this->m->websites->find($query); 
		
		$this->output($this->cursorToArray($cursor));
	}

	public function add() {
		$this->verifyArgs(['url', 'mail', 'place', 'type']);

		$pages = $this->findPages($_GET['url']);
		$keywords = $this->findKeywords($pages);

		$document = [
			'url' => $_GET['url'],
			'mail' => $_GET['mail'],
			'place' => $_GET['place'],
			'type' => $_GET['type'],
			'keywords' => $keywords,
			'pages' => $pages
		];
		// $this->m->websites->insert($document);
	}

	private function findPages($url) {
		require 'vendor/autoload.php';

		// Initiate crawl
		$crawler = new \Arachnid\Crawler($url, 4);
		$crawler->traverse();

		// Get link data
		$links = $crawler->getLinks();
		$pages = [];

		foreach($links as $link)
		{
			if(isset($link['absolute_url']) && !$link['external_link'])
				$pages[] = $link['absolute_url'];
		}

		return $pages;
	}

	private function html2txt($document){
		$search = array('@<>@',
			'@<script[^>]*?>.*?</script>@si',  // Strip out javascript  
               '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
               '@<embed[^>]*?>.*?</embed>@siU',    // embed
               '@<object[^>]*?>.*?</object>@siU',    // object
	       '@<iframe[^>]*?>.*?</iframe>@siU',    // iframe	       
               '@<![\s\S]*?--[ \t\n\r]*>@',        // Strip multi-line comments including CDATA               
               '@</?[^>]*>*@' 		  // html tags
);
		$text = preg_replace($search, '', $document);
		return strip_tags($text);
	}

	private function findKeywords($pages) {
		$proxy = 'kuzh.polytechnique.fr:8080';
		$text = '';
		foreach($pages as $page) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,$page);
			curl_setopt($ch, CURLOPT_PROXY, $proxy);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			$text .= '. '.$this->html2txt(curl_exec($ch));
			curl_close($ch);
		}

		require "TextRank/vendor/autoload.php";

		$config = new \crodas\TextRank\Config;
		$config->addListener(new \crodas\TextRank\Stopword);

		$textrank = new \crodas\TextRank\TextRank($config);
		$keywords = $textrank->getAllKeywordsSorted($text);

		// echo '<br><br />' . $text;

		var_dump($keywords);
	}
}