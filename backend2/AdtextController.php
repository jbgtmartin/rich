<?php
class AdtextController extends Controller
{
	public function getWebsites($id = null) {
		$query = [];
		if($id && strlen($id) == 24) $query['_id'] = new MongoId($id);
		$cursor = $this->m->websites->find($query); 

		foreach ($cursor as $website) {
			$this->output($website);
			echo '<br><br>';
		}
		
		$this->output($this->cursorToArray($cursor));
	}
};