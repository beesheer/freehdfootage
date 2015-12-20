<?php
class Controller_Action extends Zend_Controller_Action
{
    /**
     * Common init function.
     */
    public function init()
    {
        // Get the flash messages
        $flashMessages = $this->_helper->getHelper('FlashMessenger');
        if (!empty($flashMessages)) {
            $this->view->flashMessages = $flashMessages;
        }

        // Include the specific javascript file if exists
        $jsAbsPath = PUBLIC_PATH . 'js' . DS
            . $this->_request->getModuleName() . DS
            . $this->_request->getControllerName() . DS
            . $this->_request->getActionName() . '.js';
        if (file_exists($jsAbsPath)) {
            $jsPubPath = '/js/'
                . $this->_request->getModuleName() . '/'
                . $this->_request->getControllerName() . '/'
                . $this->_request->getActionName() . '.js';
            $this->view->headScript()->appendFile($jsPubPath);
        }
    }

    /**
     * Redirect by identity.
     */
    protected function _redirectByIdentity()
    {
        if (Zend_Auth::getInstance()->hasIdentity()) {
            $identity = Zend_Auth::getInstance()->getIdentity();
            if (isset($identity->id)) {
                $this->_redirect('/admin');
            }
        } else {
            $this->_redirect('/');
        }
    }
}
