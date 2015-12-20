<?php
/**
 * Manager class to prepare a pdf for a review portal.
 */
class Manager_Pdf_ReviewPortal extends Manager_Abstract
{
    /**
     * PDF root path.
     *
     * @var string
     */
    protected $_rootPath = false;

    /**
     * Cached PDF array so that we don't have to re-generate each time.
     *
     * @var integer
     */
    protected $_cacheArray = array();

    /**
     * The only available instance of Manager_Pdf_ReviewPortal.
     *
     * @var Manager_Pdf_ReviewPortal
     */
    protected static $_instance;

    /**
     * Returns an instance.
     *
     * Singleton pattern implementation.
     *
     * @return Manager_Pdf_ReviewPortal
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
        // Make sure we have a writable pdf folder.
        $this->_rootPath = PUBLIC_PATH . 'static' . DS . 'pdf';
        if (!file_exists($this->_rootPath)) {
            mkdir($this->_rootPath, 0777);
        }
    }

    /**
     * Get a pdf for a review portal.
     *
     * @param integer $portalId
     * @return array | false
     */
    public function getPdf($portalId)
    {
        if (!isset($this->_cacheArray[$portalId])) {
            $pdf = $this->_generatePdf($portalId);
            if (!$pdf) {
                throw new Zend_Exception('Can not generate a pdf for review portal id: ' . $portalId);
            }
            $this->_cacheArray[$portalId] = $pdf;
        }
        return $this->_cacheArray[$portalId];
    }

    /**
     * Root path.
     *
     * @return string
     */
    public function getRootPath()
    {
        return $this->_rootPath;
    }

    /**
     * Generate a review portal pdf.
     *
     * @param integer $portalId
     * @return string The pdf name
     */
    protected function _generatePdf($portalId)
    {
        $portal = new Object_ReviewPortal($portalId);
        if (!$portal->getId()) {
            return false;
        }

        // Get the screenshots of all the portal pages.
        $pages = Repo_ReviewPortalPage::getInstance()->getPortalPages($portalId);
        if (!$pages || $pages->count() == 0) {
            return false;
        }

        $pdf = new Zend_Pdf();

        $pageNumber = 1;
        $font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA);
        $fontSize = 10;
        foreach ($pages as $_p) {
            $_images = array();
            $_page = new Object_Page($_p->id);

            // Try get static screenshots if the setting is set.
            if ($_page->screenshot_type == Repo_Page::SCREENSHOT_TYPE_STATIC) {
                $_images = $_page->getStaticScreenshots(true);
            }

            // Get dynamic one.
            if (empty($_images)) {
                $_images[] = Manager_ScreenCapture_Page::getInstance()->getScreenshot($_p->id);
            }

            foreach ($_images as $_image) {
                $_pdfImage = Zend_Pdf_Image::imageWithPath($_image);
                $_pdfPage = new Zend_Pdf_Page(Zend_Pdf_Page::SIZE_LETTER_LANDSCAPE);
                $_pdfPage->drawImage($_pdfImage, 20, 36, $_pdfPage->getWidth() - 20, $_pdfPage->getHeight() - 20);
                $_pdfPage->setFont($font, 16);
                $_pdfPage->setFillColor(Zend_Pdf_Color_Html::color('#333333'))->drawText($pageNumber, $_pdfPage->getWidth()/2, 10);
                $pageNumber++;
                $pdf->pages[] = $_pdfPage;
            }
        }
        $pdfName = $portalId . '_' . time() . '.pdf';
        $path = $this->_rootPath . DS . $pdfName;
        $pdf->save($path);
        return $pdfName;
    }
}
