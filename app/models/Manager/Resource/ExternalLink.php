<?php
/**
 * Manager class to for a resource external link.
 */
class Manager_Resource_ExternalLink extends Manager_Abstract
{
    const RESOURCE_MEDIA_ASSET = 'media_asset';


    /**
     * Repo class name map based on type.
     *
     * @array
     */
    public static $typeRepoMap = array(
        self::RESOURCE_MEDIA_ASSET => 'Repo_MediaAsset'
    );

    /**
     * Object class name map based on type.
     *
     * @array
     */
    public static $typeClassMap = array(
        self::RESOURCE_MEDIA_ASSET => 'Object_MediaAsset'
    );

    /**
     * The only available instance of Manager_Resource_ExternalLink.
     *
     * @var Manager_Resource_ExternalLink
     */
    protected static $_instance;

    /**
     * Returns an instance.
     *
     * Singleton pattern implementation.
     *
     * @return Manager_Resource_ExternalLink
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
     * Generate a complete link URL for a resource.
     *
     * @param string $resourceType
     * @param string $resourceExternalLinkHash
     * @return string
     */
    public function getLink($resourceType, $resourceExternalLinkHash, $type = 'url')
    {
        return Functions_Common::hostUrl() . '/' . $type . '/' . $resourceType . '/' . $resourceExternalLinkHash;
    }
}
