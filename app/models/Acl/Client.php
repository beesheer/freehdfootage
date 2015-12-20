<?php
/**
 * This is the Acl class for client area.
 */
class Acl_Client extends Zend_Acl {
    const ROLE_TYPE_GUEST = 'Guest';
    const ROLE_TYPE_USER = 'User';
    const ROLE_TYPE_STUDENT = 'Student';
    const ROLE_TYPE_ADMIN = 'Admin';

    /**
     * Public construct function
     */
    public function __construct() {
        /**
         * Add module resources
         */
        $this->add(new Zend_Acl_Resource('default'));
        $this->add(new Zend_Acl_Resource('client'));
        $this->add(new Zend_Acl_Resource('student'));
        $this->add(new Zend_Acl_Resource('admin'));
        $this->add(new Zend_Acl_Resource('api'));

        /*
         * Add roles
         */
        $this->addRole(new Zend_Acl_Role(self::ROLE_TYPE_GUEST));
        $this->addRole(new Zend_Acl_Role(self::ROLE_TYPE_STUDENT), self::ROLE_TYPE_GUEST);
        $this->addRole(new Zend_Acl_Role(self::ROLE_TYPE_USER), self::ROLE_TYPE_GUEST);
        $this->addRole(new Zend_Acl_Role(self::ROLE_TYPE_ADMIN), self::ROLE_TYPE_USER);

        /*
         * Add rules
         *
         * Notes: we need to define the acl rule for each module and each controller
         */
        // Allow default module access for guest
        $this->allow(self::ROLE_TYPE_GUEST, 'default', null);
        $this->allow(self::ROLE_TYPE_GUEST, 'api', null);
        // Allow user module access for guest
        $this->allow(self::ROLE_TYPE_USER, 'client', null);
        // Allow student module access for student
        $this->allow(self::ROLE_TYPE_STUDENT, 'student', null);
        // Only allow user to access admin area.
        $this->allow(self::ROLE_TYPE_ADMIN, 'admin', null);
    }
}