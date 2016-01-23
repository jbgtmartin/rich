<?php
class KeywordsController extends Controller {
	public function add($site_id) {
		$this->verifyArgs(['word', 'ppc']);

		$query = ['_id' => new MongoId($site_id)];
		$keywords = $this->m->adwords->findOne($query)['keywords'];
		if(array_key_exists($_GET['word'], $keywords))
			$this->error('Ce mot clé existe déjà. Utilisez update.');
		else {
			$keywords[$_GET['word']] = [
				'ppc' => $_GET['ppc'],
				'stats' => [
				]
			];
			$this->m->adwords->update($query, ['keywords' => $keywords]);
		}
	}

	public function get($site_id, $keyword = null) {
		$this->generateStats($site_id);
		
		$query = ['_id' => new MongoId($site_id)];
		$cursor = $this->m->adwords->findOne($query); 

		if($keyword)
			$this->output($cursor['keywords'][$keyword]);
		else
			$this->output($cursor['keywords']);
	}

	public function delete($site_id) {
		$this->verifyArgs(['word']);
		$query = ['_id' => new MongoId($site_id)];
		$keywords = $this->m->adwords->findOne($query)['keywords'];
		if(!array_key_exists($_GET['word'], $keywords))
			$this->error('Ce mot clé n\'existe pas.');
		else {
			unset($keywords[$_GET['word']]);
			$this->m->adwords->update($query, ['keywords' => $keywords]);
		}
	}

	public function generateStats($site_id) {
		$query = ['_id' => new MongoId($site_id)];
		$keywords = $this->m->adwords->findOne($query)['keywords'];

		foreach($keywords as $keyword => $data) {
			if(empty($data['stats']))
				$last_date = date('Y-m-d', strtotime('-1 day'));
			else
				$last_date = array_reverse(array_keys($data['stats']))[0];

			while(strtotime($last_date) < strtotime('-1 day', strtotime(date('Y-m-d')))) {
				$last_date = date('Y-m-d', strtotime('+1 day', strtotime($last_date)));
				
				$clicks = 12;
				$views = 456;
				$interest = 0.30;
				$bounce = (1 - $interest) * 100; #TODO : décroissance initiale rapide
				$contacts = $clicks * $interest;
				$duration = $interest * 3 * 60;

				$keywords[$keyword]['stats'][$last_date] = [
					'views' => $views,
					'clicks' => $clicks,
					'bounce' => min(95, rand($bounce * 0.8, $bounce * 1.2)),
					'contact' => min($clicks, rand($contacts * 0.8, ceil($contacts * 1.2))),
					'duration' => rand($duration * 0.8, $duration * 1.2)
				];
			}
		}

		$this->m->adwords->update($query, ['keywords' => $keywords]);
	}
}