<?php
function pr($array) {
	echo '<pre>';
	print_r($array);
	echo '</pre>';
}

$url_parts = array_filter(explode('/', $_GET['url_rwrite']));
unset($_GET['url_rwrite']);

$controller_name = ucfirst($url_parts[0]).'Controller';
$action_name = $url_parts[1];
array_shift($url_parts);
array_shift($url_parts);
include('Controller.php');
include($controller_name.'.php');
$controller = new $controller_name();

call_user_func_array(array($controller, $action_name), $url_parts);