<?php
/**
 * Manager class to prepare a title manifest.
 */
class Manager_Manifest_Title extends Manager_Abstract
{
    /**
     * Cached page array so that we don't have to re-generate each time.
     *
     * @var integer
     */
    protected $_cacheArray = array();

    /**
     * The only available instance of Manager_Manifest_Title.
     *
     * @var Manager_Manifest_Title
     */
    protected static $_instance;

    /**
     * Returns an instance.
     *
     * Singleton pattern implementation.
     *
     * @return Manager_Manifest_Title
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
     * Get a manifest array for a title.
     *
     * @param integer $titleId
     * @return array | false
     */
    public function getManifest($titleId, $includePages = false)
    {
        if (!isset($this->_cacheArray[$titleId])) {
            $manifest = $this->_generateManifest($titleId, $includePages);
            if (!$manifest) {
                throw new Zend_Exception('Can not generate a manifest for title id: ' . $titleId);
            }
            $this->_cacheArray[$titleId] = $manifest;
        }
        return $this->_cacheArray[$titleId];
    }

    /**
     * Generate a title manifest.
     *
     * @param integer $titleId
     * @return array | false
     */
    protected function _generateManifest($titleId, $includePages = false)
    {
        $title = new Object_Title($titleId);
        if (!$title->getId()) {
            return false;
        }

        // Get the page ids.
        $pages = Repo_TitlePage::getInstance()->getTitlePages($titleId);
        $pageIds = array();
        if ($pages && $pages->count()) {
            foreach ($pages as $_p) {
                $pageIds[] = $_p->page_id;
            }
        }

        $manifest = array(
            'version' => $title->version,
            'id' => $title->id,
            'title' => $title->name,
            'type' => $title->type,
            'isLocked' => true,
            'description' => $title->description,
            'isEditable' => $title->is_editable,
            'pageIds' => $pageIds
        );
        if ($title->nav_type == 'tree') {
            $manifest['menu'] = $title->getMenu();
        }
        if ($includePages) {
            $pageManifests = array();
            if (is_array($pageIds) && !empty($pageIds)) {
                foreach ($pages as $_p) {
                    $pageManifests[] = Manager_Manifest_Page::getInstance()->getManifest($_p->id);
                }
                $manifest['pages'] = $pageManifests;
            }
        }
        if ($title->media_asset_id) {
            $media = new Object_MediaAsset($title->media_asset_id);
            $manifest['thumbnail'] = $media->getExternalLink();
        }
        return $manifest;
    }
}
