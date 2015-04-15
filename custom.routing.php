<?php

/**
 * Add here as many arguments as segments you want to be used on your URIs or as arguments to the
 * CLI runner.
 */


if(PHP_SAPI == 'cli') {
	$arguments_structure = array(
		'controller',
		'variable1',
	);
} else {
	// The url would be as follows http://yourserver.com/controller/variable1
	$url_structure = array(
		'controller',
		'variable1'
	);
}
