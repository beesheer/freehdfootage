<?php
/**
 * The abstract class to define a screen capture adapter.
 */
abstract class ScreenCapture_Adapter_Abstract
{
    /**
     * The binary path folder.
     *
     * @var string
     */
    protected $_binaryPath = '';


    /**
     * The executable name.
     *
     * @var string
     */
    protected $_executable = '';

    /**
     * Set the working directory to save the screenshot.
     *
     * @var string
     */
    protected $_workingDir = '';

    /**
     * Construct function.
     *
     * @param array $options
     * @return ScreenCapture_Adapter_Abstract
     */
    public function __construct($options = array())
    {
        $this->init($options);
    }

    /**
     * Init with passed in options.
     *
     * @param array $options
     * @return ScreenCapture_Adapter_Abstract
     */
    public function init($options = array())
    {
        if (!is_array($options) && empty($options)) {
            return $this;
        }
        foreach ($options as $_k => $_v) {
            $method = 'set' . ucwords($_k);
            if (method_exists($this, $method)) {
                $this->$method($_v);
            } else {
                throw new Exception('No such method exists: ' . $method . 'in ' . __CLASS__);
            }
        }
        return $this;
    }

    /**
     * The main public function to implement.
     *
     * @param string $url The URL to capture.
     * @param string $targetPath The target image relative path.
     * @param array $options The extra options.
     *
     * @return string The captured image absolute path.
     * @throws Zend_Exception If no image is generated, throw exception
     */
    abstract public function capture($url, $targetPath, $options = array());

    /**
     * Set the binary path.
     *
     * @param string $path
     * @return ScreenCapture_Adapter_Abstract
     */
    public function setBinaryPath($path)
    {
        if ($path && !file_exists($path)) {
            // The path can be empty in case it is globally installed. See STRAT-120
            throw new Zend_Exception('Binary path folder not found: ' . $path);
        }
        $this->_binaryPath = $path;
        return $this;
    }

    /**
     * Set the binary executable.
     *
     * @param string $executable
     * @return ScreenCapture_Adapter_Abstract
     */
    public function setExecutable($executable)
    {
        $this->_executable = $executable;
        return $this;
    }

    /**
     * Set the working directory.
     *
     * @param string $dirPath
     * @return ScreenCapture_Adapter_Abstract
     */
    public function setWorkingDir($dirPath)
    {
        $this->_workingDir = $dirPath;
        return $this;
    }
}
