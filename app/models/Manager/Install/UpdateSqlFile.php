<?php
/**
 * Manager class to for managing the update sql files.
 */
class Manager_Install_UpdateSqlFile extends Manager_Abstract
{
    const UPDATE_FILE_NAME = 'file_updated.txt';
    const INSTALL_DATE_FILE = 'install_date.txt';

    /**
     * Root folder path.
     *
     * @var string
     */
    protected $_rootFolder = '';

    /**
     * Installed sql files.
     *
     * @var array
     */
    protected $_installed = array();

    /**
     * Avaiable sql files.
     *
     * @var array
     */
    protected $_available = array();

    /**
     * The only available instance of Manager_Install_UpdateSqlFile.
     *
     * @var Manager_Install_UpdateSqlFile
     */
    protected static $_instance;

    /**
     * Returns an instance.
     *
     * Singleton pattern implementation.
     *
     * @return Manager_Install_UpdateSqlFile
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
        $this->_rootFolder = ROOT_PATH . 'install' . DS . 'database' . DS . 'updates' . DS;

        // Get the list of files that has been used
        $installedFile = $this->_rootFolder . self::UPDATE_FILE_NAME;
        if (file_exists($installedFile)) {
            $installed = file_get_contents($installedFile);
        } else {
            $installed = false;
        }

        if ($installed === false) {
            // File doesn't exist.
            $installed = array();
        } else {
            $installed = explode("\n", $installed);
        }
        $this->_installed = $installed;

        // Get the list of files that are available
        $availableFiles = scandir($this->_rootFolder);
        $sqlFiles = array();
        foreach ($availableFiles as $_file) {
            $_fileInfo = pathinfo($_file);
            $_extension = $_fileInfo['extension'];
            if ($_extension == 'sql' || $_extension == 'script') {
                $sqlFiles[] = $_file;
            }
        }
        $this->_available = $sqlFiles;
    }

    /**
     * Get uninstalled updates.
     *
     * @return array
     */
    public function getUninstalledFiles()
    {
        // Whether we have a install date
        $installDateFile = $this->_rootFolder . self::INSTALL_DATE_FILE;
        if (file_exists($installDateFile)) {
            $date = file_get_contents($installDateFile);
            $installDate = strtotime($date);
        } else {
            $installDate = false;
        }

        $missingFiles = array();
        foreach ($this->_available as $_file) {
            $_fileInstalled = in_array($_file, $this->_installed);
            if ($_fileInstalled === false) {
                if ($installDate) {
                    // Check whether it is an old file
                    $_fileDates = explode('_', $_file);
                    $_fileDate = $_fileDates[0];
                    $_fileDate = strtotime($_fileDate);
                    if ($installDate > $_fileDate) {
                        continue;
                    }
                }
                $missingFiles[] = $_file;
            }
        }
        return $missingFiles;
    }

    /**
     * Run a sql file update.
     *
     * @return boolean
     */
    public function runFileUpdate($file, $redirector = false)
    {
        $fileName = $file;
        $file = $this->_rootFolder . $file;
        if (!file_exists($file)) {
            return false;
        }

        // Check whether it is already executed.
        if (in_array($fileName, $this->_installed) === false) {
            // Add that to the installed file
            $installedFile = $this->_rootFolder . self::UPDATE_FILE_NAME;
            $fh = fopen($installedFile, 'a');
            if ($fh) {
                $content = $fileName . "\n";
                fwrite($fh, $content);
                fclose($fh);
            }
        }

        // Execute it
        $sql = file_get_contents($file);

        // Script, just redirect.
        $fileInfo = pathinfo($file);
        if ($fileInfo['extension'] == 'script') {
            $redirector->gotoUrl($sql);
        }

        // Run sql
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        if (strlen($sql) < 1000) {
            // For small sql, directly use zend db
            $db->query($sql);
        } else {
            unset($sql);
            // For large, we have to use cmd line import
            $config = Zend_Registry::getInstance()->config->resources->db->params;
            $cmd = 'mysql --host=' . $config->host . ' --user=' . $config->username . ' --password=' . $config->password . ' ' . $config->dbname . ' < ' . escapeshellarg($file) . ' 2>&1';
            try {
                $rs = shell_exec($cmd);
            } catch (Exception $e) {
                return "Import sql failed: " . $e->getMessage() . "\n\ncmd is: " . $cmd;
            }
            if ($rs) {
                return "Import sql failed: " . $rs . "\n\ncmd is: " . $cmd;
            }
        }
        return true;
    }

    /**
     * View a sql file.
     *
     * @return boolean
     */
    public function viewFile($file)
    {
        $fileName = $file;
        $file = $this->_rootFolder . $file;
        if (!file_exists($file)) {
            return false;
        }
        $sql = file_get_contents($file);
        return $sql;
    }

    /**
     * Set the install date.
     *
     * @param string $dateString
     * @return boolean
     */
    public function setInstallDate($dateString)
    {
        $time = strtotime($dateString);
        if (!$time) {
            return false;
        }
        $installDateFile = $this->_rootFolder . self::INSTALL_DATE_FILE;
        if (file_exists($installDateFile)) {
            unlink($installDate);
        }
        return file_put_contents($installDateFile, $dateString);
    }
}
