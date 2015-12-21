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
        if (!empty($q)) {
            Repo_Query::getInstance()->addNew($q);
        }
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

    /**
     * Top queries.
     *
     */
    public function topQueriesAction()
    {
        DebugBreak('1@127.0.0.1:7869;s=0,d=1,p=0,c=1');
        $queries = Repo_Query::getInstance()->getTopQueries();
        $this->view->rows = $queries;
    }
}
