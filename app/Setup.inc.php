<?php
/**
 * Set up environements.
 */

// Common constants.
defined('DS') || define('DS', DIRECTORY_SEPARATOR);
defined('PS') || define('PS', PATH_SEPARATOR);
defined('ROOT_PATH') || define('ROOT_PATH', realpath(dirname(dirname(__FILE__))) . DS);

// Define paths.
defined('APPLICATION_PATH') || define('APPLICATION_PATH', ROOT_PATH . 'app' . DS);
defined('PUBLIC_PATH') || define('PUBLIC_PATH', ROOT_PATH . 'content' . DS);
defined('DATA_PATH') || define('DATA_PATH', ROOT_PATH . 'data' . DS);
defined('LIBRARY_PATH') || define('LIBRARY_PATH', ROOT_PATH . 'library' . DS);
defined('CONFIG_PATH') || define('CONFIG_PATH', APPLICATION_PATH . 'configs' . DS);

// Set up includes.
set_include_path(
    LIBRARY_PATH . PATH_SEPARATOR .
	APPLICATION_PATH . 'models' . DS
);

// Define application environment.
$env = getenv('APPLICATION_ENV');

// In case $env is not set or mod_env is not enabled.
if (empty($env)) {
    $env = 'development';
}

// Temp solution to separate local host development and hosted development site
if ($env == 'development') {
    // Check URL to determine whether environment is staging
    $domain = $_SERVER['SERVER_NAME'];
    if ($domain == 'localhost' && isset($_SERVER['HTTP_X_FORWARDED_SERVER'])) {
        $domain = $_SERVER['HTTP_X_FORWARDED_SERVER'];
    }

    if ($domain == 'localhost' || $domain == 'stratus') {
        $env = 'local';
    }
}

defined('APPLICATION_ENV') || define('APPLICATION_ENV', $env);

if ($env == 'development') {
    error_reporting(E_ALL ^ E_STRICT); //This should be determined by $config->appStage == 'dev'
    ini_set('display_errors',true); //This should be determined by $config too
}

// Set default date timezone
if (function_exists('date_default_timezone_set')) {
    date_default_timezone_set('America/Toronto');
}

// Some php settings
ini_set('magic_quotes_gpc', 0);
ini_set('error_log', DATA_PATH . 'log' . DS. 'php_error.log');
ini_set('log_errors', '1');

// Autoload
require_once 'Zend/Loader.php';
@Zend_Loader::registerAutoload();
