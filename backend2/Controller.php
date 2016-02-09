<?php
class Controller {
	protected $m;

	public function __construct() {
		$this->m = (new MongoClient())->rich;
	}
	public function cursorToArray($cursor) {
		$res = [];
		foreach ($cursor as $doc) {
			$doc['_id'] = $doc['_id']->__toString();
			$res[] = $doc;
		}

		return $res;
	}

	public function output($array) {
		echo json_encode($array);
	}

	public function verifyArgs($args) {
		foreach($args as $arg) {
			if(!isset($_GET[$arg])) die('Un argument est manquant : ' . $arg . '.');
		}
	}

	protected function getWebsites($id = null) {
		$query = [];
		if($id && strlen($id) == 24) $query['_id'] = new MongoId($id);
		$cursor = $this->m->websites->find($query); 
		
		return $this->cursorToArray($cursor);
	}

	protected function closestWebsites($id) {
		$cursor = $this->m->websites->find(['_id' => new MongoId($id)]);
		$doc = $this->cursorToArray($cursor)[0];

		$return = $this->findNeighbors($doc['type'], $doc['keywords']);

		$closest = $return;
		foreach ($closest as $key => $value) {
			unset($closest[$key]['data']['keywords']);
			unset($closest[$key]['data']['pages']);
		}
		return $closest;

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
}