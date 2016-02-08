<?php
class First extends AppModel {
	//var $useDbConfig = 'mongo';

	var $mongoSchema = array(
		'name' => array('type'=>'string'),
	);

}