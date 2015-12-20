<?php
class IndexController extends Controller_Action
{
    /**
     * Landing/home page.
     *
     */
	public function indexAction()
	{
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
        $this->view->videos = Repo_Video::getInstance()->getVideos($q, $listPerPage * ($pageNumber - 1), $listPerPage);
	}
}
