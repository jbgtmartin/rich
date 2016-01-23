<?php
class WebsitesController extends Controller
{
	public function add() {
		$this->verifyArgs(['url', 'title', 'text1', 'text2', 'place', 'daily_budget']);

		$document = [
			'url' => $_GET['url'],
			'title' => $_GET['title'],
			'text1' => $_GET['text1'],
			'text2' => $_GET['text2'],
			'place' => $_GET['place'],
			'daily_budget' => $_GET['daily_budget']/100
			'keywords' => []
		];

		if(count($this->cursorToArray($this->m->adwords->find(['url' => $_GET['url']]))) >= 1)
			$this->error("Site existant.");
		else {
			$this->m->adwords->insert($document);
			$this->output(['id' => $document['_id']->__toString()]);
		}
	}

	public function get($id = null) {
		$query = [];
		if($id && strlen($id) == 24) $query['_id'] = new MongoId($id);
		$cursor = $this->m->adwords->find($query); 
		
		$this->output($this->cursorToArray($cursor));
	}
}