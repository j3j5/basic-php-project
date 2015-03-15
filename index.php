<?php

ob_start();

include('config.php');

include('routing.php');

require('functions.php');

// Load the controller
include(__DIR__ . DIRECTORY_SEPARATOR . $controller);

// Stop execution if there's no view
if(empty($view)) {
	exit;
}

// Don't try to load a non existant view
if(!is_file(__DIR__ . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR . "$view.php")) {
	$view = '404';
}

include(__DIR__ . DIRECTORY_SEPARATOR ."views" . DIRECTORY_SEPARATOR ."header.php");

include(__DIR__ . DIRECTORY_SEPARATOR ."views" . DIRECTORY_SEPARATOR ."$view.php");

include(__DIR__ . DIRECTORY_SEPARATOR ."views" . DIRECTORY_SEPARATOR ."footer.php");

ob_end_flush();
