<?php
/**
 * Index page calls for the Bootstrap file and run
 *
 * @author beesheer
 * @version 1.0
 */
require_once '../app/Setup.inc.php';
// Bootstrap and run.
$application = new Zend_Application(APPLICATION_ENV, CONFIG_PATH . 'app.ini');
$application->bootstrap()->run();