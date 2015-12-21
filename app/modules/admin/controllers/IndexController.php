<?php
/**
 * Admin module.
 *
 * This module is the central place for all the operations for site administrator.
 */
class Admin_IndexController extends Controller_Admin_Action
{
    /**
     * The dashboard for admin.
     *
     */
	public function indexAction()
	{
        $form = new Form_Video();
        if ($this->_request->isPost())
        {
            $params = $this->_request->getPost();
            if ($form->isValid($params))
            {
                $newVideo = Repo_Video::getInstance()->addNew(
                    $form->getValue('name'),
                    $form->getValue('description'),
                    $form->getValue('youtube')
                );
            } else {
                $form->populate($params);
            }
        }
        $this->view->form = $form;

        $q = trim($this->_request->getParam('q'));
        $this->view->query = $q;
        $pageNumber = intval($this->_request->getParam('p'));
        $pageNumber = $pageNumber ? $pageNumber : 1;
        $listPerPage = 9;
        $total = Repo_Video::getInstance()->getVideosCount($q);
        $paginator = Zend_Paginator::factory($total);
        $paginator->setCurrentPageNumber($pageNumber);
        $paginator->setItemCountPerPage($listPerPage);
        $this->view->paginator = $paginator;
        $this->view->videos = Repo_Video::getInstance()->getVideos($q, $listPerPage * ($pageNumber - 1), $listPerPage, 'id DESC');
	}
}