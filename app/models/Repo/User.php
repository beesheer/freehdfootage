<?php
/**
 * This is the repository for holding all of the users.
 */
class Repo_User extends Repo_Abstract
{
    const ADD_USER_ERROR_EMAIL_EXISTS = 'email-exists';
    const SERVICE_USER_TYPE = 'service';

    /**
     * The only available instance of Repo_User.
     *
     * @var Repo_User
     */
    protected static $_instance;

    /**
     * Returns an instance.
     *
     * Singleton pattern implementation.
     *
     * @return Repo_User
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Initialize some of the basic properties.
     *
     * @return void
     */
    protected function init()
    {
        $this->_dbTable = new Db_Table_User();
    }

    /**
     * Create a new user from raw data.
     *
     * @param string $email
     * @param string $password
     * @param string $UDID
     * @param string $firstname
     * @param string $surname
     * @param string $roleId
     * @param string $clientId
     *
     * @return integer | string User id or error message.
     */
    public function addNew($email, $password, $UDID, $firstname, $surname, $roleId, $clientId)
    {
        $dbTable = $this->getUpdateTable();

        // Check email existence.
        $row = $this->getRow(
            array(
                array(
                    'where' => 'email = ?',
                    'bind' => $email
                )
            )
        );
        if ($row) {
            return self::ADD_USER_ERROR_EMAIL_EXISTS;
        }

        if (is_array($roleId)) {
            $roleId = implode(',', $roleId);
        }

        // Add
        $userRowData = array(
            'email' => $email,
            'password' => Auth_Wrapper_User::getPasswordHash($password),
            'UDID' => $UDID,
            'firstname' => $firstname,
            'surname' => $surname,
            'role_id' => $roleId,
            'client_id' => $clientId,
            'created_datetime' => date('Y-m-d H:i:s')
        );
        $newRow = $dbTable->createRow(
            $userRowData
        );
        try {
            $newRow->save();
        } catch (Exception $e) {
            return 'Creating new user error: ' . $e->getMessage();
        }

        // We also update the contact rows with the same email addresses
        Repo_Contact::getInstance()->addRefUserByEmail($email, $newRow->id);

        return $newRow->id;
    }

    /**
     * Create a new service user from raw data.
     *
     * @param string $email
     * @param string $password
     * @param string $UDID
     *
     * @return integer | string User id or error message.
     */
    public function addNewServiceUser($email, $password, $UDID)
    {
        $dbTable = $this->getUpdateTable();

        // Check email existence.
        $row = $this->getRow(
            array(
                array(
                    'where' => 'email = ?',
                    'bind' => $email
                )
            )
        );
        if ($row) {
            return self::ADD_USER_ERROR_EMAIL_EXISTS;
        }

        // Add
        $userRowData = array(
            'email' => $email,
            'password' => Auth_Wrapper_User::getPasswordHash($password),
            'UDID' => $UDID,
            'user_type' => self::SERVICE_USER_TYPE,
            'created_datetime' => date('Y-m-d H:i:s')
        );
        $newRow = $dbTable->createRow(
            $userRowData
        );
        try {
            $newRow->save();
        } catch (Exception $e) {
            return 'Creating error: ' . $e->getMessage();
        }

        return $newRow->id;
    }

    /**
     * Check whether the email exists.
     *
     * @param string $email
     * @param integer $excludedUserId
     * @return boolean
     */
    public function emailExists($email, $excludedUserId = false)
    {
        $dbTable = $this->getUpdateTable();
        $find = array(
            array(
                'where' => 'email = ?',
                'bind' => $email
            )
        );
        if ($excludedUserId) {
            $find[] = array(
                'where' => 'id <> ?',
                'bind' => $excludedUserId
            );
        }
        // Check email existence.
        $row = $this->getRow($find);
        if ($row) {
            return $row->id;
        }
        return false;
    }

    /**
     * Retrieve the user client id.
     *
     * @param string $id
     * @return int
     */
    public function getUserClientId( $id )
    {
        $dbTable = $this->getUpdateTable();
        $find = array(
            array(
                'where' => 'id = ?',
                'bind' => $id
            )
        );

        // Return client id
        $row = $this->getRow($find);
        if ($row) {
            return $row->client_id;
        }
        return false;
    }

    /**
     * Check whether the reset password key exists.
     *
     * @param string $resetKey
     * @return boolean
     */
    public function resetKeyUser($resetKey)
    {
        $dbTable = $this->getUpdateTable();
        $find = array(
            array(
                'where' => 'password_reset_key = ?',
                'bind' => $resetKey
            )
        );
        // Check email existence.
        $row = $this->getRow($find);
        if ($row) {
            return $row->id;
        }
        return false;
    }

    /**
     * Get a list of service users.
     *
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function getServiceUsers()
    {
        $search = array(
            array(
                'where' => 'user_type = ?',
                'bind' => self::SERVICE_USER_TYPE
            )
        );
        return $this->getRows($search, false, false, 'id ASC', false);
    }

    /**
     * Get list of client users.
     *
     * @param integer $clientId
     * @param string $q
     * @param integer $offset
     * @param integer $limit
     * @param mixed $sortBy
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function getClientUsers($clientId = false, $q = false, $offset = 0, $limit = false, $sortBy = 'surname DESC')
    {
        $dbTable = $this->getFetchTable();
        $select = $dbTable->select()->setIntegrityCheck(false)
            ->from(
                array('u' => 'user'),
                array(
                    'id' => 'u.id',
                    'name' => "CONCAT_WS(',', u.surname, u.firstname)",
                    'firstname' => 'u.firstname',
                    'surname' => 'u.surname',
                    'email' => 'u.email',
                    'role_id' => 'u.role_id',
                    'client_id' => 'u.client_id',
                    'u.created_datetime',
                    'u.modified_datetime'
                )
            )
            ->joinLeft(
                array('c' => 'client'),
                'u.client_id = c.id',
                array(
                    'client_name' => 'c.name'
                )
            )
            ->joinLeft(
                array('r' => 'role'),
                'u.role_id = r.id',
                array(
                    'user_type' => 'r.label'
                )
            );
        if ($clientId) {
            $select->where('u.client_id = ?', $clientId);
        }
        $select->where('u.user_type IS NULL OR u.user_type <> ?', self::SERVICE_USER_TYPE);
        if (!empty($q)) {
            $q = '%' . $q . '%';
            $select->where('u.firstname like ? OR u.surname like ? OR email like ?', $q);
        }
        if ($limit) 
        {
            $select->limit($limit, $offset);
        }
        if ($sortBy) 
        {
            $select->order($sortBy);
        }
        return $dbTable->fetchAll($select);
    }

    /**
     * Get total user count based on client id and q.
     *
     * @param integer $clientId
     * @param string $q
     * @return integer
     */
    public function getTotalCount($clientId = false, $q = false)
    {
        $users = $this->getClientUsers($clientId, $q);
        return $users->count();
    }

    /**
     * Get the users from the client and all the parent clients.
     *
     * @param integer $clientId
     * @param Zend_Db_Table_Rowset_Abstract
     */
    public function getClientAndParentClientsUsers($clientId)
    {
        $dbTable = $this->getFetchTable();
        $select = $dbTable->select()->setIntegrityCheck(false)
            ->from(
                array('u' => 'user'),
                array(
                    'id' => 'u.id',
                    'name' => "CONCAT_WS(',', u.surname, u.firstname)",
                    'firstname' => 'u.firstname',
                    'surname' => 'u.surname',
                    'email' => 'u.email',
                    'role_id' => 'u.role_id',
                    'client_id' => 'u.client_id',
                    'u.created_datetime',
                    'u.modified_datetime'
                )
            )
            ->joinLeft(
                array('c' => 'client'),
                'u.client_id = c.id',
                array(
                    'client_name' => 'c.name'
                )
            )
            ->joinLeft(
                array('r' => 'role'),
                'u.role_id = r.id',
                array(
                    'user_type' => 'r.label'
                )
            );
        if ($clientId) {
            $parentClientIds = Repo_Client::getInstance()->getParentClients($clientId);
            $parentClientIds[] = $clientId;
            $select->where('u.client_id IN (' . implode(',', $parentClientIds) . ')');
        }
        $select->where('u.user_type IS NULL OR u.user_type <> ?', self::SERVICE_USER_TYPE);
        $select->order(array(
            'name ASC'
        ));
        return $dbTable->fetchAll($select);
    }

    /**
     * Check password pair.
     *
     * @param string $userId
     * @param string $password
     * @return boolean
     */
    public function checkPassword($userId, $password)
    {
        $user = new Object_User($userId);
        if (Auth_Wrapper_User::verifyAuth($user->email, $password) !== false) {
            return true;
        }
        return false;
    }
}