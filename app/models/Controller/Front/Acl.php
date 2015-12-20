<?php
/**
 * This front controller plugin will be used to check the auth and acl
 * based on the REQUEST_AREA and the auth and the request.
 */
class Controller_Front_Acl extends Zend_Controller_Plugin_Abstract
{
    /**
     * Acl object.
     *
     * @var Zend_Acl
     */
    protected $_acl = null;

    /**
     * construct function
     */
    public function __construct(Zend_Acl $acl)
    {
        $this->_acl = $acl;
    }

    /**
     * Pre-dispatch, check acl.
     *
     * @param Zend_Controller_Request_Abstract $request
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $resource = $request->getModuleName();
        $controller = $request->getControllerName();
        $action = $request->getActionName();

        // Remember me for admin
        if ($resource == 'admin' && !Zend_Auth::getInstance()->hasIdentity()) {
            $r = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
            $r->gotoUrl('/login')->redirectAndExit();
        }

        $request->setDispatched(true);
    }
}