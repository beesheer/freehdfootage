<?php
/**
 * Util_HtmlRemoteFileTranslator
 *
 * This helper class will take in a html string and parse all the url.
 * For each url, it will get the source and save it locally and change the link in the html file.
 */
class Util_HtmlRemoteFileTranslator
{
    /**
     * The html string.
     *
     * @var string
     */
    protected $_htmlString = '';

    /**
     * The result string.
     *
     * @var string
     */
    protected $_resultString = '';

    /**
     * The folder path.
     *
     * @var string
     */
    protected $_folder = '';

    /**
     * Construct function.
     *
     * @param array $options
     * @return Util_SimpleTemplate
     */
    public function __construct($options = array())
    {
        $this->init($options);
    }

    /**
     * Init with passed in options.
     *
     * @param array $options
     * @return Ext_SimpleTemplate
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
     * Parse and render.
     *
     * @return string
     */
    public function translate()
    {
        $this->_parseFiles();
        return $this->_resultString;
    }

    /**
     * Set html string.
     *
     * @param string $htmlString
     * @return Util_HtmlRemoteFileTranslator
     */
    public function setHtmlString($htmlString)
    {
        $this->_htmlString = $htmlString;
        return $this;
    }

    /**
     * Set folder path.
     *
     * @param string $folderPath
     * @return Util_HtmlRemoteFileTranslator
     */
    public function setFolder($folder)
    {
        $this->_folder = $folder;
        return $this;
    }

    /**
     * Parse all the URLs. Get the urls and store it locally.
     *
     * @return void
     */
    protected function _parseFiles()
    {
        // General remote files
        $html = preg_replace_callback(
            '/(https?:\/\/\S+\.(?:jpg|png|gif|js|css|svg|pdf|mp4|webm))/iU',
            array($this, '_parseFile'),
            $this->_htmlString
        );
        $this->_resultString = $html;
    }

    /**
     * Get the URL file and save into local folder. Replace with new relative path.
     *
     * @param array $matches
     * @return string
     */
    protected function _parseFile($matches)
    {
        $url = $matches[0];
        $urlParts = parse_url($url);
        $fileInfo = pathinfo($urlParts['path']);
        $fileContent = file_get_contents($url);
        // Save into folder
        $filePath = $this->_folder . DS . $fileInfo['basename'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        file_put_contents($filePath, $fileContent);
        return $fileInfo['basename'];
    }
}