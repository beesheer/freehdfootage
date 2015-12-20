<?php
class Controller_Ajax extends Zend_Controller_Action
{
    const RESPONSE_CODE_OK = 200;
    const RESPONSE_UNKNOWN_SERVER_ERROR = 500;
    const RESPONSE_CODE_NOT_ALLOWED = 400;
    const ERROR_NONE = '';

    /**
     * Ajax only access
     *
     * @var boolean
     */
    protected $_ajaxOnly = false;

    /**
     * The response code to return.
     *
     * @var integer
     */
    protected $_responseCode;

    /**
     * The error message string to return.
     *
     * @var string
     */
    protected $_responseErrorString;

    /**
     * The message to display to the end user.
     *
     * @var string
     */
    protected $_displayMessage = false;

    /**
     * The array of response. Which will be JSON encoded before sent.
     *
     * @var array
     */
    protected $_responseArray = array();

    /**
     * Common init function. Checking session key, etc.
     */
    public function init()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();
        $this->_responseCode = self::RESPONSE_CODE_OK;
        $this->view->t = Zend_Registry::getInstance()->translate;

        if ($this->_ajaxOnly) {
            if (!$this->_request->isXmlHttpRequest()) {
                $this->_responseCode = self::RESPONSE_CODE_NOT_ALLOWED;
                $this->_sendAjaxResponse();
                exit(0);
            }
        }
    }

    /**
     * Send the response according to the current state.
     *
     * @return void Send the response and exit.
     */
    protected function _sendAjaxResponse()
    {

        $response = $this->getResponse();
        if (!isset($this->_responseArray['meta'])) {
            $this->_responseArray['meta'] = array(
                'code' => $this->_responseCode,
                'error' => $this->_responseErrorString ? $this->_responseErrorString : self::ERROR_NONE
            );
        }
        if ($this->_displayMessage) {
            $this->_responseArray['meta']['message'] = $this->_displayMessage;
        }
        $response->setHttpResponseCode($this->_responseCode)
            ->appendBody(Zend_Json::encode($this->_responseArray))
            ->sendResponse();
        exit(0);
    }
}
