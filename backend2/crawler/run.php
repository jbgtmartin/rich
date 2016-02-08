<?php
require '../vendor/autoload.php';

// Initiate crawl
$crawler = new \Arachnid\Crawler('http://www.compagnieboisexotiques.com', 3);
$crawler->traverse();

// Get link data
$links = $crawler->getLinks();