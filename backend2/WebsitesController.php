<?php
class WebsitesController extends Controller
{
	public function get($id = null) {
		$query = [];
		if($id && strlen($id) == 24) $query['_id'] = new MongoId($id);
		$cursor = $this->m->websites->find($query); 
		
		$this->output($this->cursorToArray($cursor));
	}

	public function closest($id) {
		$cursor = $this->m->websites->find(['_id' => new MongoId($id)]);
		$doc = $this->cursorToArray($cursor)[0];

		$return = $this->findNeighbors($doc['type'], $doc['keywords']);

		$closest = $return;
		foreach ($closest as $key => $value) {
			unset($closest[$key]['data']['keywords']);
			unset($closest[$key]['data']['pages']);
		}
		$this->output($closest);

	}

	public function add() {
		$this->verifyArgs(['url', 'mail', 'place', 'type']);

		$pages = $this->findPages($_GET['url']);

		$keywords = $this->findKeywords($pages);
		if(empty($keywords)) echo 'Le site n\'a pas fourni de keywords.';

		$document = [
			'url' => $_GET['url'],
			'mail' => $_GET['mail'],
			'place' => $_GET['place'],
			'type' => $_GET['type'],
			'keywords' => $keywords,
			'pages' => $pages,
			'adwords' => []
		];

		$neighbors = $this->findNeighbors($document['type'], $document['keywords']);

		$document['adwords'] = $this->findAdwords($neighbors);

		//$this->m->websites->insert($document);

		//$this->output($document['_id']);
	}

	private function findPages($url) {
		require 'vendor/autoload.php';

		// Initiate crawl
		$crawler = new \Arachnid\Crawler($url, 2);

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

		foreach($pages as $page) {$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,$page);
			curl_setopt($ch, CURLOPT_PROXY, $proxy);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,5); 
			curl_setopt($ch, CURLOPT_TIMEOUT, 10); //timeout in seconds
			$t = curl_exec($ch);

			$status = curl_getinfo($ch)['http_code'];
			if(!in_array($status, [200, 301, 302])) break;

			$text .= '. '.$this->html2txt($t);
			curl_close($ch);
		}

		if(strlen($text) < 50) return []; 
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
		$queue = new SplPriorityQueue();
		$queue->setExtractFlags(SplPriorityQueue::EXTR_BOTH);
		$cursor = $this->m->websites->find([]); 
		foreach ($cursor as $doc) {
			$d = $this->distance($type, $keywords, $doc['type'], $doc['keywords']);
			$queue->insert($doc, $d);
		}

		$res = [];
		for($i = 0; $i < 20 && !$queue->isEmpty(); $i++)
			$res[] = $queue->extract();

		return $res;
	}

	// distance inversée, le plus élevé est le meilleur
	private function distance($typeA, $keywordsA, $typeB, $keywordsB) {
		$distance = 0;
		if($typeA == $typeB) $distance += 1;

		foreach($keywordsA as $k => $w) {
			if(isset($keywordsB[$k]))
				$distance += $keywordsB[$k] * $w;
		}
		return $distance;
	}

	public function massivedl() {
		$bdd = new PDO('mysql:host=localhost;dbname=sites', 'root', 'root');

		$sites = $bdd->query('SELECT url FROM sites ORDER BY rand() LIMIT 10000');

		while($site = $sites->fetch()) {
			echo $site['url'] . PHP_EOL;
			flush();
			$pages = $this->findPages($site['url']);
			echo '- Pages found ('.count($pages).')' . PHP_EOL;
			flush();
			$keywords = $this->findKeywords($pages);
			if(empty($keyword)) {
				echo '- No keywords found.' . PHP_EOL;
				flush();
				continue;
			}
			else {
				echo '- Keywords found ('.count($keywords).')' . PHP_EOL;				
				flush();
			}

			$document = [
				'url' => $site['url'],
				'mail' => 'test',
				'place' => 'test',
				'type' => 'test',
				'keywords' => $keywords,
				'pages' => $pages
			];

			$this->m->websites->insert($document);
			echo '- Inserted in database' . PHP_EOL;
			flush();
		}
	}

	public function findAdwords($neighbors) {
		//pr($neighbors);
		$adwords = [];
		foreach($neighbors as $neighbor) {
			foreach($neighbor['data']['adwords'] as $adword => $quality) {
				if(!array_key_exists($adword, $adwords)) 
					$adwords[$adword] = 0;
				$adwords[$adword] += $neighbor['priority'] * $quality;
			}
		}

		$max = max($adwords);

		foreach($adwords as $adword => $quality) 
			$adwords[$adword] = max($quality / $max, 0.05);
		
		return $adwords;
	}

	public function recompute() {
		//1. Compute adwords score from the stats
		//2. Refind new neighbors taking into account the adwords score
		//3. Find potential new adwords to test
		//4. Delete useless / bad quality keywords
	}
}