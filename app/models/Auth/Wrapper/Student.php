<?php
class Auth_Wrapper_Student extends Zend_Auth
{
    /**
     * Login with username and password
     * for students.
     *
     * @param string $username
     * @param string $password
     * @param boolean $passwordHash
     *
     * @return boolean Whether login.
     *
     */
    public static function login($email, $password, $passwordHash = false)
    {
        // Need to regenerate session id
        Zend_Session::regenerateId();

        //Set up db table adaptor
        $authAdaptor = new Zend_Auth_Adapter_DbTable(Zend_Db_Table::getDefaultAdapter());
        $authAdaptor->setTableName('user');
        $authAdaptor->setIdentityColumn('email');
        $authAdaptor->setCredentialColumn('password');
        $authAdaptor->setIdentity($email);
        $authAdaptor->setCredential($passwordHash ? $password : md5($password));

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
        return true;
    }

    /**
     * Get student id in the session.
     *
     * @return integer
     */
    public static function getStudentId()
    {
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
            return false;
        }
        return isset($auth->getIdentity()->is_admin) ? $auth->getIdentity()->user_id : $auth->getIdentity()->id;
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

        $rememberKey = md5($identity->username . Zend_Registry::getInstance()->config->app->salt . Functions_Common::clientIp());

        //Remember in cookie for 30 days
        setcookie("remember_user", $identity->username, time() + 3600*24*30, '/');
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
        $username = $_COOKIE['remember_user'];
        $key = $_COOKIE['remember_key'];
        $rememberKey = md5($username . Zend_Registry::getInstance()->config->app->salt . Functions_Common::clientIp());
        if ($key != $rememberKey) {
            return false;
        }
        // Login
        $userRow = Repo_Student::getInstance()->getRow(array(
            array(
                'where' => 'username = ?',
                'bind' => $username
            )
        ));
        if (!$userRow) {
            return false;
        }
        return self::login($username, $userRow->password, true);
    }
}
