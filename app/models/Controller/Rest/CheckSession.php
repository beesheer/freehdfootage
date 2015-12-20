<?php
/**
 * Normal rest controller: only check session.
 */
class Controller_Rest_CheckSession extends Controller_Rest
{
    /**
     * Init and check session key.
     */
    public function init()
    {
        parent::init();
        // Check valid session
        $this->checkSessionKey();
    }
}
