<?php
/**
 * The user model.
 */
class Object_User extends Object_Abstract
{
    /**
     * The construct function. Must providing the id.
     *
     * @param integer $id
     * @return Object_User
     */
    public function __construct($id)
    {
        $this->_repo = Repo_User::getInstance();
        $this->_dataRow = $this->_repo->findRow($id);
        if ($this->_dataRow) {
            $this->_data = $this->_dataRow->toArray();
            $this->_id = $id;
        }
    }

    /**
     * Get client object of the user
     *
     * @return Object_Client | false
     */
    public function getClient()
    {
        if (!$this->_id && !$this->client_id) {
            return false;
        }
        return new Object_Client($this->client_id);
    }

    /**
     * Save user packages.
     *
     * @param array $packages A array of package ids.
     * @return boolean
     */
    public function savePackages($packages)
    {
        // Remove all existing records
        Repo_UserPackage::getInstance()->deleteAllUserPackages($this->getId());

        // Add one by one
        if (!is_array($packages) || empty($packages)) {
            return false;
        }

        foreach ($packages as $_pId) {
            Repo_UserPackage::getInstance()->addNew($this->getId(), $_pId);
        }
        return true;
    }

    /**
     * Save user teams.
     *
     * @param array $teams A array of team ids.
     * @return boolean
     */
    public function saveTeams($teams)
    {
        // Remove all existing records
        Repo_TeamUser::getInstance()->deleteAllUserTeams($this->getId());

        // Add one by one
        if (!is_array($teams) || empty($teams)) {
            return false;
        }

        foreach ($teams as $_tId) {
            Repo_TeamUser::getInstance()->addNew($_tId, $this->getId());
        }
        return true;
    }

    /**
     * Save user apps.
     *
     * @param array $apps A array of team ids.
     * @return boolean
     */
    public function saveApps($apps)
    {
        // Remove all existing records
        Repo_AppUser::getInstance()->deleteAllUserApps($this->getId());

        // Add one by one
        if (!is_array($apps) || empty($apps)) {
            return false;
        }

        foreach ($apps as $_aId) {
            Repo_AppUser::getInstance()->addNew($_aId, $this->getId());
        }
        return true;
    }

    /**
     * Get an array of package with titles.
     *
     * @return array
     */
    public function getPackageTitleSelectOptions()
    {
        $options = array();
        $packages = Repo_UserPackage::getInstance()->getUserPackages($this->getId());
        if (!$packages || !$packages->count()) {
            return $options;
        }
        foreach ($packages as $_package) {
            $_titleOptions = array();
            $_titles = Repo_PackageTitle::getInstance()->getPackageTitles($_package->id);
            if ($_titles) {
                foreach ($_titles as $_title) {
                    $_titleOptions[$_title->id] = $_title->name;
                }
            }
            $options[$_package->name] = $_titleOptions;
        }
        return $options;
    }

    /**
     * Get an array of packages.
     *
     * @return array
     */
    public function getPackageSelectOptions()
    {
        $options = array();
        $packages = Repo_UserPackage::getInstance()->getUserPackages($this->getId());
        if (!$packages || !$packages->count()) {
            return $options;
        }
        foreach ($packages as $_package) {
            $options[$_package->id] = $_package->name;
        }
        return $options;
    }

    /**
     * Add/update a device id to a user.
     *
     * @param string $deviceId
     * @return integer | false
     */
    public function deviceIdUsed($deviceId)
    {
        $rowId = Repo_UserDevice::getInstance()->addNew($this->getId(), $deviceId);
        return $rowId;
    }

    /**
     * Get user permissions.
     *
     * @reuturn array
     */
    public function getPermissions()
    {
        $roleIds = $this->getRoleIds();
        $permissions = array();
        foreach ($roleIds as $_rId) {
            $permissions = array_merge(Repo_RolePermission::getInstance()->getRolePermissions($_rId)->toArray(), $permissions);
        }
        return $permissions;
    }

    /**
     * Get all the titles a user is entitled, including package, team package, individual titles.
     *
     */
    public function getAllTitles()
    {
        $allPackages = array();
        $packages = Repo_UserPackage::getInstance()->getUserPackages($this->getId());
        foreach ($packages as $_package) {
            if (!isset($allPackages[$_package->id])) {
                $allPackages[$_package->id] = $_package;
            }
        }
        $teams = Repo_TeamUser::getInstance()->getUserTeams($this->getId());
        foreach ($teams as $_team) {
            $_teamId = $_team->id;
            $_packages = Repo_TeamPackage::getInstance()->getTeamPackages($_teamId);
            foreach ($_packages as $_package) {
                if (!isset($allPackages[$_package->id])) {
                    $allPackages[$_package->id] = $_package;
                }
            }
        }

        // OK get the titles for each package
        $allTitles = array();
        foreach ($allPackages as $_packageId => $_package) {
            $_titles = Repo_PackageTitle::getInstance()->getPackageTitles($_packageId);
            foreach ($_titles as $_title) {
                if (!isset($allTitles[$_title->id])) {
                    $allTitles[$_title->id] = $_title;
                }
            }
        }

        return $allTitles;
    }

    /**
     * Reset password request with email address.
     *
     * @return boolean
     */
    public function resetPasswordRequest( $extra_parameters = null )
    {
        // Generate new key
        $newKey = md5($this->getId() . '_' . $this->email . '_' . time());
        $this->password_reset_key = $newKey;
        $this->save();

        $baseResetURL = Functions_Common::hostUrl() . '/login/reset-password/key/';

        $sendingEntity = "";

        //Customize for students if necessary
        if( !is_null($extra_parameters) ) {
            if( $extra_parameters->getValue('studentData') ) {
                $urlParams =  urldecode( $extra_parameters->getValue('studentData') );
                $urlParams = json_decode($urlParams);
                $resetSender = $urlParams->resetSender;
                $resetURL = $urlParams->resetUrl;
                $baseResetURL = "http://" . $resetURL . "?reset-password=";
                $sendingEntity = " for " . $resetSender;
            }
        }
        // Send out email
        $t = Zend_Registry::getInstance()->translate;
        $emailBody = $t->_('forgot-password-email-body');
        $emailBody = str_replace(
            array('{resetPasswordLink}'),
            array($baseResetURL . $this->password_reset_key),
            $emailBody
        );
        $emailSubject = $t->_('forgot-password-email-subject') . $sendingEntity;
        $emailAgent = new Mail_Mail();
        $emailAgent->setBody($emailBody)
            ->setSubject($emailSubject)
            ->setTo($this->email);
        try {
            $emailAgent->send();
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * Reset password.
     *
     * @return boolean
     */
    public function resetPassword($password)
    {
        $this->password = Auth_Wrapper_User::getPasswordHash($password);
        $this->password_reset_key = null;
        $this->save();
        return true;
    }

    /**
     * Clear password reset key
     *
     * @return boolean
     */
    public function clearPasswordResetKey()
    {
        $this->password_reset_key = null;
        $this->save();
        return true;
    }

    /**
     * Change password by providing the current password.
     *
     * @param string $old
     * @param string $new
     * @return mixed
     */
    public function changePassword($old, $new)
    {
        if (Auth_Wrapper_User::verifyAuth($this->email, $old) === false) {
            return 'Invalid current password provided';
        }

        $this->password = Auth_Wrapper_User::getPasswordHash($new);
        $this->save();
        return true;
    }

    /**
     * Get role ids in an array.
     *
     * @return array
     */
    public function getRoleIds()
    {
        return explode(',', $this->role_id);
    }

    /**
     * Whether user is super admin or not.
     *
     * @return boolean
     */
    public function isUserSuperAdmin()
    {
        return in_array(Repo_Role::$roleIds[Repo_Role::ROLE_SUPERADMIN], $this->getRoleIds()) ? true : false;
    }

    /**
     * Basic info without sensitive info.
     *
     * @return array
     */
    public function getBasicInfo()
    {
        return array(
            'id' => $this->id,
            'client_id' => $this->client_id,
            'surname' => $this->surname,
            'firstname' => $this->firstname
        );
    }
}
