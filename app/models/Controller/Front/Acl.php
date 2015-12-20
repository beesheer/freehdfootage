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
            Auth_Wrapper_User::loginFromRemember();
        }

        $auth = Zend_Auth::getInstance();

        // Set up role based on identity.
        $role = Acl_Client::ROLE_TYPE_GUEST;

        $defaultLoginController = 'login';
        $defaultLoginAction = 'index';
        if ($resource == 'admin') {
            $defaultLoginAction = 'admin';
        }

        if ($action == 'student' || $action == 'register') {
            //policy to permit cookies from iframes in IE
            $this->getResponse()->setRawHeader('P3P:CP="StratusStudentPolicy"');
        }

		if ($resource == 'student') {

            $defaultLoginAction = 'student';
            $controller = $request->getControllerName();
            if($controller == 'register') {
                //register navigation clicked
                $defaultLoginController = 'register';
                $defaultLoginAction = 'index';
            }
        }

        if ($auth->hasIdentity()) {
            $identity  = $auth->getIdentity();
            $user = new Object_User($identity->id);
            $roleIds = $user->getRoleIds();
            foreach ($roleIds as $_roleId) {
                $userRole = new Object_Role($_roleId);
                if (isset($identity->id)) {
                    if ($_roleId == Repo_Role::$roleIds[Repo_Role::ROLE_SUPERADMIN]
                        || $_roleId == Repo_Role::$roleIds[Repo_Role::ROLE_ADMIN]) {
                        $role = Acl_Client::ROLE_TYPE_ADMIN;
                        break;
                    } else if ($userRole->name == Repo_Role::ROLE_STUDENT) {
                        $role = Acl_Client::ROLE_TYPE_STUDENT;
                        break;
                    } else {
                        $role = Acl_Client::ROLE_TYPE_USER;
                    }
                }
            }
        } else if($this->getRequest()->getModuleName() != 'api'
            && $this->getRequest()->getControllerName() != 'login'
            && $this->getRequest()->getControllerName() != 'url'
            && $this->getRequest()->getActionName() != 'auth'
            && $this->getRequest()->getControllerName() != 'promote'
            && $this->getRequest()->getControllerName() != 'public-api') {
            $r = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
            $r->gotoUrl('/login')->redirectAndExit();
        }

        // We auto log out admin if the admin is trying to access user.
        // To access the default client area, user needs to login as a user
        /*if ($role == Acl_Client::ROLE_TYPE_ADMIN && $resource == 'client') {
            Auth_Wrapper_User::logout();
            $role = Acl_Client::ROLE_TYPE_GUEST;
        }*/

        // We auto log out user if the user is trying to access admin
        if ($role == Acl_Client::ROLE_TYPE_USER && $resource == 'admin') {
            Auth_Wrapper_User::logout();
            $role = Acl_Client::ROLE_TYPE_GUEST;
        }

		// We auto log out student if the student is trying to access admin or client
        if (($role == Acl_Client::ROLE_TYPE_STUDENT && $resource == 'admin') || ($role == Acl_Client::ROLE_TYPE_STUDENT && $resource == 'client')) {
            Auth_Wrapper_Student::logout();
            $role = Acl_Client::ROLE_TYPE_GUEST;
        }
        // Determine the resoure of the request
        if (!$this->_acl->has($resource)) {
            $resource = null;
        }

        // Acl access check
        if (!$this->_acl->isAllowed($role,$resource)){
            if ($defaultLoginController) {
                $request->setModuleName('default');
                $request->setControllerName($defaultLoginController);
                $request->setActionName($defaultLoginAction);
            } else {
                die ('No access.');
            }
        } else {
            // Enforce role/permission
            if (isset($identity) && isset($identity->id) && $identity->role_id != Repo_Role::$roleIds[Repo_Role::ROLE_SUPERADMIN]) {
                // SuperAdmin is allowed to do anything
                // Other role should run pass the role/permissions
                $user = new Object_User($identity->id);
                $permissions = $user->getPermissions();
                Auth_Wrapper_User::$userPermissions = $permissions;
                $requestUri = $_SERVER['REQUEST_URI'];
                $allowed = false;
                // All the ajax/api type request, we explicitely allow it.
                if ($requestUri == '/logout'
                    || stripos($requestUri, '/ajax') !== false
                    || stripos($requestUri, '/promote') === 0
                    || stripos($requestUri, '/api') === 0
                    || stripos($requestUri, '/public-api') === 0
                    || stripos($requestUri, '/login') === 0
                    || stripos($requestUri, '/url/') === 0
                    || ($request->getControllerName() == 'index' && $request->getActionName() == 'index')
                ){
                    $allowed = true;
                } else {
                    foreach ($permissions as $_p) {
                        if (stripos($requestUri, $_p['permission_name']) !== false) {
                            $allowed = true;
                            break;
                        }
                    }
                }
                if (!$allowed) {
                    $r = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
                    if ($userRole->name == Repo_Role::ROLE_ADMIN) {
                        $r->gotoUrl('/admin')->redirectAndExit();
                    } elseif ($userRole->name == Repo_Role::ROLE_STUDENT){
                        $request->setControllerName('logout');
                        $request->setActionName('student');
                        //$r->gotoUrl('/student')->redirectAndExit();
                    } else {
                        $r->gotoUrl('/')->redirectAndExit();
                    }
                }
            }
        }

        $request->setDispatched(true);
    }
}