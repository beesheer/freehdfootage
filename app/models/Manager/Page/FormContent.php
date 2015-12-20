<?php
/**
 * Manager class to handle form template setup, create folders based on client and page name, unzip to client folder, etc.
 */

class Manager_Page_FormContent extends Manager_Abstract
{
    /**
     * Languages we will extract from the zip folder.
     *
     * @var array
     */
    public static $langs = array('en' => 'en', 'fr' => 'fr');

    /**
     * The root path where the static contents are stored.
     *
     * @var string
     */
    protected $_rootPath = '';

    /**
     * The temp path where the temp unzipped files are stored.
     *
     * @var string
     */
    protected $_tempPath = '';

    /**
     * Current temp working folder.
     *
     * @var string
     */
    protected $_currentTempFolder = '';

    /**
     * Current client id.
     *
     * @var integer
     */
    protected $_currentClientId = false;

    /**
     * The only available instance of Manager_Page_FormContent.
     *
     * @var Manager_Page_FormContent
     */
    protected static $_instance;

    /**
     * Returns an instance.
     *
     * Singleton pattern implementation.
     *
     * @return Manager_Page_FormContent
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
        $this->_rootPath = PUBLIC_PATH . 'static';
        if (!file_exists($this->_rootPath)) {
			//override server permission settings
			$old = umask(0);
            mkdir($this->_rootPath, 0777);
			//restore
			umask($old);
        }

        $this->_tempPath = $this->_rootPath . DS . 'temp';
        if (!file_exists($this->_tempPath)) {
			//override server permission settings
			$old = umask(0);
            mkdir($this->_tempPath, 0777);
			//restore
			umask($old);
        }
    }

    /**
     * Get the client folder absolute path.
     *
     * @param integer $clientId
     * @return string
     */
    public function getClientFolderPath($clientId)
    {
        $clientPath = $this->_rootPath . DS . $clientId;
        if (!file_exists($clientPath)) {
			//override server permission settings
			$old = umask(0);
            mkdir($clientPath, 0777);
			//restore
			umask($old);
        }
        return $clientPath;
    }
	
	/**
     * Get the questionassets absolute path.
     *
     * @param integer $clientId
     * @return string
     */
    public function getQuestionAssetsFolderPath($clientId)
    {
        $folderPath = $this->getClientFolderPath($clientId);
        $assetsPath = $folderPath . DS . 'questionassets';

        if (!file_exists($assetsPath)) {
			//override server permission settings
			$old = umask(0);
            mkdir($assetsPath, 0777);
			//restore
			umask($old);
        }
        return $assetsPath;
    }
	
    /**
     * Folder name generation for a page.
     *
     * @return string
     */
    public static function getPageFolderName($pageId)
    {
        return md5(Zend_Registry::getInstance()->config->app->salt . $pageId);
    }

    

    /**
     * Unzip the form template and its dependencies to the client folder
     *
     * @param string $zipFilePath Temp zip file path.
     * @param integer $clientId
	 * @param integer $_pageId
     * @return true | false
    */
    public function generateSurvey($zipFilePath, $clientId, $_pageId)
    {
        $this->_currentClientId = (int)$clientId;
        if ($this->_currentClientId <= 0) {
            return false;
        }

        if (!file_exists($zipFilePath)) {
            return false;
        }

		$_pageStorageFolder = $this->getClientFolderPath($this->_currentClientId) . DS . self::getPageFolderName($_pageId);
        $indexFileName = $_pageStorageFolder . DS . $_pageId. DS . $_pageId . ".html";

		// this is a one-time only copy
		if (!file_exists($indexFileName)) {
			$zip = new ZipArchive;
			$res = $zip->open($zipFilePath);
			if ($res !== TRUE) {
				return false;
			}
			// Extract to a temp folder
			$this->_currentTempFolder = $this->_tempPath . DS . time();
			//override server permission settings
			$old = umask(0);
			mkdir($this->_currentTempFolder, 0777);
			$zip->extractTo($this->_currentTempFolder);
			mkdir($_pageStorageFolder, 0777);
			//restore
			umask($old);
			Functions_File::recurse_copy($this->_currentTempFolder, $_pageStorageFolder . DS . $_pageId);
            // rename the index file
            $_templateIndexFile = $_pageStorageFolder . DS . $_pageId. DS . "index.html";
            if(file_exists($_templateIndexFile)) {
                rename ($_templateIndexFile, $indexFileName);
            }
			// Close zip and remove temp working folder.
			$zip->close();
			Functions_File::rrmdir($this->_currentTempFolder);
			return $_pageStorageFolder;
		} else {
			return $_pageStorageFolder;
		}
		
		
    }

	
}