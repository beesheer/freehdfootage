<?php
/**
 * The screen capture adapter factory calss.
 */
class ScreenCapture_Adapter
{
    /**
     * Construct
     *
     * @return ScreenCapture_Adapter
     */
    public function __construct($options = array())
    {

    }

    /**
     * Generate an adapter
     *
     * @param mixed $adapterOptions
     * @return ScreenCapture_Adapter_Abstract
     */
    public static function factory($adapterOptions)
    {
        if (!is_array($adapterOptions) || !isset($adapterOptions['adapter'])) {
            throw new Zend_Exception('Invalid screen capture adapter');
        }

        $adapterClass = 'ScreenCapture_Adapter_' . ucwords($adapterOptions['adapter']);
        unset($adapterOptions['adapter']);
        $adapter = new $adapterClass($adapterOptions);
        return $adapter;
    }
}
