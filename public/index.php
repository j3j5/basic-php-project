<?php
// Force UTF-8 everywhere!!!
ini_set("default_charset", 'utf-8');
mb_internal_encoding('UTF-8');

ob_start();

/**
 * Initialize some variables
 */
$view_data = array();
$title = '';
$description = '';
$css_files = array('header' => array(), 'footer' => array());
$js_files = array('header' => array(), 'footer' => array());

include(dirname(__DIR__) . DIRECTORY_SEPARATOR . '/../config.php');

include(dirname(__DIR__) . DIRECTORY_SEPARATOR . '/../routing.php');

require(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'functions.php');

// Load the controller
$extra = include(APP_ROOT . DIRECTORY_SEPARATOR . $controller);

if(isset($extra['response_type'])){
	switch($extra['response_type']) {
		case 'json':
			header('Content-type: application/json');
			echo $extra['response'];
			break;
		case 'file':
			break;
		case 'html':
			// There should be a $view defined
		default:
			break;
	}
}

// Stop execution if there's no view
if(empty($view)) {
	exit;
}

// Don't try to load a non existant view
if(!is_file(APP_ROOT . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR . "$view.php")) {
	$view = '404';
}

if(isset($extra['no_header']) OR !$extra['no_header']) {
	include(APP_ROOT . DIRECTORY_SEPARATOR ."views" . DIRECTORY_SEPARATOR ."header.php");
}

extract($view_data);
include(APP_ROOT . DIRECTORY_SEPARATOR ."views" . DIRECTORY_SEPARATOR ."$view.php");
if(isset($extra['no_footer']) OR !$extra['no_footer']) {
	include(APP_ROOT . DIRECTORY_SEPARATOR ."views" . DIRECTORY_SEPARATOR ."footer.php");
}


ob_end_flush();
