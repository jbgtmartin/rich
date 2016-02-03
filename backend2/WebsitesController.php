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

		$this->findNeighbors($document['type'], $document['keywords']);

		$this->m->websites->insert($document);

		//$this->output($document['_id']);
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
		$text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
		# Remove non printable character (i.e. below ascii code 32).
		$text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/u', '', $text);
		$text = html_entity_decode($text);
		$keywords = $textrank->getAllKeywordsSorted($text);

		// echo '<br><br />' . $text;

		return $keywords;
	}

	private function findNeighbors($type, $keywords) {
		
	}

	public function massivedl() {
		$bdd = new PDO('mysql:host=localhost;dbname=sites', 'root', 'root');

		$sites = $bdd->query('SELECT url FROM sites ORDER BY rand() LIMIT 10000');

		while($site = $sites->fetch()) {
			echo $site['url'] . PHP_EOL;
			$pages = $this->findPages($site['url']);

			$keywords = $this->findKeywords($pages);

			$document = [
				'url' => $_GET['url'],
				'mail' => 'test',
				'place' => 'test',
				'type' => 'test',
				'keywords' => $keywords,
				'pages' => $pages
			];

			$this->m->websites->insert($document);
		}
	}
}