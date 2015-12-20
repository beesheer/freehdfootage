<?php
/**
 * Manager class to prepare a pdf based on a pdf template.
 */
class Manager_ScreenCapture_PdfTemplate extends Manager_Abstract
{
    public static $defaultName = 'template.pdf';
    public static $defaultDelay = 1000;

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
     * The only available instance of Manager_ScreenCapture_PdfTemplate.
     *
     * @var Manager_ScreenCapture_PdfTemplate
     */
    protected static $_instance;

    /**
     * Returns an instance.
     *
     * Singleton pattern implementation.
     *
     * @return Manager_ScreenCapture_PdfTemplate
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
     * Get a pdf for a template.
     *
     * @param integer $templateId
     * @param array $tokens. The token array to replace the token holder in the template.
     * @return array | false
     */
    public function getPdf($templateId, $tokens = array())
    {
        $hashedId = md5($templateId . '_' . serialize($tokens));
        if (!isset($this->_cacheArray[$hashedId])) {
            $pdf = $this->_generate($templateId, $tokens);
            if (!$pdf) {
                throw new Zend_Exception('Can not generate pdf for template id: ' . $templateId . ' with tokens ' . print_r($tokens, 1));
            }
            $this->_cacheArray[$hashedId] = $pdf;
        }
        return $this->_cacheArray[$hashedId];
    }

    /**
     * Generate a page screenshot.
     *
     * @param integer $pageId
     * @param array $tokens
     * @return array | false
     */
    protected function _generate($templateId, $tokens = array())
    {
        if (!$tokens) {
            $tokens = array();
        }
        $pdfName = md5($templateId . '_' . serialize($tokens));
        $template = new Object_PdfTemplate($templateId);
        if (!$template->getId()) {
            return false;
        }
        if (!$template->getPreviewLink()) {
            return false;
        }
        // Generate the GET URL with tokens
        $tokenUrl = 'tokens=' . urlencode(Zend_Json::encode($tokens));
        $captureImage = $this->getAdapter()->setWorkingDir($template->getFolderPath())->capture(Functions_Common::hostUrl() . $template->getPreviewLink() . '?' . $tokenUrl, $pdfName . '.pdf', array('delay' => self::$defaultDelay, 'isPdf' => true));

        // Turn this into a pdf
        /*$pdf = new Zend_Pdf();
        $pdfImage = Zend_Pdf_Image::imageWithPath($captureImage);
        $pdfPage = new Zend_Pdf_Page(Zend_Pdf_Page::SIZE_LETTER_LANDSCAPE);
        $pdfPage->drawImage($pdfImage, 20, 20, $pdfPage->getWidth() - 20, $pdfPage->getHeight() - 20);
        $pdf->pages[] = $pdfPage;
        $path = $this->_rootPath . DS . $pdfName . '.pdf';
        $pdf->save($path);*/
        return $pdfName;
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
