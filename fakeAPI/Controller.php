<?php
class Controller {
	protected $m;

	public function __construct() {
		$this->m = (new MongoClient())->rich;
	}
	
	protected function cursorToArray($cursor) {
		$res = [];
		foreach ($cursor as $doc) {
			$doc['_id'] = $doc['_id']->__toString();
			$res[] = $doc;
		}

		return $res;
	}

	protected function output($array) {
		echo json_encode($array);
	}

	protected function verifyArgs($args) {
		foreach($args as $arg) {
			if(!isset($_GET[$arg])) $this->error('Un argument est manquant : ' . $arg . '.');
		}
	}

	protected function error($text) {
		die($text);
	}
}