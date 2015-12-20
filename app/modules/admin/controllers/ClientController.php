<?php

/**
 * Client management in admin module.
 *
 * This module is the central place for all the operations for managing clients.
 */
class Admin_ClientController extends Controller_Admin_Action {

    /**
     * List all clients.
     */
    public function indexAction() {
        $this->view->clients = Repo_Client::getInstance()->getClients();
    }

    /**
     * Client details.
     *
     */
    public function detailAction()
    {
        $id = $this->_request->getParam('id');
        $client = new Object_Client($id);
        $clientId = $client->getId();
        if (empty($clientId)) {
            // No client defined, redirec to list users.
            $this->_redirect('/admin/client');
            return false;
        }

        $this->_setClientContext($clientId);

        $this->view->client = $client;

        // Edit form
        $form = new Form_Admin_Client_Create();
        $form->setClient($client);
        $form->setAttrib('id', 'edit-client-form');
        $this->view->form = $form;
    }

    /**
     * List client users.
     *
     */
    public function userAction()
    {
        $clientId = $this->_request->getParam('client');
        if ($clientId > 0) {
            $client = new Object_Client($clientId);
            $this->view->clientName = $client->name;
        }
        // js for list sort and search
        $this->view->headScript()->prependFile('/js/libraries/list/list.min.js');

        //js for bootstrap style pagination
        $this->view->headScript()->prependFile('/js/libraries/list/list.pagination.min.js');
        $this->view->users = Repo_User::getInstance()->getClientUsers($clientId);
    }

    /**
     * List client meetings.
     *
     */
    public function meetingAction() {
        $clientId = $this->_request->getParam('client');
        if ($clientId > 0) {
            $client = new Object_Client($clientId);
            $this->view->clientName = $client->name;
        }
    }

    /**
     * List client contacts.
     *
     */
    public function contactAction()
    {
        $clientId = $this->_request->getParam('client');
        if ($clientId > 0)
        {
            $client = new Object_Client($clientId);
            $this->view->clientName = $client->name;
        }
    }
    /**
     * User detail page: edit/delete functionality.
     *
     * Also will include permissions, packages, groups etc.
     *
     * It is the central place for a user object.
     */
    public function userDetailAction()
    {
        $userId = $this->_request->getParam('id');
        $user = new Object_User($userId);
        $userId = $user->getId();
        if (empty($userId))
        {
            // No user defined, redirec to list users.
            $this->_redirect('/admin/client/user');
            return false;
        }
        $form = new Form_Admin_Client_User_Create(false, array('user' => $user));
        // Check for user update
        if ($this->_request->isPost())
        {
            $params = $this->_request->getPost();
            if ($form->isValid($params))
            {
                // Update user if necessary
                $form->updateUser($user);
                $form->setUser($user);
            }
            else
            {
                $form->populate($params);
            }
        }
        $emailSignatureId = Repo_UserEmailSignature::getInstance()->getIdByUserId($userId);
        $this->view->userEmailSignature =  new Object_UserEmailSignature($emailSignatureId);;

        $this->view->emailSignatureId = $emailSignatureId;
        $this->view->user = $user;
        $this->view->client = new Object_Client($user->client_id);
        $this->view->form = $form;

        $this->view->userPackages = Repo_UserPackage::getInstance()->getUserPackages($userId);
        $this->view->userTeams = Repo_TeamUser::getInstance()->getUserTeams($userId);
        $this->view->userApps = Repo_AppUser::getInstance()->getUserApps($userId);

        $this->view->userDeviceIds = Repo_UserDevice::getInstance()->getUserDeviceIds($userId);
    }

    /**
     * List client team.
     *
     */
    public function teamAction()
    {
        $clientId = $this->_request->getParam('client');
        if ($clientId > 0) {
            $client = new Object_Client($clientId);
            $this->view->client = $client;
        }
        $this->view->teams = Repo_Team::getInstance()->getClientTeams($clientId);
    }

    /**
     * Create a new team.
     *
     */
    public function newTeamAction() {
        $form = new Form_Admin_Client_Team();
        // Check for post to create new team.
        if ($this->_request->isPost()) {
            $params = $this->_request->getPost();
            if ($form->isValid($params)) {
                // Create a new team
                $tId = Repo_Team::getInstance()->addNew(
                        $form->getValue('client'), $form->getValue('name'), $form->getValue('status'), $form->getValue('description')
                );
                // Redirect to package detail page
                $this->_redirect('/admin/client/team-detail/id/' . $tId);
            } else {
                $form->populate($params);
            }
        } else {
            // Set client context
            if ($this->_currentClientId) {
                $form->getElement('client')->setValue($this->_currentClientId);
            }
        }

        $this->view->form = $form;
    }

    /**
     * Team detail page: edit/delete functionality. With the UI to add package and user to a team.
     *
     * It is the central place for a team object.
     */
    public function teamDetailAction() {
        $id = $this->_request->getParam('id');
        $team = new Object_Team($id);
        $teamId = $team->getId();
        if (empty($teamId)) {
            // No team defined, redirect to list.
            $this->_redirect('/admin/client/team');
            return false;
        }
        $form = new Form_Admin_Client_Team(false, array('team' => $team));

        // Check for team update
        if ($this->_request->isPost()) {
            $params = $this->_request->getPost();
            if ($form->isValid($params)) {
                // Update team if necessary
                $form->updateTeam($team);
                $form->setTeam($team);
            } else {
                $form->populate($params);
            }
        }

        $this->view->team = $team;
        $this->view->client = new Object_Client($team->client_id);
        $this->view->form = $form;

        // Team packages and users
        $this->view->teamPackages = Repo_TeamPackage::getInstance()->getTeamPackages($teamId);
        $this->view->teamUsers = Repo_TeamUser::getInstance()->getTeamUsers($teamId);
        $this->view->teamTeams = Repo_TeamTeam::getInstance()->getTeamChildren($teamId);
    }

    /**
     * List client app.
     *
     */
    public function appAction() {
        $clientId = $this->_request->getParam('client');
        if ($clientId > 0) {
            $client = new Object_Client($clientId);
            $this->view->client = $client;
        }
        $this->view->apps = Repo_App::getInstance()->getClientApps($clientId);
    }

    /**
     * Create a new app.
     *
     */
    public function newAppAction() {
        $form = new Form_Admin_Client_App();
        // Check for post to create new.
        if ($this->_request->isPost()) {
            $params = $this->_request->getPost();
            if ($form->isValid($params)) {
                // Create a new team
                $aId = Repo_App::getInstance()->addNew(
                        $form->getValue('client'), $form->getValue('name'), $form->getValue('app_key'), $form->getValue('description')
                );
                // Redirect to package detail page
                $this->_redirect('/admin/client/app-detail/id/' . $aId);
            } else {
                $form->populate($params);
            }
        } else {
            // Set client context
            if ($this->_currentClientId) {
                $form->getElement('client')->setValue($this->_currentClientId);
            }
        }

        $this->view->form = $form;
    }

    /**
     * App detail page: edit/delete functionality. With the UI to add package and user to an app.
     *
     * It is the central place for an app object.
     */
    public function appDetailAction() {
        $id = $this->_request->getParam('id');
        $app = new Object_App($id);
        $appId = $app->getId();
        if (empty($appId)) {
            // Not defined, redirect to list.
            $this->_redirect('/admin/client/app');
            return false;
        }
        $form = new Form_Admin_Client_App(false, array('object' => $app));

        // Check for package update
        if ($this->_request->isPost()) {
            $params = $this->_request->getPost();
            if ($form->isValid($params)) {
                // Update package if necessary
                $form->updateObject($app);
                $form->setObject($app);
            } else {
                $form->populate($params);
            }
        }

        $this->view->app = $app;
        $this->view->client = new Object_Client($app->client_id);
        $this->view->form = $form;

        // Package titles and libraries
        $this->view->appPackages = Repo_AppPackage::getInstance()->getAppPackages($appId);
        $this->view->appUsers = Repo_AppUser::getInstance()->getAppUsers($appId);
    }

    /**
     * Bulk upload multiple pages with a zip file.
     *
     */
    public function uploadPagesAction() {
        $id = $this->_request->getParam('id');
        $client = new Object_Client($id);
        $clientId = $client->getId();
        if (empty($clientId)) {
            // No client defined, redirec to list users.
            $this->_redirect('/admin/client');
            return false;
        }
        $this->view->client = $client;

        $form = new Form_Admin_Client_Page_BulkUpload(false, array('clientId' => $id));
        if ($this->_request->isPost()) {
            $params = $this->_request->getPost();
            if ($form->isValid($params)) {
                $pages = $form->generatePages($id);
                if (!empty($pages)) {
                    // Redirect to client page list
                    $this->_helper->getHelper('FlashMessenger')->addMessage(count($pages) . ' pages have been created/updated.');

                    $titleId = $params['title'];
                    if (!empty($titleId) && (int) $titleId > 0) {
                        $titlePages = array();
                        // Added to a title if title is available
                        $title = new Object_Title($titleId);
                        $existingPages = Repo_TitlePage::getInstance()->getTitlePages($title->getId());
                        if ($existingPages) {
                            foreach ($existingPages as $_p) {
                                $titlePages[] = $_p->id;
                            }
                        }
                        $titlePages = array_merge($titlePages, $pages);
                        $title->savePages($titlePages);
                        $this->_helper->getHelper('FlashMessenger')->addMessage('Pages have been added to title: <a href="/admin/client/title-detail/id/' . $title->id . '">' . $title->name . '</a>.');
                    }

                    $this->_redirect('/admin/client/page/client/' . $id);
                }
                $form->populate($params);
            } else {
                $form->populate($params);
            }
        }
        $this->view->form = $form;
    }

    /**
     * List client pages.
     *
     */
    public function pageAction() {
        $clientId = $this->_request->getParam('client');
        $status = $this->_request->getParam('status');
        if ($clientId > 0) {
            $client = new Object_Client($clientId);
            $this->view->client = $client;
        }

        $this->view->pages = Repo_Page::getInstance()->getClientPages($clientId, $status);
        $this->view->status = $status;

        // js for list sort and search
        $this->view->headScript()->prependFile('/js/libraries/list/list.min.js');

        //js for bootstrap style pagination
        $this->view->headScript()->prependFile('/js/libraries/list/list.pagination.min.js');
    }

    /**
     * Create a new page.
     *
     */
    public function newPageAction() {
        $form = new Form_Admin_Client_Page();
        // Check for user update
        if ($this->_request->isPost()) {
            $params = $this->_request->getPost();
            if ($form->isValid($params)) {
                // Create a new page
                $pageId = Repo_Page::getInstance()->addNew(
                    $form->getValue('client'), $form->getValue('name'), $form->getValue('type'), $form->getValue('status'), $form->getValue('description'), false, false, false, false, $form->getValue('lang'), $form->getValue('page_id')
                );
                if ((int) $pageId == 0) {
                    // Create failed
                    $form->getElement('page_id')->addError($pageId);
                    $form->populate($params);
                } else {
                    // Create static content if needed
                    $form->updateStaticContent($pageId, false);

                    // Navigation
                    $page = new Object_Page($pageId);
                    $form->updatePageNavigation($page);
                    // Redirect to page detail page
                    $this->_redirect('/admin/client/page-detail/id/' . $pageId);
                }
            } else {
                $form->populate($params);
            }
        } else {
            // Set client context
            if ($this->_currentClientId) {
                $form->getElement('client')->setValue($this->_currentClientId);
            }
        }
        $this->view->form = $form;

        // Extra js for file upload
        $this->view->headScript()->prependFile('/js/libraries/jquery/jquery.fileupload.js');
        $this->view->headScript()->prependFile('/js/libraries/jquery/jquery.iframe-transport.js');
        $this->view->headLink()->appendStylesheet('/css/jquery.fileupload.css');

        // Page navigation
        $this->view->headScript()->prependFile('/js/PageNavigationUi.js');
    }

    /**
     * Page detail page: edit/delete functionality.
     *
     * It is the central place for a page object.
     */
    public function pageDetailAction() {
        $id = $this->_request->getParam('id');
        $page = new Object_Page($id);
        $pageId = $page->getId();
        if (empty($pageId)) {
            // No page defined, redirec to list page.
            $this->_redirect('/admin/client/page');
            return false;
        }

        // Whenever a page is visited, refresh the thumb.
        $page->refreshThumb();

        $form = new Form_Admin_Client_Page(false, array('page' => $page));

        // Check for page update
        if ($this->_request->isPost()) {

            $params = $this->_request->getPost();

            if( $params['form-object-tag-list'] ) {
                Repo_TagEntity::getInstance()->removeEntityTags($id, 'page');
                $tags = Zend_Json::decode($params['form-object-tag-list']);
                if (!empty($tags[0])) {
                    Repo_TagEntity::getInstance()->addEntityTags($page->client_id, $tags, 'page', $id);
                    $this->view->updateMessage = "Tags saved";
                }

            } else {
                if ($form->isValid($params)) {
                    // Update page if necessary
                    $form->updatePage($page);
                    $form->updatePageNavigation($page);
                    $form->setPage($page);
                    $form->updateStaticContent($pageId, true);
                } else {
                    $form->populate($params);
                }
            }
        }

        $this->view->lang = $page->getPageLanguage();
        $this->view->page = $page;
        $this->view->client = new Object_Client($page->client_id);
        $this->view->form = $form;
        // Survey ad page questions
        $this->view->surveyQuestions = Repo_Survey::getInstance()->getSurveyQuestions();
        $this->view->pageQuestions = Repo_PageQuestion::getInstance()->getPageQuestionGroup($pageId);

        // Extra js for file upload
        $this->view->headScript()->prependFile('/js/libraries/jquery/jquery.fileupload.js');
        $this->view->headScript()->prependFile('/js/libraries/jquery/jquery.iframe-transport.js');
        $this->view->headLink()->appendStylesheet('/css/jquery.fileupload.css');

        // get tags
        $this->view->tagTree        = Repo_Tag::getInstance()->getTagTree( $page->client_id );
        $this->view->assetTags      = Manager_Tag_Helper::getInstance()->getTagIds( "page", $id );
        $this->view->assetTagNames  = $page->getTags();

        //Tag management js
        $this->view->headScript()->prependFile('/js/managers/TagManager.js');

        // js for list sort and search
        $this->view->headScript()->prependFile('/js/libraries/list/list.min.js');

        // Page navigation
        $this->view->headScript()->prependFile('/js/PageNavigationUi.js');

        // Page pdf template
        $this->view->pagePdfTemplates = Repo_PagePdfTemplate::getInstance()->getPagePdfTemplates($pageId);

        // Page versions
        $this->view->pageVersions = Repo_PageVersion::getInstance()->getPageVersions($pageId);
    }


    /**
     * Linked page.
     *
     * It is the central place for setting up linked pages (page group)
     */
    public function pageGroupAction()
    {
        $id = $this->_request->getParam('id');
        $page = new Object_Page($id);
        $pageId = $page->getId();
        if (empty($pageId)) {
            // No page defined, redirec to list.
            $this->_redirect('/admin/client/page');
            return false;
        }

        $this->view->page = $page;

        // Find page group
        $pageGroupId = Repo_PageGroup::getInstance()->getPageGroupIdForPage($pageId);

        // Title pages
        $this->view->pageGroupPages = Repo_PageGroup::getInstance()->getPageGroupPages($pageGroupId, $pageId);
        $this->view->clientPages = Repo_PageGroup::getInstance()->getClientRemainPages($page->client_id, $this->view->pageGroupPages);
    }

    /**
     * Get the manifest for a page. TESTING ONLY.
     *
     */
    public function pageManifestAction() {
        $id = $this->_request->getParam('id');
        $manifest = Manager_Manifest_Page::getInstance()->getManifest($id);
        echo Zend_Json::encode($manifest);
        die;
    }

    /**
     * Get the manifest for a title. TESTING ONLY.
     *
     */
    public function titleManifestAction() {
        $id = $this->_request->getParam('id');
        $manifest = Manager_Manifest_Title::getInstance()->getManifest($id);
        echo Zend_Json::encode($manifest);
        die;
    }

    /**
     * Get the manifest for a package. TESTING ONLY.
     *
     */
    public function packageManifestAction() {
        $id = $this->_request->getParam('id');
        if ($id) {
            $manifest = Manager_Manifest_Package::getInstance()->getManifest($id);
        } else {
            $manifestId = $this->_request->getParam('manifest');
            if ($manifestId) {
                $manifestRow = Repo_PackageManifest::getInstance()->findRow($manifestId);
                if ($manifestRow && $manifestRow->data) {
                    $manifest = unserialize($manifestRow->data);
                }
            }
        }
        echo Zend_Json::encode($manifest);
        die;
    }

    /**
     * List client pdf templates.
     *
     */
    public function pdfTemplateAction() {
        $clientId = $this->_request->getParam('client');
        if ($clientId > 0) {
            $client = new Object_Client($clientId);
            $this->view->client = $client;
        }
        $this->view->templates = Repo_PdfTemplate::getInstance()->getClientPdfTemplates($clientId);
    }

    /**
     * Create a new title.
     *
     */
    public function newPdfTemplateAction() {

        $form = new Form_Admin_Client_PdfTemplate();
        // Check for post to create new title.
        if ($this->_request->isPost()) {
            $params = $this->_request->getPost();
            if ($form->isValid($params)) {
                // Create a new pdf template
                $tId = Repo_PdfTemplate::getInstance()->addNew(
                        $form->getValue('client'), $form->getValue('name'), $form->getValue('template'), Zend_Auth::getInstance()->getIdentity()->id
                );
                $form->receiveStaticFile($tId);
                // Redirect to title detail page
                $this->_redirect('/admin/client/pdf-template-detail/id/' . $tId);
            } else {
                $form->populate($params);
            }
        } else {
            // Set client context
            if ($this->_currentClientId) {
                $form->getElement('client')->setValue($this->_currentClientId);
            }
        }
        $this->view->form = $form;

        // CKEditor js
        $this->view->headScript()->prependFile('//cdn.ckeditor.com/4.4.3/standard/ckeditor.js');
    }

    /**
     * Pdf template detail page: edit/delete functionality.
     *
     * It is the central place for a Pdf template object.
     */
    public function pdfTemplateDetailAction() {
        $id = $this->_request->getParam('id');
        $template = new Object_PdfTemplate($id);
        $tId = $template->getId();
        if (empty($tId)) {
            // No defined, redirec to list.
            $this->_redirect('/admin/client/pdf-template');
            return false;
        }
        $form = new Form_Admin_Client_PdfTemplate(false, array('object' => $template));

        // Check for user update
        if ($this->_request->isPost()) {
            $params = $this->_request->getPost();
            if ($form->isValid($params)) {
                // Update user if necessary
                $form->updateObject($template);
                $form->setObject($template);
                $form->receiveStaticFile($tId);
            } else {
                $form->populate($params);
            }
        }

        $this->view->template = $template;
        $this->view->client = new Object_Client($template->client_id);
        $this->view->form = $form;

        // CKEditor js
        $this->view->headScript()->prependFile('//cdn.ckeditor.com/4.4.3/standard/ckeditor.js');
    }

    /**
     * List client page templates.
     *
     */
    public function pageTemplateAction() {
        $clientId = $this->_request->getParam('client');
        if ($clientId > 0) {
            $client = new Object_Client($clientId);
            $this->view->client = $client;
        }
        $this->view->templates = Repo_PageTemplate::getInstance()->getClientPageTemplates($clientId);
    }

    /**
     * Create a new title.
     *
     */
    public function newPageTemplateAction() {

        $form = new Form_Admin_Client_PageTemplate();
        // Check for post to create new title.
        if ($this->_request->isPost()) {
            $params = $this->_request->getPost();
            if ($form->isValid($params)) {
                // Create a new pdf template
                $tId = Repo_PageTemplate::getInstance()->addNew(
                        $form->getValue('client'), $form->getValue('name'), $form->getValue('template'), Zend_Auth::getInstance()->getIdentity()->id
                );
                // Redirect to title detail page
                $this->_redirect('/admin/client/page-template-detail/id/' . $tId);
            } else {
                $form->populate($params);
            }
        } else {
            // Set client context
            if ($this->_currentClientId) {
                $form->getElement('client')->setValue($this->_currentClientId);
            }
        }
        $this->view->form = $form;

        // CKEditor js
        $this->view->headScript()->prependFile('/js/libraries/ckeditor/ckeditor.js');
    }

    /**
     * Page template detail page: edit/delete functionality.
     *
     * It is the central place for a Page template object.
     */
    public function pageTemplateDetailAction() {
        $id = $this->_request->getParam('id');
        $template = new Object_PageTemplate($id);
        $tId = $template->getId();
        if (empty($tId)) {
            // No defined, redirec to list.
            $this->_redirect('/admin/client/page-template');
            return false;
        }
        $form = new Form_Admin_Client_PageTemplate(false, array('object' => $template));

        // Check for user update
        if ($this->_request->isPost()) {
            $params = $this->_request->getPost();
            if ($form->isValid($params)) {
                // Update user if necessary
                $form->updateObject($template);
                $form->setObject($template);
            } else {
                $form->populate($params);
            }
        }

        $this->view->template = $template;
        $this->view->client = new Object_Client($template->client_id);
        $this->view->form = $form;

        // CKEditor js
        $this->view->headScript()->prependFile('/js/libraries/ckeditor/ckeditor.js');

        // Extra js for angular js
        $this->view->headScript()->prependFile('/js/libraries/angular/ui-bootstrap-tpls-0.12.0.min.js');
        $this->view->headScript()->prependFile('/js/libraries/angular/angular.min.js');

        // Angular files
        $this->view->headScript()->appendFile('/js/angular-module/page-template/controllers/page-template-detail.js');

        // Extra js for file upload
        $this->view->headScript()->prependFile('/js/libraries/jquery/jquery.fileupload.js');
        $this->view->headScript()->prependFile('/js/libraries/jquery/jquery.iframe-transport.js');
        $this->view->headLink()->appendStylesheet('/css/jquery.fileupload.css');
    }

    /**
     * List client titles.
     *
     */
    public function titleAction() {
        $clientId = $this->_request->getParam('client');
        $status = $this->_request->getParam('status');
        if ($clientId > 0) {
            $client = new Object_Client($clientId);
            $this->view->client = $client;
        }
        $this->view->titles = Repo_Title::getInstance()->getClientTitles($clientId, $status);
        $this->view->status = $status;
    }

    /**
     * Create a new title.
     *
     */
    public function newTitleAction() {
        $form = new Form_Admin_Client_Title();
        // Check for post to create new title.
        if ($this->_request->isPost()) {
            $params = $this->_request->getPost();
            if ($form->isValid($params)) {
                // Create a new title
                $tId = Repo_Title::getInstance()->addNew(
                        $form->getValue('client'), $form->getValue('name'), $form->getValue('status'), $form->getValue('type'), $form->getValue('nav_type'), $form->getValue('description'), $form->getValue('is_editable')
                );
                // Redirect to title detail page
                $this->_redirect('/admin/client/title-detail/id/' . $tId);
            } else {
                $form->populate($params);
            }
        } else {
            // Set client context
            if ($this->_currentClientId) {
                $form->getElement('client')->setValue($this->_currentClientId);
            }
        }
        $this->view->form = $form;
    }

    /**
     * Title detail page: edit/delete functionality. With the UI to add page to a title.
     *
     * It is the central place for a title object.
     */
    public function titleDetailAction() {
        $id = $this->_request->getParam('id');
        $title = new Object_Title($id);
        $titleId = $title->getId();
        if (empty($titleId)) {
            // No title defined, redirec to list.
            $this->_redirect('/admin/client/title');
            return false;
        }
        $form = new Form_Admin_Client_Title(false, array('title' => $title));

        // Check for user update
        if ($this->_request->isPost()) {
            $params = $this->_request->getPost();
            if ($form->isValid($params)) {
                // Update user if necessary
                $form->updateTitle($title);
                $form->setTitle($title);
            } else {
                $form->populate($params);
            }
        }

        $this->view->title = $title;
        $this->view->client = new Object_Client($title->client_id);
        $this->view->form = $form;

        // Title pages
        $this->view->titlePages = Repo_TitlePage::getInstance()->getTitlePages($titleId);
        $this->view->clientPages = Repo_TitlePage::getInstance()->getClientRemainPagesForTitle($title->client_id, $this->view->titlePages);

        // Extra js for file upload
        $this->view->headScript()->prependFile('/js/libraries/jquery/jquery.fileupload.js');
        $this->view->headScript()->prependFile('/js/libraries/jquery/jquery.iframe-transport.js');
        $this->view->headLink()->appendStylesheet('/css/jquery.fileupload.css');
    }

    /**
     * Title page tree page: edit title menu if the title type is a menu.
     *
     * It is the central place for a title menu
     */
    public function titleMenuAction() {
        $id = $this->_request->getParam('id');
        $title = new Object_Title($id);
        $titleId = $title->getId();
        if (empty($titleId)) {
            // No title defined, redirec to list.
            $this->_redirect('/admin/client/title');
            return false;
        }

        $this->view->title = $title;
        $this->view->client = new Object_Client($title->client_id);

        // Title pages
        $this->view->titlePages = Repo_TitlePage::getInstance()->getTitlePages($titleId);

        // Create/edit form in the modal
        $this->view->nodeForm = new Form_Admin_Client_Title_MenuNode(false, array(
            'pages' => $this->view->titlePages
        ));

        // Fancy tree library
        $this->view->headScript()->appendFile('/js/libraries/fancytree/jquery.fancytree-all.min.js');
        $this->view->headLink()->appendStylesheet('/js/libraries/fancytree/skin-lion/ui.fancytree.min.css');
    }

    /**
     * List client libraries.
     *
     */
    public function libraryAction() {
        $clientId = $this->_request->getParam('client');
        $status = $this->_request->getParam('status');
        if ($clientId > 0) {
            $client = new Object_Client($clientId);
            $this->view->client = $client;
        }
        $this->view->libraries = Repo_Library::getInstance()->getClientLibraries($clientId, $status);
        $this->view->status = $status;
    }

    /**
     * Create a new library.
     *
     */
    public function newLibraryAction() {
        $form = new Form_Admin_Client_Library();
        // Check for post to create new library.
        if ($this->_request->isPost()) {
            $params = $this->_request->getPost();
            if ($form->isValid($params)) {
                // Create a new library
                $lId = Repo_Library::getInstance()->addNew(
                        $form->getValue('client'), $form->getValue('name'), $form->getValue('status'), $form->getValue('type'), $form->getValue('description')
                );
                // Redirect to library detail page
                $this->_redirect('/admin/client/library-detail/id/' . $lId);
            } else {
                $form->populate($params);
            }
        }
        $this->view->form = $form;
    }

    /**
     * Library detail page: edit/delete functionality. With the UI to add page to a library.
     *
     * It is the central place for a library object.
     */
    public function libraryDetailAction() {
        $id = $this->_request->getParam('id');
        $library = new Object_Library($id);
        $libraryId = $library->getId();
        if (empty($libraryId)) {
            // No title defined, redirec to list.
            $this->_redirect('/admin/client/library');
            return false;
        }
        $form = new Form_Admin_Client_Library(false, array('library' => $library));

        // Check for user update
        if ($this->_request->isPost()) {
            $params = $this->_request->getPost();
            if ($form->isValid($params)) {
                // Update user if necessary
                $form->updateLibrary($library);
                $form->setLibrary($library);
            } else {
                $form->populate($params);
            }
        }

        $this->view->library = $library;
        $this->view->client = new Object_Client($library->client_id);
        $this->view->form = $form;

        // Title pages
        $this->view->libraryPages = Repo_LibraryPage::getInstance()->getLibraryPages($libraryId);
        $this->view->clientPages = Repo_LibraryPage::getInstance()->getClientRemainPagesForLibrary($library->client_id, $this->view->libraryPages);
    }

    /**
     * List client package.
     *
     */
    public function packageAction() {
        $clientId = $this->_request->getParam('client');
        $status = $this->_request->getParam('status');
        if ($clientId > 0) {
            $client = new Object_Client($clientId);
            $this->view->client = $client;
        }
        $this->view->packages = Repo_Package::getInstance()->getClientPackages($clientId, $status);
        $this->view->status = $status;
    }

    /**
     * Create a new package.
     *
     */
    public function newPackageAction() {
        $form = new Form_Admin_Client_Package();
        // Check for post to create new library.
        if ($this->_request->isPost()) {
            $params = $this->_request->getPost();
            if ($form->isValid($params)) {
                // Create a new package
                $pId = Repo_Package::getInstance()->addNew(
                        $form->getValue('client'), $form->getValue('name'), $form->getValue('status'), $form->getValue('description'), $form->getValue('play_audio')
                );
                // Redirect to package detail page
                $this->_redirect('/admin/client/package-detail/id/' . $pId);
            } else {
                $form->populate($params);
            }
        } else {
            // Set client context
            if ($this->_currentClientId) {
                $form->getElement('client')->setValue($this->_currentClientId);
            }
        }
        $this->view->form = $form;
    }

    /**
     * Package detail page: edit/delete functionality. With the UI to add title and library to a package.
     *
     * It is the central place for a package object.
     */
    public function packageDetailAction() {
        $id = $this->_request->getParam('id');
        $package = new Object_Package($id);
        $packageId = $package->getId();
        if (empty($packageId)) {
            // No package defined, redirect to list.
            $this->_redirect('/admin/client/package');
            return false;
        }
        $form = new Form_Admin_Client_Package(false, array('package' => $package));

        // Check for package update
        if ($this->_request->isPost()) {
            $params = $this->_request->getPost();
            if ($form->isValid($params)) {
                // Update package if necessary
                $form->updatePackage($package);
                $form->setPackage($package);
            } else {
                $form->populate($params);
            }
        }

        $this->view->package = $package;
        $this->view->client = new Object_Client($package->client_id);
        $this->view->form = $form;

        // Package contents
        $this->view->packageTitles = Repo_PackageTitle::getInstance()->getPackageTitles($packageId);
        $this->view->packageApps = Repo_AppPackage::getInstance()->getPackageApps($packageId);

        // Extra js for file upload
        $this->view->headScript()->prependFile('/js/libraries/jquery/jquery.fileupload.js');
        $this->view->headScript()->prependFile('/js/libraries/jquery/jquery.iframe-transport.js');
        $this->view->headLink()->appendStylesheet('/css/jquery.fileupload.css');
    }

    /**
     * Package navigation page: edit package navigation if the nav type is a tree.
     *
     * It is the central place for package nav.
     */
    public function packageNavAction()
    {
        $id = $this->_request->getParam('id');
        $package = new Object_Package($id);
        $packageId = $package->getId();
        if (empty($packageId)) {
            // No title defined, redirec to list.
            $this->_redirect('/admin/client/package');
            return false;
        }

        $this->view->package = $package;
        $this->view->client = new Object_Client($package->client_id);

        // Package titles
        $this->view->titles = Repo_PackageTitle::getInstance()->getPackageTitles($packageId);

        // Create/edit form in the modal
        $this->view->nodeForm = new Form_Admin_Client_Package_NavFolder();

        // Fancy tree library
        $this->view->headScript()->appendFile('/js/libraries/fancytree/jquery.fancytree-all.min.js');
        $this->view->headLink()->appendStylesheet('/js/libraries/fancytree/skin-lion/ui.fancytree.min.css');
    }

    /**
     * List forms.
     *
     */
    public function formAction() {
        $clientId = $this->_request->getParam('client');
        $status = $this->_request->getParam('status');
        if ($clientId > 0) {
            $client = new Object_Client($clientId);
            $this->view->client = $client;
        }

        $this->view->forms = Repo_Form::getInstance()->getClientForms($clientId, $status);
        $this->view->status = $status;
    }

    /**
     * Create a new form.
     *
     */
    public function newFormAction() {
        $form = new Form_Admin_Client_Form();
        // Check for post to create new form.
        if ($this->_request->isPost()) {
            $params = $this->_request->getPost();
            if ($form->isValid($params)) {
                // Create a new form
                $tId = Repo_Form::getInstance()->addNew(
                        $form->getValue('page'), $form->getValue('client'), $form->getValue('name'), $form->getValue('status'), $form->getValue('text')
                );
                // Redirect to form detail page
                $this->_redirect('/admin/client/form-detail/id/' . $tId);
            } else {
                $form->populate($params);
            }
        }
        $this->view->form = $form;
    }

    /**
     * Title detail page: edit/delete functionality. With the UI to add page to a title.
     *
     * It is the central place for a title object.
     */
    public function formDetailAction() {
        $id = $this->_request->getParam('id');
        $pageForm = new Object_Form($id);
        $pageFormId = $pageForm->getId();
        if (empty($pageFormId)) {
            // No form defined, redirect to list.
            $this->_redirect('/admin/client/form');
            return false;
        }
        $form = new Form_Admin_Client_Form(false, array('form' => $pageForm));

        // Check for user update
        if ($this->_request->isPost()) {
            $params = $this->_request->getPost();
            if ($form->isValid($params)) {
                // Update user if necessary
                $form->updateForm($pageForm);
                $form->setForm($pageForm);
            } else {
                $form->populate($params);
            }
        }

        $this->view->pageForm = $pageForm;
        $this->view->client = new Object_Client($pageForm->client_id);
        $this->view->form = $form;

        // Form items
        $this->view->formItems = Repo_FormItem::getInstance()->getFormItems($pageFormId);
    }

    /**
     * List client surveys.
     *
     */
    public function surveyAction() {
        $clientId = $this->_request->getParam('client');

        $status = $this->_request->getParam('status');


        if ($clientId > 0) {
            $client = new Object_Client($clientId);
            $this->view->client = $client;
        }
        $titleId = $this->_request->getParam('client');
        if ($titleId > 0) {
            $title = new Object_Title($titleId);
            $this->view->title = $title;
        }

        // $this->view->surveys = Repo_Survey::getInstance()->getClientSurveys($clientId, $status);
        // Survey is now implemented as a data access controlled resource. Get surveys from the access manager
        $this->view->surveys = Manager_Resource_Access::getInstance()->getClientResources($clientId, 'survey', 'Repo_Survey', $status);
        $this->view->status = $status;
    }

    /**
     * Create a new survey.
     *
     */
    public function newSurveyAction() {
        $form = new Form_Admin_Client_Survey();
        // Check for user update
        if ($this->_request->isPost()) {
            $params = $this->_request->getPost();
            if ($form->isValid($params)) {
                // Create a new page
                $surveyId = Repo_Survey::getInstance()->addNew(
                        $form->getValue('client'), $form->getValue('status'), $form->getValue('name'), $form->getValue('type'), $form->getValue('passscore'), $form->getValue('ce'), $form->getValue('maxtime'), $form->getValue('maxquestions'), $form->getValue('random'), $form->getValue('userfeedback'), $form->getValue('responsetype'), $form->getValue('completiontype'), $form->getValue('description')
                );
                // Redirect to page detail page
                $this->_redirect('/admin/client/survey-detail/id/' . $surveyId);
            } else {
                $form->populate($params);
            }
        }
        $this->view->form = $form;
    }

    /**
     * Survey detail page: edit/delete functionality.
     *
     * It is the central place for a survey object.
     */
    public function surveyDetailAction() {


        $id = $this->_request->getParam('id');

        $survey = new Object_Survey($id);
        $surveyId = $survey->getId();
        if (empty($surveyId)) {
            // No survey defined, redirect to list survey.
            $this->_redirect('/admin/client/survey');
            return false;
        }

        // loading all questions from csv file:
        $csvFile = $survey->getCSVFile();
        $this->view->csvError = '';
        if($csvFile !== FALSE && file_exists($csvFile) ) {
            //get the question content and validate it:
            if (($handle = fopen($csvFile, "r")) !== FALSE) {
                $processCSV = $survey->addCSVQuestions($handle, $survey->client_id, $surveyId, $this->_request->getParam('name'));
                if(isset($processCSV['error'])) {
                    $this->view->csvError = $processCSV['error'];
                }
                unlink($csvFile);
            }
        }

        $form = new Form_Admin_Client_Survey(false, array('survey' => $survey));

        // Check for user update
        if ($this->_request->isPost()) {
            $params = $this->_request->getPost();
            if ($form->isValid($params)) {
                // Update user if necessary
                $form->updateSurvey($survey);
                $form->setSurvey($survey);
            } else {
                $form->populate($params);
            }
        }

        $this->view->survey = $survey;
        $this->view->client = new Object_Client($survey->client_id);
        $this->view->form = $form;

        // Survey questions
        $this->view->surveyQuestions = Repo_Survey::getInstance()->getSurveyQuestions($surveyId);

        // Feedback Question Types
        $question_repo = Repo_Question::getInstance();
        $this->view->feedbackQuestionTypeLabels = json_encode($question_repo->feedbackQuestionTypeLabels);
        $this->view->questionTags = Repo_QuestionTag::getInstance()->getTags();

        // Survey access control for resource access
        $this->view->childClients = $this->view->client->getChildClients();
        $this->view->accessClients = Repo_ResourceAccess::getInstance()->getResourceClients(Object_Survey::RESOURCE_TYPE, $survey->getId());


    }

    /**
     * List client questions.
     *
     */
    public function questionAction() {

        $clientId = $this->_request->getParam('client');

        $status = $this->_request->getParam('status');

        if ($clientId > 0) {
            $client = new Object_Client($clientId);
            $this->view->client = $client;
        }
        $titleId = $this->_request->getParam('client');
        if ($titleId > 0) {
            $title = new Object_Title($titleId);
            $this->view->title = $title;
        }

        $this->view->questions = Repo_Question::getInstance()->getClientQuestions($clientId, $status);
        $this->view->status = $status;
    }

    /**
     * List surveys belonging to client.
     *
     */
    public function resultAction() {

        $clientId = $this->_request->getParam('client');
        $status = $this->_request->getParam('status');
        $titleId = $this->_request->getParam('ttlid');
        $typeFilter = $this->_request->getParam('pgtype');
        $surveyFilter = $this->_request->getParam('survey');

        if ($titleId > 0) {
            $title = new Object_Title($titleId);
            $this->view->title = $title;
        }

        if ($clientId > 0) {
            $client = new Object_Client($clientId);
            $this->view->client = $client;
        }

        if ($typeFilter > 0) {
            $this->view->pagetype = $typeFilter;
        }

        $this->view->surveyFilter = ($surveyFilter) ? true : false;
        $this->view->surveyFilterClass = ($surveyFilter) ? " checked" : "";

        $this->view->surveys = Repo_Page::getInstance()->getClientFormResults($clientId, $status, $titleId, $typeFilter);
        $this->view->studentLists = Repo_Student::getInstance()->getRegisteredStudents( $clientId );
        $this->view->status = $status;
        $this->view->titleid = $titleId;
        $this->view->clientid = $clientId;
    }

    /**
     * Result student list: view a CE application's list of registered students.
     */
    public function resultStudentListAction() {
        if ($this->_request->getParam('export')) {
            $this->view->exporting = $this->_request->getParam('export');
        }
        $appId = $this->_request->getParam('id');

        //Complete student list, not date filtered
        $listStudents = Repo_UserSurvey::getInstance()->getStudentList($appId);
        $this->view->listStudents = $listStudents;
    }

    /**
     * Result detail page: view all results for selected survey/question.
    */
    public function resultDetailAction() {

        //if export to excel button was selected
        if ($this->_request->getParam('export')) {
            $this->view->exporting = $this->_request->getParam('export');
            if($this->_request->getParam('export') == "students") {
                if($this->_request->getParam('studentprintfields') ) {
                    $this->view->studentprintfields = $this->_request->getParam('studentprintfields');
                }
            }
        }

        $id = $this->_request->getParam('id');
        $page = new Object_Page($id);

        $pageId = $page->getId();

        $clientId = $this->_request->getParam('client');

        $fromDate = $toDate = "";

        //if range calendar was changed
        if ($this->_request->isPost()) {
            $fromDate = $this->_request->getParam('input-filter-date-from');
            $toDate = $this->_request->getParam('input-filter-date-to');

            $this->view->currentTab = $this->_request->getParam('current-tab');
        } else {
            $this->view->currentTab = "#averages";
        }

        if (empty($pageId)) {
            // if no page id, may be a survey id
            // $this->_request->getParam('survey');
            // code here if a survey id has been passed
            // No page defined, redirect to result page
            $this->_redirect('/admin/client/result');
            return false;
        } else {

            // ** CE Student Quizzes code ****
            if ($fromDate == "" && $toDate == "") {
                //if no dates were posted, create
                $toDate = date('Y-m-d');
                //add 1 day
                $toDate = date('Y-m-d', strtotime('+1 day', strtotime($toDate)));
                $courseBegin = Repo_UserSurvey::getInstance()->getDateOfFirstQuizCompletion($pageId);
                if ($courseBegin) {
                    if (strtotime($courseBegin) >= strtotime($toDate)) {
                        $courseBegin = date('Y-m-d', strtotime('-1 day', strtotime($courseBegin)));
                    }
                }

                //if no quizzes completed yet, default to one week
                $fromDate = ( is_null($courseBegin)) ? date('Y-m-d', strtotime('-1 week', strtotime($toDate))) : $courseBegin;
            }

            $this->view->quizLayout = true;
            $this->view->dateRange = array($fromDate, $toDate);

            $pageType = Repo_Page::getInstance()->getPageType($pageId);

            $this->view->pageType = $pageType;

            //collect statistics according to page type
            switch ($pageType) {

                case "survey":

                    //Tab 1 data: Totals and averages
                    $quizResultsGeneral = Repo_UserSurvey::getInstance()->getResultsQuizGeneral($pageId, $fromDate, $toDate);
                    $this->view->quizResultsGeneral = $quizResultsGeneral;

                    //Tab 2 data: Question data
                    $quizResultsQuestion = Repo_UserSurvey::getInstance()->getResultsQuizQuestions($pageId, $fromDate, $toDate);
                    $this->view->quizResultsQuestion = $quizResultsQuestion;

                    //Tab 3 data: Student details
                    $quizResultsStudent = Repo_UserSurvey::getInstance()->getStudentQuizProgress($pageId, $fromDate, $toDate);
                    $this->view->quizResultsStudent = $quizResultsStudent;
                    break;

                case "cefeedback":

                    $this->view->currentTab = "#cefeedback";

                    //Get Question Type:
                    $feedbackData = Repo_UserSurvey::getInstance()->getResultsCEFeedback($pageId, $fromDate, $toDate);

                    //Tab 1 data: feedback results
                    $this->view->CEFeedback = $feedbackData[1];
                    $this->view->CEFeedbackRespondents = $feedbackData[0];

                    break;
            }
        }



        if ($clientId > 0) {
            $client = new Object_Client($clientId);
            $this->view->clientName = $client->name;
        }
        $this->view->page = $page;
    }

    /*
     * Fetches all tags
    */

    public function tagAction() {

        $this->view->headScript()->prependFile('/js/admin/client/tag.js');
        $name = $this->_request->getParam('name');
        $client_id = $this->_request->getParam('client');
        $parent_id = $this->_request->getParam('parent_id');

        if ($name != "") {
            $newTagValue = Repo_Tag::getInstance()->addNew($name, $client_id, $parent_id);
            if( !is_int($newTagValue) ) {
                $this->view->errorAddingTags = $newTagValue;
            }
        }
        $whereClause = ( $client_id > 0 ) ? 'client_id="'.$client_id.'"' : '';
        $this->view->tags = Repo_Tag::getInstance()->getRows($whereClause)->toArray();

        $treeClientId = ($client_id==0) ? false : $client_id;
        $this->view->tagTree = Repo_Tag::getInstance()->getTagTree($treeClientId);

    }

    /**
     * Add tag.
     * @param integer $tag
     * @param integer $clientId
     * @param integer $parentTagId
     * @return integer
     */
    public function createTagAction(  ) {

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $form = new Form_Admin_Client_Tag_Create();
        $formData = $this->_request->getParams();


        if (!$form->isValid($formData)) {
            $errorMessages = $form->getMessages();
            $messages = array();
            foreach ($errorMessages as $_k => $_e) {
                $messages[] = $_k . ': ' . implode(', ', $_e);
            }
            $errors = implode(';', $messages);
            echo json_encode(array("error" => $errors));
        }

        // Create new tag
        $newTagId = Repo_Tag::getInstance()->addNew(
            $formData['name'], $formData['client_id'], $formData['parent_id']
        );
        if (!(int) $newTagId) {
            echo json_encode(array("error" => $newTagId));
        } else {
            echo json_encode(array("success" => true, "newId" => $newTagId));
        }

    }

    /**
     * Deletes a tag.
     *
     * @param id
     *
     */
    public function deleteTagAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $id = $this->_request->getParam("id");
        if ($id !== "") {
            Repo_Tag::getInstance()->removeTag("id=" . $id);
        }
        echo json_encode(array("success" => true));
    }

    /**
     * Updates a tag
     *
     * @param id
     * @param name
     * @param client_id
     *
     */
    public function updateTagAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $id = $this->_request->getParam("id");
        $parentId = ($this->_request->getParam("parent_id") == 0) ? null : $this->_request->getParam("parent_id");

        if ($id !== "" && $this->_request->isPost()) {
            Repo_Tag::getInstance()->updateTag(array(
                "name" => $this->_request->getParam("name"),
                "client_id" => $this->_request->getParam("client_id"),
                "parent_id" => $parentId
                ),
                "id=" . $id);
        }
        echo json_encode(array("success" => true, "name"=>$this->_request->getParam("name")));
    }

    /**
     * Creates a child tag
     *
     * @param name
     * @param parent_id
     */
    public function childTagAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        if ($this->_request->isPost()) {
            $data['name'] = $this->_request->getParam('name');
            $data['parent_id'] = $this->_request->getParam('parent_id');
            Repo_ChildTag::getInstance()->addChildTag($data);
        }
        $this->redirect("/admin/client/tag");
    }

    /**
     * Deletes a child tag
     *
     * @param id
     *
     */
    public function deleteChildTagAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $id = $this->_request->getParam("id");
        if ($id !== "") {
            Repo_ChildTag::getInstance()->removeChildTag("id=" . $id);
        }

        //echo $this->_request->getParams();
        echo json_encode(array("success" => true));
    }

    /**
     * Updates a Child tag
     *
     * @param id
     * @param name
     */
    public function updateChildTagAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $id = $this->_request->getParam("id");
        if ($id !== "") {
            Repo_ChildTag::getInstance()->updateChildTag(array("name" => $this->_request->getParam("name")), "id=" . $id);
        }
        echo json_encode(array("success" => true));
    }

    /*
     * Saves a question tag
     *
     * @param tag_id
     * @param question_id
     */

    public function questionTagAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $data['tag_id'] = $this->_request->getParam('tag_id');
        $data['question_id'] = $this->_request->getParam('question_id');
        $result = Repo_QuestionTag::getInstance()->addQuestionTag($data);
        $this->redirect('admin/client/survey-detail/id/' . $data['survey_id']);
    }

    /**
     * Add a tag to a survey
     *
     * @param tag_id
     * @param survey_id
     *
     */
    public function surveyTagAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $data['tag_id'] = $this->_request->getParam('tag_id');
        $data['survey_id'] = $this->_request->getParam('survey_id');
        Repo_SurveyTag::getInstance()->addSurveyTag($data);
        echo json_encode($this->_request->getParams());
        $this->redirect('admin/client/survey-detail/id/' . $data['survey_id']);
    }

    /**
     * Adds a tag to question option
     *
     * @param tag_id
     * @param question_id
     */
    public function optionTagAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $data['tag_id'] = $this->_request->getParam('tag_id');
        $data['question_id'] = $this->_request->getParam('question_id');
        $db = Zend_Db_Table::getDefaultAdapter();
        $result = $db->select()->from("question_option")->where("`option` = ?", $data['question_id'])->query()->fetch();
        $data['question_option_id'] = $result['id'];
        unset($data['question_id']);
        $theResult = Repo_QuestionOptionTag::getInstance()->addQuestionOptionTag($data);
        $this->redirect($this->getRequest()->getHeader('Referer'));
    }

    /**
     * Survey tag test
     */
    public function surveyTagTestAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        print('<xmp>');
        print_r(Repo_SurveyTag::getInstance()->getTagBySurveyId(1));
        print('</xmp>');
    }

    /**
     * Tag test
     */
    public function tagTestAction() {
        $this->view->headScript()->prependFile('/js/libraries/jquery/jquery.tagsinput.js');
        $this->view->headLink()->appendStylesheet('/css/jquery.tagsinput.css');
    }

    /**
     * Deletes a survey tag
     *
     * @param id
     */
    public function deleteSurveyTagAction() {
        $id = $this->_request->getParam('id');
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        Repo_SurveyTag::getInstance()->removeSurveyTag("tag_id = " . $id);
        return Zend_Json::encode(array('success' => 'true'));
    }

    /*
     * Deletes a question tag
     *
     * @param id
     */

    public function deleteQuestionTagAction() {
        $id = $this->_request->getParam('id');
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $result = Repo_QuestionTag::getInstance()->removeRowsBySearch("tag_id = '" . $id . "'");
        return Zend_Json::encode(array('success' => 'true'));
    }

    public function surveyChartAction() {
        $this->view->headScript()->prependFile('http://d3js.org/d3.v3.min.js');
        $this->view->headLink()->appendStylesheet('/css/sequences.css');
    }

    /*
     * Prepares data for the watson chart
     *
     * @param start_date
     * @param end_date
     */

    public function watsonChartAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();
        $start_date = $this->_request->getParam("start_date");
        $end_date = $this->_request->getParam("end_date");
        $result = Repo_Watson_Collection_test::getInstance()->generateDataForWatsonChart($start_date, $end_date);
        $results = $result['results'];
        $count = $result['counts']['emit'];
        $this->getResponse()->setHeader("Content-type", "application/json");
        $this->getResponse()->setHeader('Access-Control-Allow-Origin', '*');
        $array = array();
        foreach ($results as $res) {
            if (isset($res['value']['average_confidence']) && is_nan($res['value']['average_confidence'])) {
                $res['value']['average_confidence'] = 0;
            }
            $array[] = array("id" => $res['_id'], "value" => $res['value']);
        }
        $this->getResponse()->setBody(Zend_Json::encode(array('results' => $array, 'count' => $count)));
    }

    /*
     * Gets a survey upon click generated by the watson chart
     *
     * @param data
     * @param start_date
     * @param end_date
     */

    public function getSurveyAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();
        $data = str_replace("?", "\?", $this->_request->getParam("data"));
        $data = str_replace("(", "\(", $data);
        $data = str_replace(")", "\)", $data);
        $start_date = $this->_request->getParam("start_date");
        $end_date = $this->_request->getParam("end_date");
        $this->getResponse()->setHeader('Access-Control-Allow-Origin', '*');
        $ratingSign = "1";

        $results['positive'] = $this->innerSurveyFunction($data, $ratingSign, $start_date, $end_date);
        $ratingSign = "-1";
        $results['negative'] = $this->innerSurveyFunction($data, $ratingSign, $start_date, $end_date);
        //print('<xmp>');
        //$data = Repo_Client::getInstance()->getRows("parent_id=".$this->_getClientContext());
        //print('<xmp>');
        //print_r($data->toArray());
        //print('</xmp>');
        //  print('<xmp>');
        // print_r($this->_getClientContext());
        // print('</xmp>');

        $bigData = array();
        if (!empty($results['positive'])) {
            foreach ($results['positive'] as $res) {
                $bigData[$res['survey_name']][$res['option']][$res['data']]['positive'] = $res['sum_of_all'];
            }
        }
        if (!empty($results['negative'])) {
            foreach ($results['negative'] as $res) {
                $bigData[$res['survey_name']][$res['option']][$res['data']]['negative'] = $res['sum_of_all'];
            }
        }
        $this->getResponse()->setBody(Zend_Json::encode(array('results' => $bigData)));
    }

    /*
     * Helper function for the getSurvey function
     *
     */

    public function innerSurveyFunction($data, $ratingSign, $start_date, $end_date) {
        $user = Zend_Auth::getInstance()->getIdentity();
        // Grab all the clients who have the client_id of the logged in user as parent
        $clients = Repo_Client::getInstance()->getRows("parent_id=" . $user->client_id)->toArray();

        $allClients = array();
        foreach ($clients as $cli) {
            $allClients[] = $cli['id'];
        }
        // Now we should add the client id of the logged in user as well - to that client list.
        $allClients[] = $user->client_id;

        $result = Repo_Watson_Collection_test::getInstance()->findDocument(
                array("evidencelist.title" => new MongoRegex("/" . $data . "/gi"),
                    "survey" => array('$exists' => true, '$elemMatch' => array("rating" => $ratingSign, "client_id" => $user->client_id)),
                    "query_date" => array('$gte' => new MongoDate((int) $start_date, 0), '$lte' => new MongoDate((int) $end_date, 0))));
        $allQuestions = array();

        foreach ($result as $res) {
            foreach ($res['survey'] as $surv) {
                if ($surv['rating'] == $ratingSign) {
                    foreach ($surv['survey_question'] as $ques) {
                        foreach ($ques as $q) {
                            $allQuestions[] = $q;
                        }
                    }
                }
            }
        }
        $data = array();
        $sql = "";
        if (!empty($allQuestions)) {
            $dbTable = new Db_Table_Abstract();
            $adapter = $dbTable->getDefaultAdapter();
            $sql = "select question.id as question_id,"
                    . "survey.id as survey_id,"
                    . "survey.name as survey_name,"
                    . "question,"
                    . "`option`,"
                    . "question_option.type_id,"
                    . "data,"
                    . "client.id,count(data) as sum_of_all from question "
                    . "left join question_option on question.id = question_option.question_id "
                    . "left join user_survey_question on question.id = user_survey_question.question_id "
                    . "left join survey on survey.id = question.survey_id "
                    . "left join client on client.id = survey.client_id "
                    . "where survey.type_id in (select id from survey_type where description = 'watson_survey') "
                    . "and user_survey_question.id in (" . implode(",", array_unique($allQuestions)) . ")"
                    . "and client.id in (" . implode(",", array_unique($allClients)) . ") "
                    . "and completed_datetime between '" . strftime("%Y-%m-%d %H:%M:%S", $start_date) . "' and '" . strftime("%Y-%m-%d %H:%M:%S", $end_date) . "' "
                    . "and user_survey_question.useranswer = question_option.id "
                    . "group by data,question order by survey_name;";

            $data[] = $adapter->fetchAll($sql);
        }
        return array_pop($data);
    }

    /*
     * Get the maximum and minimum dates from mongo for the watson chart
     *
     */

    public function getMaxMinDateForSunburstChartAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();
        $this->getResponse()->setHeader("Content-type", "application/json");
        $this->getResponse()->setHeader('Access-Control-Allow-Origin', '*');
        $data = Repo_Watson_Collection_test::getInstance()->maxMinDate();
        $this->getResponse()->setBody(Zend_Json::encode(array('results' => array('max' => $data['max'], 'min' => $data['min']))));
    }

    public function getPositiveNegativeForSunburstAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();
        $this->getResponse()->setHeader("Content-type", "application/json");
        $this->getResponse()->setHeader("Access-Control-Allow-Origin", '*');
        $data = Repo_Watson_Collection_test::getInstance()->positiveNegativeRating();
        foreach ($data as $dat) {
            if ($dat['_id'] == "positive") {
                $positive = $dat['count'];
            } else {
                $negative = $dat['count'];
            }
        }
        $this->getResponse()->setBody(Zend_Json::encode(array('results' => array('positive' => $positive, 'negative' => $negative))));
    }

    public function saveQuestionOptionTypeAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();
        $option_type_id = $this->_request->getParam("option_type_id");
        $option_id = $this->_request->getParam("option_id");
        $path_name = $this->_request->getParam("path_name");
        $question_id = $this->_request->getParam("question_id");
        $option_value = $this->_request->getParam("option_value");
        $db = new Db_Table_Abstract();
        $test = $db->getDefaultAdapter()->update("question_option", array('type_id' => $option_type_id), "question_id='" . $question_id . "' and `option`='" . $option_value . "'");
        $this->redirect($path_name);
    }

}
