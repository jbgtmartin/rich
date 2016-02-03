<?php
function pr($array) {
	echo '<pre>';
	print_r($array);
	echo '</pre>';
}


$controller_name = ucfirst('websites').'Controller';
$action_name = 'massivedl';
include('Controller.php');
include($controller_name.'.php');
$controller = new $controller_name();

call_user_func_array(array($controller, $action_name), []);