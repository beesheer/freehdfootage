<?php
/**
 * Manager class to prepare a package manifest.
 */
class Manager_Manifest_Package extends Manager_Abstract
{
    /**
     * Cached page array so that we don't have to re-generate each time.
     *
     * @var integer
     */
    protected $_cacheArray = array();

    /**
     * The only available instance of Manager_Manifest_Package.
     *
     * @var Manager_Manifest_Package
     */
    protected static $_instance;

    /**
     * Returns an instance.
     *
     * Singleton pattern implementation.
     *
     * @return Manager_Manifest_Package
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
     * Get a manifest array for a package.
     *
     * @param integer $packageId
     * @return array | false
     */
    public function getManifest($packageId)
    {
        if (!isset($this->_cacheArray[$packageId])) {
            $manifest = $this->_generateManifest($packageId);
            if (!$manifest) {
                throw new Zend_Exception('Can not generate a manifest for package id: ' . $packageId);
            }
            $this->_cacheArray[$packageId] = $manifest;
        }
        return $this->_cacheArray[$packageId];
    }

    /**
     * Generate a package manifest.
     *
     * @param integer $packageId
     * @return array | false
     */
    protected function _generateManifest($packageId)
    {
        $package = new Object_Package($packageId);
        if (!$package->getId()) {
            return false;
        }

        // Get all the title manifests.
        $titles = Repo_PackageTitle::getInstance()->getPackageTitles($packageId);
        $titleManifests = array();
        if ($titles && $titles->count()) {
            foreach ($titles as $_t) {
                $titleManifests[] = Manager_Manifest_Title::getInstance()->getManifest($_t->id);
            }
        }

        // Get all the page manifests.
        $pages = Repo_PackageTitle::getInstance()->getPackagePages($packageId);
        $pageManifests = array();
        if (is_array($pages) && !empty($pages)) {
            foreach ($pages as $_p) {
                $pageManifests[] = Manager_Manifest_Page::getInstance()->getManifest($_p['page_id']);
            }
        }

        $manifest = array(
            'version' => $package->version,
            'id' => $package->id,
            'title' => $package->name,
            'manifestVersion' => $package->getManifestVersion(),
            'remoteControlEnabled' => true,
            'description' => $package->description,
            'nav_type' => $package->nav_type,
            'nav_data' => $package->nav_data,
            'titles' => $titleManifests,
            'pages' => $pageManifests
        );

        if ($package->media_asset_id) {
            $media = new Object_MediaAsset($package->media_asset_id);
            $manifest['thumbnail'] = $media->getExternalLink();
        }

        return $manifest;
    }
}