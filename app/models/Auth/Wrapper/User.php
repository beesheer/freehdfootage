<?php
class Auth_Wrapper_User extends Zend_Auth
{
    public static $userPermissions = array();

    /**
     * Login with email and password.
     *
     * @param string $email
     * @param string $password
     * @param boolean $checkPassword
     *
     * @return boolean Whether login.
     *
     */
    public static function login($email, $password, $checkPassword = true)
    {
        // Need to regenerate session id
        Zend_Session::regenerateId();

        //Authentication attempt
        $auth = Zend_Auth::getInstance();
        $result = self::verifyAuth($email, $password, $checkPassword);
        if($result !== false) {
            //Save the object without password
            $auth->getStorage()->write($result);
            return true;
        }
        return false;
    }
    
    public static function loginWithSub($sub)
    {
        // Need to regenerate session id
        Zend_Session::regenerateId();
        $auth = Zend_Auth::getInstance();
        $userOpenId = Repo_UserOpenId::getInstance()->checkSubForAuth($sub);
        if($userOpenId) 
        {
            //Save the object without password
            $auth->getStorage()->write($userOpenId);
            return true;
        }
        return false;
    }

    /**
     * Login with email and client id.
     *
     * @param string $email
     * @param string $clientid
     *
     * @return boolean Whether login.
     *
     */
    public static function loginWithClientId($email, $clientId)
    {
        // Need to regenerate session id
        Zend_Session::regenerateId();

        //Set up db table adaptor
        $authAdaptor = new Zend_Auth_Adapter_DbTable(Zend_Db_Table::getDefaultAdapter());
        $authAdaptor->setTableName('user');
        $authAdaptor->setIdentityColumn('email');
        $authAdaptor->setCredentialColumn('client_id');
        $authAdaptor->setIdentity($email);
        $authAdaptor->setCredential( $clientId );

        //Authentication attempt
        $auth = Zend_Auth::getInstance();
        $result = $auth->authenticate($authAdaptor);
        if($result->isValid()) {
            //Save the object without password
            $auth->getStorage()->write($authAdaptor->getResultRowObject(null, array('password')));
            return true;
        }
        return false;
    }

    /**
     * Logout and clear session.
     *
     * @return boolean Whether logout.
     *
     */
    public static function logout()
    {
        $auth = Zend_Auth::getInstance();
        $auth->clearIdentity();

        // Clear remember cookie
        if (isset($_COOKIE['remember_key'])) {
            setcookie("remember_user", '', time() - 3600*24*30, '/');
            setcookie("remember_key", '', time() - 3600*24*30, '/');
        }

        return true;
    }

    /**
     * Get user id in the session.
     *
     * @return integer
     */
    public static function getUserId()
    {
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
            return false;
        }
        return $auth->getIdentity()->id;
    }

    /**
     * Set up cookie for remember me.
     *
     * @return boolean
     */
    public static function rememberSetup()
    {
        if (! ($identity = Zend_Auth::getInstance()->getIdentity())) {
            return false;
        }

        $rememberKey = md5($identity->email . Zend_Registry::getInstance()->config->app->salt . Functions_Common::clientIp());

        //Remember in cookie for 30 days
        setcookie("remember_user", $identity->email, time() + 3600*24*30, '/');
        setcookie("remember_key", $rememberKey, time() + 3600*24*30, '/');
        return true;
    }

    /**
     * Based on the remember cookie, login the user
     *
     * @return boolean
     */
    public static function loginFromRemember()
    {
        if (!isset($_COOKIE['remember_key']) || !isset($_COOKIE['remember_user'])) {
            return false;
        }
        // Check whether the key is correct
        $email = $_COOKIE['remember_user'];
        $key = $_COOKIE['remember_key'];
        $rememberKey = md5($email . Zend_Registry::getInstance()->config->app->salt . Functions_Common::clientIp());
        if ($key != $rememberKey) {
            return false;
        }
        // Login
        $userRow = Repo_User::getInstance()->getRow(array(
            array(
                'where' => 'email = ?',
                'bind' => $email
            )
        ));
        if (!$userRow) {
            return false;
        }
        return self::login($email, $userRow->password, false);
    }

    /**
     * Check user has permission or not.
     *
     * @param mixed $uri
     */
    public static function userHasPermission($uri)
    {
        if (! ($identity = Zend_Auth::getInstance()->getIdentity())) {
            return false;
        }
        $user = new Object_User($identity->id);
        if ($user->isUserSuperAdmin()) {
            return true;
        }
        if (empty(self::$userPermissions)) {
            $permissions = $user->getPermissions();
        }
        foreach (self::$userPermissions as $_p) {
            if (stripos($uri, $_p['permission_name']) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Is user super admin.
     *
     * @return boolean
     */
    public static function isUserSuperAdmin()
    {
        if (! ($identity = Zend_Auth::getInstance()->getIdentity())) {
            return false;
        }
        $user = new Object_User($identity->id);
        if ($user->isUserSuperAdmin()) {
            return true;
        }
        return false;
    }

    /**
     * Get password hash to store in the database
     *
     * @param mixed $password
     */
    public static function getPasswordHash($password)
    {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        if (!$hash) {
            throw new Exception('Can not create password hash!');
        }
        return $hash;
    }
   
    
    /**
     * Authenticate and return an object if authenticated.
     *
     * @param mixed $email
     * @param mixed $password
     * @return mixed
     */
    public static function verifyAuth($email, $password, $checkPassword = true)
    {
        $userRow = Repo_User::getInstance()->getRow(array(
            array(
                'where' => 'email = ?',
                'bind' => $email
            )
        ), true);
        if (!$userRow) {
            return false;
        }
        $verified = false;
        if ($checkPassword) {
            // Make sure the password hash the correct length
            if ($userRow->password_converted !== 1 && strlen($userRow->password) == 32) {
                if ($userRow->password == md5($password)) {
                    // Verified using md5 and convert it
                    $userRow->password = self::getPasswordHash($password);
                    $userRow->password_converted = 1;
                    $userRow->save();
                    $verified = true;
                } else {
                    // Not verified.
                    return false;
                }
            } else {
                if (password_verify($password, $userRow->password)) {
                    $verified = true;
                } else {
                    return false;
                }
            }
        } else {
            $verified = true;
        }

        if (!$verified) {
            return false;
        }
        $user = new Object_User($userRow->id);
        $userData = $user->getDataArray();
        $userObj = new stdClass;
        foreach ($userData as $_k => $_v) {
            if ($_k !== 'password') {
                $userObj->$_k = $_v;
            }
        }
        return $userObj;
    }
}
