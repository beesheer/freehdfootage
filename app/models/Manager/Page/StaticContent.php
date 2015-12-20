<?php
/**
 * Manager class to handle static content upload, page creation based on uploaded content, etc.
 */

class Manager_Page_StaticContent extends Manager_Abstract
{
    const SCREENSHOTS_FOLDER = 'screenshots';

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
     * The only available instance of Manager_Page_StaticContent.
     *
     * @var Manager_Page_StaticContent
     */
    protected static $_instance;

    /**
     * Returns an instance.
     *
     * Singleton pattern implementation.
     *
     * @return Manager_Page_StaticContent
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
            mkdir($this->_rootPath, 0777);
        }

        $this->_tempPath = $this->_rootPath . DS . 'temp';
        if (!file_exists($this->_tempPath)) {
            mkdir($this->_tempPath, 0777);
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
            mkdir($clientPath, 0777);
        }
        return $clientPath;
    }

    /**
     * Folder name generation for a page.
     *
     * @return string
     */
    public static function getPageFolderName($pageId, $version = false)
    {
        return md5(Zend_Registry::getInstance()->config->app->salt . $pageId) . ($version ? '_version' . $version : '');
    }

    /**
     * Update a page's static content.
     *
     * @param string $zipFilePath
     * @param integer $pageId
     * @return boolean
     */
    public function updatePage($zipFilePath, $pageId)
    {
        if (!file_exists($zipFilePath)) {
            return false;
        }

        $zip = new ZipArchive;
        $res = $zip->open($zipFilePath);
        if ($res !== TRUE) {
            return false;
        }

        $page = new Object_Page($pageId);
        if (!$page->getId()) {
            return false;
        }
        $this->_currentClientId = $page->client_id;

        // Extract to a temp folder
        $this->_currentTempFolder = $this->_tempPath . DS . time();
        mkdir($this->_currentTempFolder, 0777);
        $zip->extractTo($this->_currentTempFolder);
        $zip->close();

        $folderPath = $this->_currentTempFolder . DS . $page->page_language . DS . $page->page_id;
        if (!file_exists($folderPath)) {
            return false;
        }

        // Copy to a path
        $_pageStorageFolder = $this->getClientFolderPath($this->_currentClientId) . DS . self::getPageFolderName($page->page_id);
        if (!file_exists($_pageStorageFolder)) {
            mkdir($_pageStorageFolder, 0777);
        } else {
            // Remove the existing content since we are updating.
            if (file_exists($_pageStorageFolder . DS . $page->page_id)) {
                Functions_File::rrmdir($_pageStorageFolder . DS . $page->page_id);
            }
        }
        Functions_File::recurse_copy($folderPath, $_pageStorageFolder . DS . $page->page_id);

        // Use static thumb if possible.
        $page->useStaticThumb();

        return true;
    }

    /**
     * Generate the pages with the uploaded zip file.
     *
     * @param string $zipFilePath Temp zip file path.
     * @param integer $clientId
     * @return false | array An array of page ids.
     */
    public function generatePages($zipFilePath, $clientId)
    {
        $this->_currentClientId = (int)$clientId;
        if ($this->_currentClientId <= 0) {
            return false;
        }

        if (!file_exists($zipFilePath)) {
            return false;
        }

        $zip = new ZipArchive;
        $res = $zip->open($zipFilePath);
        if ($res !== TRUE) {
            return false;
        }

        // Extract to a temp folder
        $this->_currentTempFolder = $this->_tempPath . DS . time();
        mkdir($this->_currentTempFolder, 0777);
        $zip->extractTo($this->_currentTempFolder);
        $zip->close();

        $pages = array();
        foreach (self::$langs as $_lang) {
            if (file_exists($this->_currentTempFolder . DS . $_lang)) {
                // Create all the pages
                $_pages = $this->_generatePagesForLanguageFolder($this->_currentTempFolder . DS . $_lang, $_lang);
                if (is_array($_pages) && !empty($_pages)) {
                    $pages += $_pages;
                }
            }
        }

        // Close zip and remove temp working folder.
        Functions_File::rrmdir($this->_currentTempFolder);

        return $pages;
    }

    /**
     * Generate pages based on the language folder.
     *
     * @param string $folderPath
     * @param string $language
     * @return false array
     */
    protected function _generatePagesForLanguageFolder($folderPath, $language)
    {
        $pages = array();
        $dir = dir($folderPath);
        while (false !== ($entry = $dir->read())) {
            if ($entry == '.' || $entry == '..' || $entry == '.DS_Store') {
                continue;
            }
            // We deal with each page
            $_pageFolder = $folderPath . DS . $entry;
            $_pageId = $entry;
            $_id = (int) Repo_Page::getInstance()->addNew($this->_currentClientId, $_pageId, Repo_Page::PAGE_TYPE_STATIC, Repo_Page::PAGE_STATUS_IN_PROGRESS, '', false, false, false, false, $language, $_pageId, false);
            if (empty($_id)) {
                // OK, we are deal with existing page
                $_id = Repo_Page::getInstance()->clientPageExists($this->_currentClientId, $_pageId, $language);
                $_existingPage = new Object_Page($_id);
                $_existingPage->cloud_file_container = null;
                $_existingPage->version++;
                $_existingPage->save();
            }

            // Copy to a path
            $_pageStorageFolder = $this->getClientFolderPath($this->_currentClientId) . DS . self::getPageFolderName($_pageId);
            if (!file_exists($_pageStorageFolder)) {
                mkdir($_pageStorageFolder, 0777);
            } else {
                // Remove the existing content since we are updating.
                if (file_exists($_pageStorageFolder . DS . $_pageId)) {
                    Functions_File::rrmdir($_pageStorageFolder . DS . $_pageId);
                }
            }
            Functions_File::recurse_copy($_pageFolder, $_pageStorageFolder . DS . $_pageId);

            // Check whether successfully
            if (!file_exists($_pageStorageFolder . DS . $_pageId)) {
                throw new Zend_Exception('Can not copy page content from a zip file: ' . $_pageStorageFolder);
            }
            $pages[] = $_id;

            // In case we upload a thumb image, use it.
            $page = new Object_Page($_id);
            $page->useStaticThumb();
        }
        $dir->close();
        return $pages;
    }
}