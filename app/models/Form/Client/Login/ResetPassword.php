<?php
/**
 * Client user login forgot password form.
 */
class Form_Client_Login_ResetPassword extends Form_Abstract
{
    /**
     * Init function to set up form elements.
     *
     * @return void
     */
    public function init(){
        //Password
        $this->addElement('password','password',array(
            'decorators'=>$this->_standardElementDecorator,
            'class' => 'form-control',
            'label' => '',
            'required' => true,
            'placeholder' => 'New Password',
            'id' => 'client-reset-password-password',
            'validators' => array(array('StringLength', false, array(8, 50)))
        ));

        //Password2
        $this->addElement('password','password2',array(
            'decorators'=>$this->_standardElementDecorator,
            'class' => 'form-control',
            'label' => '',
            'required' => true,
            'placeholder' => 'Confirm Password',
            'id' => 'client-reset-password-password2',
            'validators' => array(array('StringLength', false, array(8, 50)))
        ));

        //Student status
        $this->addElement('hidden','studentData', array(
            'decorators'=>Array('ViewHelper'),
            'value' => ''
        ));

        //Submit button
        $this->addElement('submit','submit',array(
            'decorators' => $this->_submitElementDecorator,
            'label' => 'Reset My Password',
            'id' => 'client-reset-password-submit'
        ));
    }
}
