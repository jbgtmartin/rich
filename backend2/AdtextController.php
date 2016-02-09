<?php
class AdTextController extends Controller
{
	public function getWebsites($id = null) {
		return parent::getWebsites($id)[0];
	}

	public function addAd($id) {
		$title = $_GET['title'];
		$desc = array($_GET['desc1'], $_GET['desc2']);
		$fiability = isset($_GET['fiability']) ? $_GET['fiability'] : 0.8;
		$clicks = isset($_GET['clicks']) ? $_GET['clicks'] : 0;
		
		$ad = array(
			'title' => $title,
			'desc' => $desc,
			'fiability' => $fiability,
			'clicks' => $clicks,
			);
		$this->appendAdd($ad, $this->getWebsites($id));
		pr($ad);
		pr($this->getWebsites($id));
	}

	public function removeAds($id) {
		$this->m->websites->update(array('_id' => new MongoId($id)), array('$set' => array('ads' => array())));
		pr($this->getWebsites($id));		
	}

	public function appendClosestAdd($id) {

		$website = $this->getWebsites($id);

		$closest_ad = $this->getClosestAdd($website, 50);
		if($closest_ad == false)
			return 'No close ad found';

		$closest_ad['title'] = ucfirst($website['type']) . ' à ' . ucfirst($website['place']);

		$closest_ad['fiability'] *= 0.7;
		$closest_ad['clicks'] = 0;
		$this->appendAdd($closest_ad, $website);

		pr($closest_ad);
		pr($website);
		return $closest_ad;

	}

	private function getClosestAdd($website, $min_clicks) {
		
		$id = $website['_id'];
		$closest_websites = $this->closestWebsites($id);

		$best_ad;
		$found = false;

		// On parcourt tous les voisins
		foreach ($closest_websites as $neighbor) {
			$neighbor = $neighbor['data'];

			if($neighbor['_id'] == new MongoId($id) || !isset($neighbor['ads']))
				continue;

			$neighbor_ads = $neighbor['ads'];

			// On cherche la meilleure Add
			$max_fiability = 0;
			$best_ad_index;
			foreach ($neighbor_ads as $k => $ad) {
				$fiability = $ad['fiability'];
				if($ad['clicks'] >= $min_clicks && $fiability > $max_fiability) {
					$max_fiability = $fiability;
					$best_ad_index = $k;
				}
			}
			// Si on a trouvé une Add on s'arrête
			if($max_fiability > 0) {
				$best_ad = $neighbor_ads[$best_ad_index];
				$found = true;
				break;
			}
			
		}

		return $found ? $best_ad : false;

	}

	private function appendAdd($ad, $website) {

		if(!isset($ad))
			return 'No valid ad.';

		if(isset($website['ads']))
			$ads = $website['ads'];
		else
			$ads = array();

		if(!in_array($ad, $ads)) {
			array_push($ads, $ad);
			$document = array('ads' => $ads);
			$this->m->websites->update(array('_id' => new MongoId($website['_id'])), array('$set' => $document));
		}

	}
};

