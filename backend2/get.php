<?php
$mongo = new MongoClient();
$collection = $mongo->rich->first;

$cursor = $collection->find($fruitQuery);
foreach ($cursor as $doc) {
    var_dump($doc);
}
?>