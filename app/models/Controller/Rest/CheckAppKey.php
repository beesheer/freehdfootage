<?php
/**
 * Normal rest controller: only check session.
 */
class Controller_Rest_CheckAppKey extends Controller_Rest
{
    public function init()
    {
        parent::init();
        // Check valid app key
        $this->checkAppKey();
    }
}
