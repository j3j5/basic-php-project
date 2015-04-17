<?php

$parameters = array();

include(__DIR__ .  '/custom.routing.php');

// CLI execution
if(PHP_SAPI == 'cli') {
	$log->pushHandler(new StreamHandler('php://stdout', $minimum_log_level));
	if($argc >= 2) {
		foreach($arguments_structure AS $key => $component) {
			if(isset($argv[$key+1])) {
				$parameters[] = $argv[$key+1];
				${$component} = $argv[$key+1];
			}
		}
		// If there were more arguments on the CLI, add them together to the last component, the first 2 are script and controller.
		if(count($argv) + 2 > count($arguments_structure)) {
			$arguments = $argv;
			end($arguments_structure);
			${$arguments_structure[key($arguments_structure)]} = implode(' ', array_splice($arguments, 2));
		}

		if(!is_file(__DIR__ . DIRECTORY_SEPARATOR . "cli" . DIRECTORY_SEPARATOR . "$controller.php")) {
			$controller = '404';
		}
	} else {
		$controller = '404';
		$username = '';
	}
	$controller = "cli" . DIRECTORY_SEPARATOR ."$controller.php";
// WEBSERVER
} else {
	$log->pushHandler(new StreamHandler(dirname(__DIR__) . '/data/logs/general.log', $minimum_log_level));
	$uri_segments = FALSE;
	if(mb_strlen($_SERVER['REQUEST_URI']) > 1) { // Homepage has '/' as REQUEST_URI
		$uri = $_SERVER['REQUEST_URI'];
		// Remove GET parameters
		if(mb_strpos($uri, '?') !== FALSE){
			$uri = mb_substr($uri, 0, mb_strpos($uri, '?'));
		}
		$uri_segments = explode('/', $uri);
	}
	if(is_array($uri_segments)) {
		foreach($url_structure AS $key => $component) {
			if(isset($uri_segments[$key+1])) {
				$parameters[] = $argv[$key+1];
				${$component} = $uri_segments[$key+1];
			}
		}
		if(!is_file(__DIR__ . DIRECTORY_SEPARATOR . "controllers" . DIRECTORY_SEPARATOR . "$controller.php")) {
			$controller = '404';
		}
	} else {
		// Default controllers
		$controller = 'landing';
		$username = '';
	}
	$controller = "controllers" . DIRECTORY_SEPARATOR ."$controller.php";
}
