<?php
/**
 * Fuel is a fast, lightweight, community driven PHP5 framework.
 *
 * @package    Fuel
 * @version    1.7
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2013 Fuel Development Team
 * @link       http://fuelphp.com
 */

/**
 * Set error reporting and display errors settings.  You will want to change these when in production.
 */
error_reporting(-1);
ini_set('display_errors', 1);

/**
 * Website document root
 */
define('DOCROOT', __DIR__.DIRECTORY_SEPARATOR);

/**
 * Path to the application directory.
 */
define('APPPATH', realpath(__DIR__.'/../fuel/app/').DIRECTORY_SEPARATOR);

/**
 * Path to the default packages directory.
 */
define('PKGPATH', realpath(__DIR__.'/../fuel/packages/').DIRECTORY_SEPARATOR);

/**
 * The path to the framework core.
 */
define('COREPATH', realpath(__DIR__.'/../fuel/core/').DIRECTORY_SEPARATOR);

// Get the start time and memory for use later
defined('FUEL_START_TIME') or define('FUEL_START_TIME', microtime(true));
defined('FUEL_START_MEM') or define('FUEL_START_MEM', memory_get_usage());

// Boot the app
require APPPATH.'bootstrap.php';

// disable cli
\Fuel::$is_cli = getenv('DISABLE_CLI') ? false : \Fuel::$is_cli;

$http = new swoole_http_server('0.0.0.0', 80, SWOOLE_BASE);
$http->set([
    'log_level' => SWOOLE_LOG_TRACE,
    'trace_flags' => SWOOLE_TRACE_SERVER | SWOOLE_TRACE_HTTP2,
    'worker_num' => 10,
]);

$http->on('request', function ($request, $response) {
	$_GET = $request->get ?: array();
	$_POST = $request->post ?: array();
	$_COOKIE = $request->cookie ?: array();
	$_FILES = $request->files ?: array();
	$_SERVER = array_change_key_case($request->server, CASE_UPPER);

	try
	{
		$fuel_response = \Request::forge($request->server['request_uri'])->execute()->response();
	}
	catch (HttpNotFoundException $e)
	{
		$fuel_response = \Request::forge('_404_')->execute()->response();
	}

	$fuel_response->body((string) $fuel_response);
	// $fuel_response->send(true);
    $response->end($fuel_response->body());
});

$http->on('start', function (swoole_server $server){
    echo "swoole server start.\n";
});

$http->start();

