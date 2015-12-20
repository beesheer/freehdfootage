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
                }
            }
            else
            {
                $form->populate($params);
            }
        }
        $this->view->form = $form;
    }
}