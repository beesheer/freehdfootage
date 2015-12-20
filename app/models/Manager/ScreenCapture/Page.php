<?php
/**
 * Manager class to prepare a page screenshot.
 */
class Manager_ScreenCapture_Page extends Manager_Abstract
{
    public static $defaultImageName = 'screenshot.jpg';
    public static $defaultDelay = 3000;

    /**
     * The adapter to do the real functionality.
     *
     * @var ScreenCapture_Adapter_Abstract
     */
    protected $_adapter = null;

    /**
     * The options for the adapter
     *
     * @var string
     */
    protected $_adapterOptions = array();

    /**
     * Cached page array so that we don't have to re-generate each time.
     *
     * @var integer
     */
    protected $_cacheArray = array();

    /**
     * The only available instance of Manager_ScreenCapture_Page.
     *
     * @var Manager_ScreenCapture_Page
     */
    protected static $_instance;

    /**
     * Returns an instance.
     *
     * Singleton pattern implementation.
     *
     * @return Manager_ScreenCapture_Page
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
    }

    /**
     * Get a screenshot for a page.
     *
     * @param integer $pageId
     * @return array | false
     */
    public function getScreenshot($pageId)
    {
        if (!isset($this->_cacheArray[$pageId])) {
            $screenImage = $this->_generateScreenshot($pageId);
            if (!$screenImage) {
                throw new Zend_Exception('Can not generate a screenshot for page id: ' . $pageId);
            }
            $this->_cacheArray[$pageId] = $screenImage;
        }
        return $this->_cacheArray[$pageId];
    }

    /**
     * Generate a page screenshot.
     *
     * @param integer $pageId
     * @return array | false
     */
    protected function _generateScreenshot($pageId)
    {
        $page = new Object_Page($pageId);
        if (!$page->getId()) {
            return false;
        }
        if (!$page->getPreviewLink()) {
            return false;
        }
        $url = $page->getPreviewLink();
        if (!$page->cloud_file_container) {
            $url = Functions_Common::hostUrl() . $url;
        }
        $capturedImage = $this->getAdapter()->setWorkingDir($page->getStaticContentFolder())->capture($url, self::$defaultImageName, array('delay' => self::$defaultDelay));
        return $capturedImage;
    }

    /**
     * Get the screen capture adapter or initialize one.
     *
     * @return ScreenCapture_Adapter_Abstract
     */
    public function getAdapter()
    {
        if (!$this->_adapter || !is_a($this->_adapter, 'ScreenCapture_Adapter_Abstract')) {
            $adapterOptions = Zend_Registry::getInstance()->config->screencapture->toArray();
            $this->_adapter = ScreenCapture_Adapter::factory($adapterOptions);
        }
        return $this->_adapter;
    }
}
