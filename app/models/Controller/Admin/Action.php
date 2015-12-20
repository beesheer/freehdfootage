<?php
class Controller_Admin_Action extends Controller_Action
{
    /**
     * Common init function.
     */
    public function init()
    {
        parent::init();
        $this->_helper->layout->setLayout('client-admin-default');
    }
}
