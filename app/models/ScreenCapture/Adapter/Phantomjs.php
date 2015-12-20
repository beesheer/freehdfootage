<?php
/**
 * The phantom.js screen capture adapter.
 *
 * WARNING: This adapter can only be invoked from command line due to issues from phantomjs.
 *
 * @see https://github.com/ariya/phantomjs/wiki/Screen-Capture
 */
class ScreenCapture_Adapter_Phantomjs extends ScreenCapture_Adapter_Abstract
{
    /**
     * The js script template.
     *
     * @var string
     */
    public $scriptTempate = <<<JS
    var page = require('webpage').create();
    page.zoomFactor = 1;
    page.open('{{url}}', function () {
    window.setTimeout(function(){
        {{extraActions}}
        page.render('{{targetPath}}');
        phantom.exit();
    }, {{delay}})
});
JS;

    /**
     * PDF Template script template.
     *
     *  @var mixed
     */
    public $pdfScriptTempate = <<<JS
    var page = require('webpage').create();
    page.viewport = {
        width: 1024,
        height: 768
    };
    page.paperSize = {
        width: '7.5in',
        height: '5.625in'
    }
    page.zoomFactor = 1;
    page.open('{{url}}', function () {
    window.setTimeout(function(){
        {{extraActions}}
        page.render('{{targetPath}}');
        phantom.exit();
    }, {{delay}})
});
JS;

    /**
     * The main public function to implement.
     *
     * @param string $url The URL to capture.
     * @param string $targetPath The saved image path.
     * @param array $options The extra options.
     *
     * @return string The captured image absolute path.
     * @throws Zend_Exception If no image is generated, throw exception
     */
    public function capture($url, $targetPath, $options = array())
    {
        $tokens = array_merge(array('url' => $url, 'targetPath' => $targetPath), $options);
        $script = $this->_renderScriptTemplate($tokens, isset($options['isPdf']) ? $options['isPdf'] : false);

        // Generate the script
        $scriptFilePath = DATA_PATH . md5(serialize($tokens)) . '.js';
        if (file_exists($scriptFilePath)) {
            unlink($scriptFilePath);
        }
        if (false === file_put_contents($scriptFilePath, $script)) {
            throw new Zend_Exception('Can not generate the phantom.js javascript file: ' . $scriptFilePath);
        }

        // Execute phantom.js to generate the image file
        $exec = ($this->_binaryPath ? $this->_binaryPath . DS : '') . $this->_executable . ' --ssl-protocol=any ' . $scriptFilePath;

        //$exec = $this->_binaryPath . DS . $this->_executable . ' --version';
        //$exec = $this->_binaryPath . DS . $this->_executable . ' direction.js';
        $escaped_command = escapeshellcmd($exec);

        // Change directory to the data path
        chdir($this->_workingDir);
        exec($escaped_command);

        //Proc_Close (Proc_Open ($this->_binaryPath . DS . $this->_executable . ' ' . $scriptFilePath . " param1 param2 &", Array (), $foo));
        //exit;

        //DebugBreak('1@127.0.0.1:7869;d=1');

        // Check whether the image is generated
        $imagePath = $this->_workingDir . DS . $targetPath;
        if (!is_file($imagePath)) {
            //throw new Zend_Exception('Failed to generate screenshot: ' . $escaped_command);
        }
        return $imagePath;
    }


    /**
     * Render the javascript file content.
     *
     * @param array $tokens
     * @return string
     */
    protected function _renderScriptTemplate($tokens, $isPdf = false)
    {
        $templateOptions = array(
            'tokens' => $tokens,
            'templateString' => $isPdf ? $this->pdfScriptTempate : $this->scriptTempate,
            'removeTokenHolderIfEmpty' => true,
            'tokenHolderPrefix' => '{{',
            'tokenHolderPostfix' => '}}'
        );
        $scriptTemplate = new Util_SimpleTemplate($templateOptions);
        return $scriptTemplate->render();
    }
}
