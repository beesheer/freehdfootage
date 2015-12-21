<?php
class TopQueriesController extends Controller_Action
{
    /**
     * Top queries.
     *
     */
    public function indexAction()
    {
        $queries = Repo_Query::getInstance()->getTopQueries();
        $this->view->rows = $queries;
    }
}
