<?php
class UrlController extends Controller_Action
{
    /**
     * The default url handler.
     */
	public function indexAction()
	{
        $type = $this->_request->getParam('type');
        $hashKey = $this->_request->getParam('key');
        $repoClassName = isset(Manager_Resource_ExternalLink::$typeRepoMap[$type]) ? Manager_Resource_ExternalLink::$typeRepoMap[$type] : false;
        $objectClassName = isset(Manager_Resource_ExternalLink::$typeClassMap[$type]) ? Manager_Resource_ExternalLink::$typeClassMap[$type] : false;
        if (!$repoClassName) {
            die('Invalid resource type: ' . $type);
        }
        $objectRow = $repoClassName::getInstance()->getRowByExternalLinkKey($hashKey);
        $object = new $objectClassName($objectRow->id);
        $publicUrl = $object->getPublicLink();

        // Redirect to public url
        $this->_helper->getHelper('Redirector')
            ->gotoUrl($publicUrl);

        exit(0);
	}

    /**
     * Preview a media asset.
     *
     */
    public function previewAction()
    {
        $type = $this->_request->getParam('type');
        $hashKey = $this->_request->getParam('key');
        $repoClassName = isset(Manager_Resource_ExternalLink::$typeRepoMap[$type]) ? Manager_Resource_ExternalLink::$typeRepoMap[$type] : false;
        $objectClassName = isset(Manager_Resource_ExternalLink::$typeClassMap[$type]) ? Manager_Resource_ExternalLink::$typeClassMap[$type] : false;
        if (!$repoClassName) {
            die('Invalid resource type: ' . $type);
        }
        $objectRow = $repoClassName::getInstance()->getRowByExternalLinkKey($hashKey);
        $object = new $objectClassName($objectRow->id);
        $publicUrl = $object->getPublicLink();

        // Redirect to public url
        $this->_helper->getHelper('Redirector')
            ->gotoUrl($publicUrl);

        exit(0);
    }
}
