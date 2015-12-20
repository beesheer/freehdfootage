<?php
/**
 * Client user login form
 */
class Form_Client_Login extends Form_Abstract
{
    /**
     * Init function to set up form elements.
     *
     * @return void
     */
    public function init(){
        //Set the action
        $this->setAction('/login');

        //Username
        $this->addElement('text','email',array(
            'decorators'=>$this->_standardElementDecorator,
            'class' => 'form-control',
            'label'=>'',
            'required'=>true,
            'placeholder' => 'Email',
            'style'=>'text-align:left',
            'id' => 'client-login-username'
        ));

        //Password
        $this->addElement('password','password', array(
            'decorators'=>$this->_standardCheckboxDecorator,
            'class' => 'form-control',
            'label'=>'',
            'required'=>true,
            'placeholder' => 'Password',
            'style'=>'text-align:left',
            'id' => 'client-login-password'
        ));

        //Hash
        /**
         * @todo comment out until we find a solution for csrf
         */
        /*$this->addElement('hash', 'loginformhash', array(
            'ignore' => true,
            'timeout' => 60000,
            'decorators'=>$this->_buttonElementDecorator,
            'salt' => Zend_Registry::getInstance()->config->form->hash->salt
        ));*/
        
        /* //did not work
        $this->addElement(new Zend_Form_Element_Hash(
         'loginformhash',
         array(
         'salt' => 'unique',
         'ignore' => true,
         'timeout' => 60000,
         'session' => new Zend_Session_Namespace(),
         'formId' => get_class($this),
         )
        )
        );*/
        
        //Submit button
        $this->addElement('submit','submit',array(
            'decorators'=>$this->_submitElementDecorator,
            'label'=>'LOGIN',
            'id' => 'client-login-submit'
        ));
    }
}
