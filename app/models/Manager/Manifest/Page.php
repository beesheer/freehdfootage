<?php
/**
 * Manager class to prepare a page manifest.
 */
class Manager_Manifest_Page extends Manager_Abstract
{
    /**
     * Cached page array so that we don't have to re-generate each time.
     *
     * @var integer
     */
    protected $_cacheArray = array();

    /**
     * The only available instance of Manager_Manifest_Page.
     *
     * @var Manager_Manifest_Page
     */
    protected static $_instance;

    /**
     * Returns an instance.
     *
     * Singleton pattern implementation.
     *
     * @return Manager_Manifest_Page
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
     * Get a manifest array for a page.
     *
     * @param integer $pageId
     * @return array | false
     */
    public function getManifest($pageId)
    {
        if (!isset($this->_cacheArray[$pageId])) {
            $manifest = $this->_generateManifest($pageId);
            if (!$manifest) {
                throw new Zend_Exception('Can not generate a manifest for page id: ' . $pageId);
            }
            $this->_cacheArray[$pageId] = $manifest;
        }
        return $this->_cacheArray[$pageId];
    }

    /**
     * Generate a page manifest.
     *
     * @param integer $pageId
     * @return array | false
     */
    protected function _generateManifest($pageId)
    {
        $page = new Object_Page($pageId);
        if (!$page->getId()) {
            return false;
        }

        $pageVersion = $page->getMostLatestApprovedVersionAsActive();
        $version = $pageVersion ? $pageVersion->version : false;
        $cloudFileContainer = $pageVersion ? $pageVersion->cloud_file_container : $page->cloud_file_container;

        if ($cloudFileContainer) {
            $files = $page->getCloudFilePaths($version, $cloudFileContainer);
        } else {
            // Get the files
            $files = $this->_folderFilePaths($page->getStaticContentFolder() . DS . $page->page_id, $page->page_id . '.html');
        }

        // Prepare the pdf templates
        $pdfTemplateRows = Repo_PagePdfTemplate::getInstance()->getPagePdfTemplates($page->getId());
        $pdfTemplates = array();
        if ($pdfTemplateRows && $pdfTemplateRows->count()) {
            foreach ($pdfTemplateRows as $_tRow) {
                $_template = new Object_PdfTemplate($_tRow->id);
                $pdfTemplates[] = array(
                    'id' => $_template->getId(),
                    'name' => $_template->name,
                    'tokens' => $_template->getTokens(),
                    'preview' => $_template->getManifestPreviewFilePath()
                );
            }
        }

        return array(
            'version' => $pageVersion ? $pageVersion->version : $page->version,
            'id' => $page->page_id,
            'uid' => $page->id,
            'legalPageUid' => (int)$page->legal_page_id > 0 ? (int)$page->legal_page_id : '',
            'linkedPageUids' => $page->getManifestPageGroupPages(),
            'title' => $page->name,
            'fileType' => 'htmlEdge',
            'type' => 'html',
            'typename' => $page->type,
            'baseHost' => $page->getBaseHost($cloudFileContainer),
            'baseUri' => $page->getBaseUrl($version),
            'hash' => Manager_Page_StaticContent::getPageFolderName($page->page_id, $version),
            'instructions' => $page->description ? $page->description : '',
            'description' => $page->internal_desc ? $page->internal_desc : '',
            'navigation' => array(),
            'language' => $page->page_language,
            'defaultPath' => $page->page_id . '.html',
            'width' => $page->getWidth(),
            'height' => $page->getHeight(),
            'transcript' => $page->transcript ? $page->transcript : '',
            'audio' => $page->audio_url,
            'filePaths' => $files,
            'navigation' => $page->navigation ? unserialize($page->navigation) : array(),
            'content_type' => $page->content_type ? Repo_Page::$contentTypeLabels[$page->content_type] : Repo_Page::$contentTypeLabels[Repo_Page::PAGE_CONTENT_TYPE_DEFAULT],
            'editor_behaviors' => $page->editor_behaviors ? $page->editor_behaviors : '',
            'pdf_templates' => $pdfTemplates,
            'tags' => $page->getTags()
        );
    }

    /**
     * Write the transcript file to the page folder
     */
    protected function _writeTranscript($path, $text) {
        $filePath =  $path;
        $f = fopen($filePath, "w");
        $old = umask(0);
        fwrite($f, $text);
        umask($old);
        fclose($f);
        chmod($filePath, 0777);
    }

    /**
     * Get an flat array of folder files.
     *
     * @param string $pageFolder Folder path
     * @return array | false
     */
    protected function _folderFilePaths($pageFolder, $indexPagePath)
    {
        $files = array();
        if (!file_exists($pageFolder) || !is_dir($pageFolder)) {
            return $files;
        }
        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($pageFolder), RecursiveIteratorIterator::SELF_FIRST);
        while($it->valid()) {
            if (!$it->isDot() && !$it->isDir()) {
                // We account for windows server issue.
                $_path = str_replace(DS, '/', $it->getSubPathName());
                if ($_path != $indexPagePath && array_search($_path, $files) === false && $this->_pathValid($_path)) {
                    $files[] = $_path;
                }
            }
            $it->next();
        }
        return $files;
    }

    /**
     * We only return valid paths. For example, those should be filtered out: .DS_Store
     *
     * @param string $path
     * @return boolean
     */
    protected function _pathValid($path)
    {
        $filePathes = explode('/', $path);
        $fileName = array_pop($filePathes);
        $fileNames = explode('.', $fileName);
        if (count($fileNames) < 2) {
            return false;
        }
        if (count($fileNames) == 2 && stristr($fileNames[0], '*') !== false) {
            return false;
        }
        if (empty($fileNames[0])) {
            return false;
        }
        return true;
    }
}
