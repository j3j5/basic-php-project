<?php
require __DIR__ . '/vendor/autoload.php';

use Monolog\Logger;
use \Illuminate\Cache\CacheManager;
use \Illuminate\Filesystem\Filesystem;

define("APP_ROOT", __DIR__);

/**
 * Load here common styles and scripts for the page, you can add more on a per controller basis later
 */

// $css_files['header'][] = "//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css";
// $css_files['header'][] = "//netdna.bootstrapcdn.com/font-awesome/3.0.2/css/font-awesome.css";

// $js_files['footer'][] = "//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js";
// $js_files['footer'][] = "//netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js";
// $js_files['footer'][] = "/js/global.js";

$app = array(
	'config' => array(
		'cache.driver' => 'file',
		'cache.path' => __DIR__ . '/data/cache',
		'cache.prefix' => '_'
	),
	'files' => new Filesystem(),
);

$cacheManager = new CacheManager($app);
$cache = $cacheManager->driver();

$log = new Logger('general');
$minimum_log_level = Logger::WARNING;

/**
 * Config variables.
 * Add here any extra config variables you want to make
 * available to the controllers.
 *
 */

