<?php
/**
 * Admin create client user form.
 */
class Form_Client_ResouceLibrary_View extends Form_Abstract
{
    /**
     * Init function to set up form elements.
     *
     * @return void
     */
    public function init(){
        $this->setAttrib('id', 'create-new-user-form');

        $clients = Repo_Client::getInstance()->getSelectArray();
        $roles = Repo_Role::getInstance()->getSelectArray();
        unset($roles['']);

        // Email
        $this->addElement('text','email',array(
            'decorators' => $this->_standardElementDecorator,
            'class' => 'form-control',
            'label' => 'Email',
            'required' => true,
            'validators' => array('EmailAddress')
        ));

        // Password
        $this->addElement('text','password',array(
            'decorators' => $this->_standardElementDecorator,
            'class' => 'form-control',
            'label' => 'Password',
            'required' => true,
            'validators' => array(array('StringLength', false, array(8, 50)))
        ));

        // UDID
        $this->addElement('text','UDID',array(
            'decorators' => $this->_standardElementDecorator,
            'class' => 'form-control',
            'label' => 'UDID',
            'required' => false
        ));

        // Surname
        $this->addElement('text','surname',array(
            'decorators' => $this->_standardElementDecorator,
            'class' => 'form-control',
            'label' => 'Surname',
            'required' => true
        ));

        // First name
        $this->addElement('text','firstname',array(
            'decorators' => $this->_standardElementDecorator,
            'class' => 'form-control',
            'label' => 'First Name',
            'required' => true
        ));

        // User type
        /*$this->addElement('text','user_type',array(
            'decorators' => $this->_standardElementDecorator,
            'class' => 'form-control',
            'label' => 'User Type',
            'required' => true
        ));*/

        // Role
        $this->addElement('multiCheckbox','role', array(
            'decorators' => $this->_multiCheckboxDecorator,
            'label' => 'Role',
            'required' => true,
            'registerInArrayValidator' => false
        ));

        $this->getElement('role')->addMultiOptions(
            $roles
        );

        // Client
        $this->addElement('select','client', array(
            'decorators' => $this->_standardElementDecorator,
            'class' => 'form-control',
            'label' => 'Client',
            'required' => true
        ));

        $this->getElement('client')->addMultiOptions(
            $clients
        );

        // ID
        $this->addElement('hidden','id',array(
            'decorators' => $this->_buttonElementDecorator,
            'required' => false
        ));

        // Set user values if a user is passed in.
        if (isset($this->extraParams['user']) && $user = $this->extraParams['user']) {
            $this->setUser($user);
        }
    }

    /**
     * Set initial user data.
     *
     * @param Object_User $user
     * @return boolean
     */
    public function setUser($user)
    {
        if (!is_a($user, 'Object_User')) {
            return false;
        }

        $this->getElement('email')->setValue($user->email);
        // We don't have to change password
        $this->getElement('password')->setRequired(false)->setDescription('Password will not be changed if it is left empty');
        $this->getElement('UDID')->setValue($user->UDID);
        $this->getElement('surname')->setValue($user->surname);
        $this->getElement('firstname')->setValue($user->firstname);
        $this->getElement('role')->setValue($user->getRoleIds());
        $this->getElement('client')->setValue($user->client_id);
    }

    /**
     * Update the user with the form data.
     *
     * @param Object_User $user
     * @return boolean
     */
    public function updateUser($user)
    {
        if (!is_a($user, 'Object_User')) {
            return false;
        }

        // Check email duplication
        if (Repo_User::getInstance()->emailExists($this->getValue('email'), $user->id)) {
            $this->getElement('email')->addError('Email exists: ' . $this->getValue('email'));
            return false;
        } else {
            $user->email = $this->getValue('email');
        }

        $newPassword = $this->getValue('password');
        if (!empty($newPassword)) {
            $user->password = Auth_Wrapper_User::getPasswordHash($this->getValue('password'));
        }

        $roleIds = $this->getValue('role');
        $user->firstname = $this->getValue('firstname');
        $user->surname = $this->getValue('surname');
        $user->UDID = $this->getValue('UDID');
        if (is_array($roleIds)) {
            $roleIds = implode(',', $roleIds);
        }
        $user->role_id = $roleIds;
        $user->client_id = $this->getValue('client');

        return $user->save();
    }

}
