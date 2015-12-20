<?php
class Controller_Admin_Action extends Controller_Action
{
    const CLIENT_CONTEXT_SESSION = 'client_context';

    /**
     * Current client context.
     *
     * @var integer
     */
    protected $_currentClientId = 0;

    /**
     * Common init function.
     */
    public function init()
    {
        parent::init();
        $this->_helper->layout->setLayout('client-admin-default');

        $identity = Zend_Auth::getInstance()->getIdentity();

        // Set client context if it exists in session.
        $this->_currentClientId = $this->_getClientContext();
        $this->view->clientContext = new Object_Client($this->_currentClientId);
    }

    /**
     * Set client context into session.
     *
     * @param integer $clientId
     * @return void
     */
    protected function _setClientContext($clientId)
    {
        $clientContext = new Zend_Session_Namespace(self::CLIENT_CONTEXT_SESSION);
        $clientContext->clientId = $clientId;
        $this->_currentClientId = $clientId;
        $this->view->clientContext = new Object_Client($this->_currentClientId);
    }

    /**
     * Get client context from session.
     *
     * @return integer
     */
    protected function _getClientContext()
    {
        $clientContext = new Zend_Session_Namespace(self::CLIENT_CONTEXT_SESSION);
        $clientId = isset($clientContext->clientId) ? (int)$clientContext->clientId : 0;
        return $clientId;
    }
}
