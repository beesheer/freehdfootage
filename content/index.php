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
$application->bootstrap();

$front = Zend_Controller_Front::getInstance();
try {
    $front->dispatch();   // takes the place of run()
} catch (Zend_Exception $e) {
   // this is where the 404 goes
   header( 'HTTP/1.0 404 Not Found' );
   echo "<div style='float:center; width:1000px; margin:0 auto;'><img src='/client/images/landing_page.png'  alt='Everything is gonna be fine, please do not panic!' /></div>";
}
