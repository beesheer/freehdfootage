<?php
/**
 * Client user login forgot password form.
 */
class Form_Client_Login_ForgotPassword extends Form_Abstract
{
    /**
     * Init function to set up form elements.
     *
     * @return void
     */
    public function init(){
        //Set the action
        $this->setAction('/login/forgot-password');

        //Email
        $this->addElement('text','email',array(
            'decorators'=>$this->_standardElementDecorator,
            'class' => 'form-control',
            'label' => '',
            'required' => true,
            'placeholder' => 'Email',
            'id' => 'client-forgot-password-email',
            'validators' => array('EmailAddress')
        ));

        //Submit button
        $this->addElement('submit','submit',array(
            'decorators' => $this->_submitElementDecorator,
            'label' => 'Reset My Password',
            'id' => 'client-forgot-password-submit'
        ));

        //Student status
        $this->addElement('hidden','studentData', array(
            'decorators'=>Array('ViewHelper'),
            'value' => ''
        ));
    }
}
