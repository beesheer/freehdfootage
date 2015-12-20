<?php
/**
 * Admin ajax tag controller.
 */
class Admin_AjaxTagController extends Controller_Ajax
{
    /**
     * Make sure it is a valid admin session.
     */
    public function init()
    {
        parent::init();
        $identity = Zend_Auth::getInstance()->getIdentity();
        if (!$identity || !isset($identity->id)) {
            die($this->view->t->_('Please login first'));
        }
    }

    /**
     * Tag an entity.
     *
     */
    public function entityAction()
    {
        // Valid upload for a page.
        $clientId = (int)$this->_request->getParam('client_id');
        $entityType = $this->_request->getParam('entity_type');
        $entityId = $this->_request->getParam('entity_id');
        $tags = Zend_Json::decode($this->_request->getParam('tags'));

        Repo_TagEntity::getInstance()->addEntityTags($clientId, $tags, $entityType, $entityId);
        $this->_sendAjaxResponse();
    }
}