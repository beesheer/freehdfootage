<?php
/**
 * Manager class to handle Rackspace cloud files.
 */
class Manager_File_Rackspace extends Manager_Abstract
{
    /**
     * Each client will have its own container.
     *
     * The current client id.
     *
     * @var integer
     */
    protected $_clientId = null;

    /**
     * The rackspace file service.
     *
     * @var Zend_Service_Rackspace_Files
     */
    protected $_service = null;

    /**
     * The only available instance of Manager_File_Rackspace.
     *
     * @var Manager_File_Rackspace
     */
    protected static $_instance;

    /**
     * Container Cache.
     *
     * @var array
     */
    protected static $_containerCache = array();

    /**
     * Container URL Cache.
     *
     * @var array
     */
    protected static $_containerURLCache = array();

    /**
     * Returns an instance.
     *
     * Singleton pattern implementation.
     *
     * @return Manager_File_Rackspace
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
        $config = Zend_Registry::getInstance()->config->rackspace;
        $this->_service = new Zend_Service_Rackspace_Files($config->user, $config->key);
    }

    /**
     * Set current client id.
     *
     * @return Manager_File_Rackspace
     */
    public function setClientId($clientId)
    {
        $this->_clientId = $clientId;
        return $this;
    }

    /**
     * Save a file into a container.
     *
     * @param string $tempFilePath
     * @param string $name
     * @param array $metadata
     * @return boolean
     */
    public function saveFile($tempFilePath, $name, $metadata = array(), $contentType = null, $clientId = false)
    {
        if ($clientId) {
            $this->setClientId($clientId);
        }
        if (!file_exists($tempFilePath)) {
            return false;
        }
        $file = file_get_contents($tempFilePath);
        $container = $this->getGeneralContainer();
        $this->deleteObject($name, $container);
        $this->_service->storeObject($container, $name, $file, $metadata, $contentType);

        if (!$this->_service->isSuccessful()) {
            throw new Exception('Storing ' . $name . ' into container ' . $container . ' failed: ' . $this->_service->getErrorMsg());
            return false;
        }

        return true;
    }

    /**
     * Copy an cloud file to another cloud file.
     *
     * @param string $srcName
     * @param string $dstName
     * @return boolean
     */
    public function copyFile($srcName, $dstName, $container = false)
    {
        $container = $container ? $container : $this->getGeneralContainer();
        return $this->_service->copyObject($container, $srcName, $container, $dstName);
    }

    /**
     * Create a new container if it doesn't exists.
     *
     * @return string
     */
    public function getCurrentClientContainer()
    {
        if (empty($this->_clientId)) {
            return false;
        }
        $container = strtolower(APPLICATION_ENV) . '-client-' . $this->_clientId;
        if (!$this->_service->getContainer($container)) {
            $this->_service->createContainer($container);
            $this->_service->enableCdnContainer($container);
            if (!$this->_service->isSuccessful()) {
                throw new Exception($this->_service->getErrorMsg());
            }
        }
        return $container;
    }

    /**
     * The general container.
     *
     * @return string
     */
    public function getGeneralContainer()
    {

        $config = Zend_Registry::getInstance()->config->rackspace;
        $container = $config->container->cdn->general;
        if (!empty($this->_clientId)) {
            $container = $this->getCurrentClientContainer();
        }
        return $container;
    }

    /**
     * Get general container CDN URI base.
     *
     * @return string | false
     */
    public function getGeneralContainerCdnUri()
    {
        return $this->getContainerCdnUri($this->getGeneralContainer());
    }

    /**
     * Get a container.
     *
     * @param string $container
     * @return object
     */
    public function getContainer($container)
    {
        if (!isset(self::$_containerCache[$container])) {
            self::$_containerCache[$container] = $this->_service->getContainer($container);
        }
        return self::$_containerCache[$container];
    }

    /**
     * Get container CDN URI base.
     *
     * @param string $container
     * @return string | false
     */
    public function getContainerCdnUri($container)
    {
        if (!isset(self::$_containerURLCache[$container])) {
            $containerObj = $this->getContainer($container);
            self::$_containerURLCache[$container] = $containerObj->getCdnUri();
        }
        return self::$_containerURLCache[$container];
    }

    /**
     * Get a list of objects that starts with path prefix
     *
     * @param string $prefix
     * @return Zend_Service_Rackspace_Files_ObjectList
     */
    public function getObjectList($prefix, $container = false)
    {
        if (!$container) {
            $container = $this->getGeneralContainer();
        }
        $objectList = $this->_service->getObjects($container, array(
            'prefix' => $prefix
        ));
        return $objectList;
    }

    /**
     * Delete all the files started with prefix
     *
     * @param string $prefix
     * @return int | false Count of the objects deleted or false
     */
    public function deletePeudoFolder($prefix, $container = false)
    {
        $objectList = $this->getObjectList($prefix, $container);
        if (!$objectList->count()) {
            return false;
        }
        $deleteCount = 0;
        foreach ($objectList as $_obj) {
            if ($this->deleteObject($_obj->getName())) {
                $deleteCount++;
            }
        }
        return $deleteCount;
    }

    /**
     * Check whether file exists.
     *
     * @param string $file
     * @return boolean
     */
    public function objectExists($file, $container = false)
    {
        if (!$container) {
            $container = $this->getGeneralContainer();
        }
        $obj = $this->_service->getMetadataObject($container, $file);
        if (!$obj) {
            return false;
        }
        return true;
    }

    /**
     * Delete an object.
     *
     * @param string $file
     * @return boolean
     */
    public function deleteObject($file, $container = false)
    {
        if (!$container) {
            $container = $this->getGeneralContainer();
        }
        $this->_service->deleteObject($container, $file);
        return $this->_service->isSuccessful();
    }
}
