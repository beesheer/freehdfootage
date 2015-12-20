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
    public static function login($email, $password)
    {
        // Need to regenerate session id
        Zend_Session::regenerateId();
        //Authentication attempt
        $auth = Zend_Auth::getInstance();
        $authAdaptor = new Zend_Auth_Adapter_DbTable(Zend_Db_Table::getDefaultAdapter());
        $authAdaptor->setTableName('user');
        $authAdaptor->setIdentityColumn('email');
        $authAdaptor->setCredentialColumn('password');
        $authAdaptor->setIdentity($email);
        if(strlen($password) < 32) {
            $password = md5($password);
        }
        $authAdaptor->setCredential($password);
        $result = $auth->authenticate($authAdaptor);
        if($result->isValid()) {
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
}
