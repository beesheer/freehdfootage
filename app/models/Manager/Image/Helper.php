<?php
/**
 * Manager class for handling image processing.
 */
class Manager_Image_Helper extends Manager_Abstract
{
    const THUMB_WIDTH = 240;
    const THUMB_HEIGHT = 180;

    /**
     * Imagine object
     *
     * @var Imagine\Gd\Imagine
     */
    protected $_imagine = null;

    /**
     * The only available instance of Manager_Image_Helper.
     *
     * @var Manager_Image_Helper
     */
    protected static $_instance;

    /**
     * Returns an instance.
     *
     * Singleton pattern implementation.
     *
     * @return Manager_Image_Helper
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
        if(extension_loaded('imagick')) {
            $this->_imagine = new \Imagine\Imagick\Imagine();
        } else {
            $this->_imagine = new \Imagine\Gd\Imagine();
        }
    }

    /**
     * Generate a thumb of an image and save it to the specific path.
     *
     * @param string $sourceFile
     * @param string $destFile
     * @param boolean $isCloud
     * @return boolean
     */
    public function thumb($sourceFile, $destFile, $isCloud = false, $cloudFileName = '')
    {
        if (!file_exists($sourceFile)) {
            return false;
        }
        if (file_exists($destFile)) {
            unlink($destFile);
        }

        $size = new \Imagine\Image\Box(self::THUMB_WIDTH, self::THUMB_HEIGHT);
        $mode = \Imagine\Image\ImageInterface::THUMBNAIL_OUTBOUND;
        try {
            $this->_imagine->open($sourceFile)
            ->thumbnail($size, $mode)
            ->save($destFile);
        } catch (Exception $e) {
            return false;
        }

        // Save to cloud
        if ($isCloud) {
            Manager_File_Rackspace::getInstance()->saveFile($destFile, $cloudFileName, false, 'image/jpeg');
        }
        return true;
    }
}
