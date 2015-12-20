<?php
/**
 * Admin login form.
 */
class Form_Admin_Login extends Form_Abstract
{
    /**
     * Init function to set up form elements.
     *
     * @return void
     */
    public function init(){
        //Set the action
        $this->setAction('/login/admin');

        //Username
        $this->addElement('text','email',array(
            'decorators' => $this->_standardElementDecorator,
            'class' => 'form-control',
            'label' => '',
            'required' => true,
            'placeholder' => 'Email',
            'id' => 'admin-login-username'
        ));

        //Password
        $this->addElement('password','password', array(
            'decorators' => $this->_standardElementDecorator,
            'class' => 'form-control',
            'label' => '',
            'required' => true,
            'placeholder' => 'Password',
            'id' => 'admin-login-password'
        ));

        //Remember me
        $this->addElement('checkbox','remember', array(
            'decorators' => $this->_standardCheckboxDecorator,
            'label' => '',
            'description' => 'Remember Me',
            'required' => true
        ));

        if (isset($this->extraParams) && isset($this->extraParams['addHash'])) {
            // hash
            $this->addElement('hash', 'adminloginformhash', array(
                'decorators'=>$this->_buttonElementDecorator,
                'salt' => Zend_Registry::getInstance()->config->form->hash->salt
            ));
        }

        //Submit button
        $this->addElement('submit','submit',array(
            'decorators'=>$this->_submitElementDecorator,
            'label'=>'LOGIN',
            'id' => 'admin-login-submit'
        ));
    }
}
