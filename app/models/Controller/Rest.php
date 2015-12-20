<?php
class Controller_Rest extends Zend_Rest_Controller
{
    const RESPONSE_CODE_OK = 200;
    const RESPONSE_CODE_BAD_REQUEST = 400;
    const RESPONSE_CODE_NOT_AUTHORIZED = 401;
    const RESPONSE_CODE_NOT_ALLOWED = 403;
    const RESPONSE_CODE_NOT_FOUND = 404;
    const RESPONSE_CODE_METHOD_NOT_ALLOWED = 405;
    const RESPONSE_UNKNOWN_SERVER_ERROR = 500;
    const ERROR_NONE = 'none';
    const ERROR_SESSION_EXPIRED = 'session_expired';
    const ERROR_USER_NOT_FOUND = 'user_not_found';
    const ERROR_INVALID_PARAMS = 'invalid_parameters';
    const ERROR_INVALID_TOKEN = 'not_authenticated';
    const ERROR_INVALID_CREDENTIALS ='invalid_credentials';
    const ERROR_INVALID_SESSION ='invalid_session';

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
     * The user object.
     *
     * @var Object_User
     */
    protected $_user = null;

    /**
     * Common init function. Checking session key, etc.
     */
    public function init()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();
        $this->_responseCode = self::RESPONSE_CODE_OK;
    }

    /**
     * The index action handles index/list requests; it should respond with a
     * list of the requested resources.
     */
    public function indexAction()
    {
        $this->_sendRestResponse();
    }

    /**
     * The head action handles HEAD requests and receives an 'id' parameter; it
     * should respond with the server resource state of the resource identified
     * by the 'id' value.
     */
    public function headAction()
    {
        $this->_sendRestResponse();
    }

    /**
     * The get action handles GET requests and receives an 'id' parameter; it
     * should respond with the server resource state of the resource identified
     * by the 'id' value.
     */
    public function getAction()
    {
        $this->_sendRestResponse();
    }

    /**
     * The post action handles POST requests; it should accept and digest a
     * POSTed resource representation and persist the resource state.
     */
    public function postAction()
    {
        $this->_sendRestResponse();
    }

    /**
     * The put action handles PUT requests and receives an 'id' parameter; it
     * should update the server resource state of the resource identified by
     * the 'id' value.
     */
    public function putAction()
    {
        $this->_sendRestResponse();
    }

    /**
     * The delete action handles DELETE requests and receives an 'id'
     * parameter; it should update the server resource state of the resource
     * identified by the 'id' value.
     */
    public function deleteAction()
    {
        $this->_sendRestResponse();
    }

    /**
     * Send the response according to the current state.
     *
     * @return void Send the response and exit.
     */
    protected function _sendRestResponse()
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
        $body = Zend_Json::encode($this->_responseArray);

        // Always set 200 in the header
        $response->setHttpResponseCode(self::RESPONSE_CODE_OK)
        //        ->setHeader("Access-Control-Allow-Origin","*")
            ->appendBody($body)
            ->sendResponse();

        exit(0);
    }

    /**
     * Check the app key.
     *
     * @return boolean | void Whether app key is valid.
     */
    public function checkAppKey()
    {
        // $appKey = trim($this->_request->getParam('appKey'));
        // Currently we will allow all.
        return true;
    }

    /**
     * Check the session key.
     *
     * @return boolean | void Session key valid or not.
     */
    public function checkSessionKey()
    {
        $invalidSession = false;
        $userId = $this->_request->getParam('user');
        $sessionKey = $this->_request->getParam('session');
        $result = array('result' => '','key' => '');
        if (!$userId || !$sessionKey)
        {
            $invalidSession = true;
        }
        else
        {
            try
            {
                $result = Repo_UserApiSessionKeys::getInstance()->updateSessionKey($userId, $sessionKey);
            }
            catch (Exception $e)
            {
                $invalidSession = true;
                $this->_responseErrorString = self::ERROR_SESSION_EXPIRED;
            }
            if (!$result['result'] || $result['key'] != $sessionKey)
            {
                $invalidSession = true;
                $this->_responseErrorString = self::ERROR_SESSION_EXPIRED;
            }
        }
        if ($invalidSession)
        {
            $this->_responseCode = self::RESPONSE_CODE_NOT_ALLOWED;
            $this->_sendRestResponse();
        }
        $this->_user = new Object_User($userId);
       return true;
    }

    /**
     * Get session user/client info sololy based on session key.
     *
     * @return array Array of user and client array.
     */
    public function getSessionInfo()
    {
        $sessionKey = $this->_request->getParam('session');
        $keyRow = Repo_UserApiSessionKeys::getInstance()->getRow(array(
            array(
                'where' => 'session_key = ?',
                'bind' => $sessionKey
            )
        ));
        if (!$keyRow || time() > ($keyRow->modified + $keyRow->lifetime)) {
            return false;
        }
        $user = new Object_User($keyRow->user_id);
        $client = new Object_Client($user->client_id);
        return array(
            'user' => $user->getBasicInfo(),
            'client' => $client->getDataArray()
        );
    }


    /**
     * This is method authAccess
     * if the token is not valid it will return 401
     * @return Json
     *
     */
    protected function authAccess()
    {
        //woud be better to provide the token in the header not as get
        //$request = new Zend_Controller_Request_Http();
        //$tokenApi = $request->getHeader('token');
        /**
         * @todo Need to implement some method of caching for token
         */
        $tokenApi = trim($this->getRequest()->getParam('token'));
        $api = Repo_PublicAuthenticationToken::getInstance()->getClientIdByToken($tokenApi);
        if($api->count() == 0)
        {
            $this->_responseCode = 401;
            $this->_responseErrorString= self::ERROR_INVALID_TOKEN;
            $this->_sendRestResponse();
        }
        return (int)$api->getRow(0)->client_id;
    }
}
