<?php
/**
 * The login controller.
 */
class LoginController extends Controller_Action
{
    /**
     * User login.
     *
     */
    public function indexAction()
    {
        // Check whether logged in.
        $identity = Zend_Auth::getInstance()->getIdentity();
        if ($identity) 
        {
            $this->_redirectByIdentity();
        }
        $form = new Form_Client_Login();
        if ($this->_request->isPost()) 
        {
            $params = $this->_request->getPost();
            if ($form->isValid($params)) 
            {
                $email = $form->getValue('email');
                $password = $form->getValue('password');
                if (($result = Auth_Wrapper_User::login($email, $password)) === false) 
                {
                    $this->view->errorMessage = $result !== false ? $result : 'Wrong email or password.';
                } 
                else 
                {
                    $this->_redirectByIdentity();

//                    $redirect = '/';
//                    $session = new Zend_Session_Namespace('login-request');
//                    if ($session->uri) {
//                        $redirect = $session->uri;
//                        $this->_redirect($redirect);
//                    }
                    }
            } 
            else 
            {
                $form->populate($params);
            }
        } 
        else 
        {
            // Save the request URI
            $session = new Zend_Session_Namespace('login-request');
            $requestUri = $this->_request->getRequestUri();
            if (stripos($requestUri, 'login') === false) 
            {
                $session->uri = $requestUri;
            } 
            else 
            {
                $session->uri = false;
            }
        }
        $this->view->form = $form;
    }

    /**
     * Admin login.
     */
    public function adminAction()
    {
        // Check whether logged in.
        $identity = Zend_Auth::getInstance()->getIdentity();
        if ($identity) {
            $this->_redirectByIdentity();
        }

        $form = new Form_Admin_Login(null, array('addHash'=>true));
        if ($this->_request->isPost()) {
            $params = $this->_request->getPost();
            if ($form->isValid($params)) {
                $email = $form->getValue('email');
                $password = $form->getValue('password');
                if (($result = Auth_Wrapper_User::login($email, $password)) === false) {
                    $this->view->errorMessage = $result !== false ? $result : 'Wrong email or password.';
                } else {
                    $remember = $form->getValue('remember');
                    if ($remember) {
                        Auth_Wrapper_User::rememberSetup();
                    }
                    $redirect = '/admin';
                    $session = new Zend_Session_Namespace('login-request');
                    if ($session->uri) {
                        $redirect = $session->uri;
                    }
                    $this->_redirect($redirect);
                }
            } else {
                $form->populate($params);
            }
        } else {
            // Save the request URI
            $requestUri = $this->_request->getRequestUri();
            $session = new Zend_Session_Namespace('login-request');
            if (stripos($requestUri, 'login') === false) {
                $session->uri = $requestUri;
            } else {
                $session->uri = false;
            }
        }
        $this->view->form = $form;
    }

    /**
     * Student login.
     */
    public function studentAction()
    {

        $layout = 'student-default';

        //client, title, quiz number can be passed
        $studentSession = Manager_Session_Student::getInstance();
        $studentSession->saveRequestValues($this->_request,$this->view);
        $studentSession->getStudentSession();
        if( $studentSession->hasLayout ) {
            $layout  = $studentSession->studentSessionDetails->baseLayout;
        }

        Zend_Layout::getMvcInstance()->setLayout($layout);
        // Check whether logged in.
        $identity = Zend_Auth::getInstance()->getIdentity();

        if ($identity) {
            $this->_redirectByIdentity();
        }

        $form = new Form_Student_Login(null, array('addHash'=>true));
        if ($this->_request->isPost()) {
            $params = $this->_request->getPost();

            if ($form->isValid($params)) {

                $username = $form->getValue('username');
                $password = $form->getValue('password');

                if (($result = Auth_Wrapper_User::login($username, $password)) === false) {
                    $this->view->errorMessage = $result !== false ? $result : 'Wrong username or password.';
                } else {

                    //save session info
                    $studentSession->setStudentId();
                    $remember = $form->getValue('remember');

                    if ($remember) {
                        Auth_Wrapper_Student::rememberSetup();
                    }
                    $this->_redirectByIdentity();
                }
            } else {
                $form->populate($params);
            }
        }
        $this->view->form = $form;
    }

    /**
     * Forgot password. Allow user to enter email to send reset password email.
     *
     */
    public function forgotPasswordAction()
    {
        // Check whether logged in.
        $identity = Zend_Auth::getInstance()->getIdentity();
        if ($identity) {
            $this->_redirectByIdentity();
        }
        $form = new Form_Client_Login_ForgotPassword();
        if ($this->_request->isPost()) {
            $params = $this->_request->getPost();
            if ($form->isValid($params)) {
                $email = $form->getValue('email');
                $this->view->studentData = $form->getValue('studentData');
                $userId = Repo_User::getInstance()->emailExists($email);
                if ($userId === false) {
                    //$errorMessage = 'An email has been sent if it exists. Please follow the link there.';
                    //$this->view->errorMessage = $errorMessage;
                    $this->_helper->getHelper('FlashMessenger')->addMessage("An email has been sent if it exists.<br />Please follow the link there.");
                } else {
                    $user = new Object_User($userId);
                    // Send forgot password email
                    if (!$user->resetPasswordRequest( $form )) {
                        $this->view->errorMessage = 'Failed to send reset password email';
                    } else {
                        // Redirect with flash message
                        $append = (!$form->getValue('studentData')) ? "" : "?studentData=".$form->getValue('studentData');
                        $this->_helper->getHelper('FlashMessenger')->addMessage("An email has been sent if it exists.<br />Please follow the link there.");
                        $this->_redirect('/login/forgot-password'.$append);
                    }
                }
            } else {
                $form->populate($params);
            }
        }
        if($this->_request->studentData) {
            $this->view->studentData = $this->_request->studentData;
        }
        $username = $form->getValue('username');
        $this->view->form = $form;
    }

    /**
     * Reset password.
     *
     */
    public function resetPasswordAction()
    {
        // Check whether logged in.
        $identity = Zend_Auth::getInstance()->getIdentity();
        if ($identity) {
            $this->_redirectByIdentity();
        }

        // Check the key is valid
        $resetKey = $this->_request->getParam('key');
        $userId = Repo_User::getInstance()->resetKeyUser($resetKey);

        if ($userId === false) {
            $redirectURL = '/';
            if( $this->_request->studentData ) {
                $this->_helper->getHelper('FlashMessenger')->addMessage("Invalid user.");
                $redirectURL = "/login/forgot-password?studentData=resetPasswordInvalidUser";
            }
            $this->_redirect( $redirectURL );
        }

        $form = new Form_Client_Login_ResetPassword();

        if ($this->_request->isPost()) {
            $params = $this->_request->getPost();
            if ($form->isValid($params)) {
                $newPassword = $form->getValue('password');
                $passwordConfirm = $form->getValue('password2');
                if ($newPassword != $passwordConfirm) {
                    $this->view->errorMessage = 'Passwords do not match. Please check';
                } else {
                    $user = new Object_User($userId);
                    $user->resetPassword($newPassword);
                    $this->_helper->getHelper('FlashMessenger')->addMessage("You have successfully changed your password.");
                    $redirectURL = (!$form->getValue('studentData')) ? "/" : "/login/forgot-password?studentData=passwordResetSuccess";
                    $this->_redirect( $redirectURL );
                }
            }
        }
        if($this->_request->studentData) {
            $this->view->studentData = $this->_request->studentData;
        }
        $this->view->form = $form;
    }

    /**
     * Register a student.
     *  Added September 18
     *
     */
    public function registerAction()
    {

        //Check whether logged in.
        $identity = Zend_Auth::getInstance()->getIdentity();
        if ($identity) {
            $this->_redirectByIdentity();
        }

        $layout = 'client-default';

        //client, title, quiz number can be passed
        $studentSession = Manager_Session_Student::getInstance();
        $studentSession->saveRequestValues($this->_request,$this->view);
        if( $studentSession->hasLayout ) {
            $layout  = $studentSession->studentSessionDetails->baseLayout . '-register';
        }

        Zend_Layout::getMvcInstance()->setLayout($layout);

        $form = new Form_Student_Register(null, array('addHash'=>true));

        if ($this->_request->isPost()) {

            $params = $this->_request->getPost();
            if ($form->isValid($params)) {
                // NEW
                // Create a new student
                // addNew($email, $password, $UDID, $firstname, $surname, $roleId, $clientId)
                $userId = Repo_User::getInstance()->addNew(
                    $form->getValue('email'),
                    $form->getValue('password'),
                    0,
                    $form->getValue('first'),
                    $form->getValue('last'),
                    $form->getValue('roleid'),
                    $form->getValue('clientid')
                );
                // add supplementary student information
                $studentId = Repo_Student::getInstance()->addNew(
                    $userId,
                    $form->getValue('phone'),
                    $form->getValue('clinic'),
                    $form->getValue('licensenumber'),
                    $form->getValue('stateoflicensure'),
                    $form->getValue('address1'),
                    $form->getValue('address2'),
                    $form->getValue('city'),
                    $form->getValue('provstate'),
                    $form->getValue('country'),
                    $form->getValue('postalcode')
                );
                if (($result = Auth_Wrapper_User::login($form->getValue('email'), $form->getValue('password'))) !== false) {
                    //save session info
                    $studentSession->setStudentId();
                    $this->_redirectByIdentity();
                }
                // Redirect to student page
                //$this->_redirect('/student/id/' . $userId);
            } else {
                $form->populate($params);
            }
        }

        $this->view->form = $form;
        //exit;
    }

}