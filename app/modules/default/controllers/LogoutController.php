<?php
/**
 * The logout controller.
 */
class LogoutController extends Zend_Controller_Action
{
    /**
     * User logout.
     */
    public function indexAction()
    {
        if (Auth_Wrapper_User::logout()) {
            $this->_redirect('/login');
        }
    }

    /**
     * Admin logout.
     */
    public function adminAction()
    {
        if (Auth_Wrapper_User::logout()) {
            $this->_redirect('/admin');
        }
    }

    /**
     * Student logout.
     */
    public function studentAction()
    {
        if (Auth_Wrapper_Student::logout()) {
            //Zend_Session::namespaceUnset('studentCourseDetails');
            $studentSession = Manager_Session_Student::getInstance();
            $studentSession->clearStudentId();
            $this->_redirect('/student');
        }
    }
}