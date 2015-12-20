<?php
/**
 * Default ajax controller.
 */
class AjaxController extends Controller_Ajax
{
    /**
     * Get user info by user id.
     *
     */
    public function userInfoAction()
    {
        if (Zend_Auth::getInstance()->hasIdentity()) {
            $identity = Zend_Auth::getInstance()->getIdentity();
        } else {
            $this->_sendAjaxResponse();
        }
        $userInfo = array(
            'id' => $identity->id,
            'email' => $identity->email,
            'firstname' => $identity->firstname,
            'surname' => $identity->surname
        );
        $this->_responseArray = array(
            'user' => $userInfo
        );
        $this->_sendAjaxResponse();
    }

    /**
     * Get package info by id.
     *
     * Example requeset: /admin/ajax/package-info/id/1
     */
    public function packageInfoAction()
    {
        // Valid upload for a page.
        $id = $this->_request->getParam('id');
        if ($id > 0) {
            $object = new Object_Package($id);
            if (!$object->getId()) {
                $this->_responseErrorString = 'Invalid id';
                $this->_sendAjaxResponse();
            }
        }

        $info = array(
            'id' => $id,
            'name' => $object->name
        );

        $this->_responseArray = array(
            'package' => $info
        );
        $this->_sendAjaxResponse();
    }

    /**
     * Get title info by id.
     *
     * Example requeset: /admin/ajax/title-info
     */
    public function titleInfoAction()
    {
        $ids = $this->_request->getParam('id');
        if (!is_array($ids) || empty($ids)) {
            $this->_responseErrorString = 'Invalid ids';
            $this->_sendAjaxResponse();
        }
        $infos = array();
        foreach ($ids as $_id) {
            $_object = new Object_Title($_id);
            if (!$_object->getId()) {
                continue;
            }
            $_info = array(
                'id' => $_id,
                'name' => $_object->name,
                'description' => $_object->description ? $_object->description : ''
            );
            $infos[] = $_info;
        }
        $this->_responseArray = array(
            'titles' => $infos
        );
        $this->_sendAjaxResponse();
    }

    /**
     * Get page info by id.
     *
     * Example requeset: /admin/ajax/page-info
     */
    public function pageInfoAction()
    {
        $ids = $this->_request->getParam('id');
        if (!is_array($ids) || empty($ids)) {
            $this->_responseErrorString = 'Invalid ids';
            $this->_sendAjaxResponse();
        }
        $infos = array();
        foreach ($ids as $_id) {
            // It is the page id that is passed in
            $_pageId = Repo_Page::getInstance()->getIdByPageId($_id);

            $_object = new Object_Page($_pageId);
            if (!$_object->getId()) {
                continue;
            }
            $_info = array(
                'id' => $_id,
                'name' => $_object->name,
                'description' => $_object->internal_desc ? $_object->internal_desc : ''
            );
            $infos[] = $_info;
        }
        $this->_responseArray = array(
            'pages' => $infos
        );
        $this->_sendAjaxResponse();
    }
}