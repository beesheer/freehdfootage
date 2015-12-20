<?php
/**
 * Manager class to for a resource access.
 */
class Manager_Resource_Access extends Manager_Abstract
{
    /**
     * The only available instance of Manager_Resource_Access.
     *
     * @var Manager_Resource_Access
     */
    protected static $_instance;

    /**
     * Returns an instance.
     *
     * Singleton pattern implementation.
     *
     * @return Manager_Resource_Access
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
     * Check whether the client has access according to the access client lists.
     *
     * @param integer $clientId
     * @param array $accessClients
     */
    public function clientInAccess($clientId, $accessClients)
    {
        if (!$accessClients || !is_array($accessClients) || !count($accessClients)) {
            return false;
        }
        foreach ($accessClients as $_aC) {
            if ($_aC['id'] == $clientId) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get a list of resources based on client id, resource type and resource repo class.
     *
     * @param integer $clientId
     * @param string $resourceType
     * @param string $resourceRepoClass
     * @return array
     */
    public function getClientResources($clientId, $resourceType, $resourceRepoClass, $status)
    {
        if ($clientId) {
            $parentClientIds = Repo_Client::getInstance()->getParentClients($clientId);
            $clientIds = $parentClientIds;
            $clientIds[] = $clientId;
        } else {
            $clientIds = array();
        }

        // Get all the resources
        $resources = $resourceRepoClass::getInstance()->getClientsResources($clientIds, $status);

        // Get all the access rows
        $accessRows = Repo_ResourceAccess::getInstance()->getClientResources($clientId, $resourceType);

        $allowedResources = array();

        // Filter out those not allowed for this client
        foreach ($resources as $_r) {
            if (!$clientId || $_r->client_id == $clientId || in_array($_r->client_id, $accessRows)) {
                $allowedResources[] = $_r;
            }
        }
        return $allowedResources;
    }
}
