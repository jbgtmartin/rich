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
}