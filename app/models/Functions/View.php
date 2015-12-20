<?php

/**
 * The central place to host all of the view helpers.
 */
class Functions_View {

    const STD_DATE_FORMAT = 'F j, Y h:i a';
    const SHORT_DATE = 'M j, Y H:i';
    const HTML_DEFAULT_EMPTY = '';
    const HTML_NOT_AVAILABLE = 'not-available';

    /**
     * List clients
     *
     * @param Zend_Db_Table_Rowset_Abstract $clients
     * @return string
     */
    public static function listClients($clients) {
        $html = self::HTML_DEFAULT_EMPTY;
        if ($clients && $clients->count()) {
            $html .= '<table class="table table-striped table-bordered">';
            $html .= '<thead><tr><th width="50%" class="text-center">Name</th><th width="25%" class="text-center">Type</th><th width="10%"></th></tr></thead>';
            $html .= '<tbody>';
            foreach ($clients as $_client) {
                $html .= '<tr rel="' . $_client->id . '">';
                $html .= '<td class="client-name">' . $_client->name . '</td>';
                $html .= '<td class="client-type">' . $_client->type . '</td>';
                $html .= '<td><div class="checkbox select-client" clientID="' . $_client->id . '"></div></td>';
                $html .= '</tr>';
            }
            $html .= '</tbody>';
            $html .= '</table>';
        }
        return $html;
    }

    /**
     * List apps.
     *
     * @param Zend_Db_Table_Rowset_Abstract $apps
     * @return string
     */
    public static function listApps($apps) {
        $html = self::HTML_DEFAULT_EMPTY;
        if ($apps && $apps->count()) {
            $html .= '<table class="table table-striped table-bordered table-condensed">';
            $html .= '<thead><tr><th width="30%" class="text-center">Name</th><th width="60%">Description</th>';
            $html .= '<th width="10%" class="text-center"><button type="button" class="btn btn-danger delete-app">Delete</button></th></tr></thead>';
            $html .= '<tbody>';
            foreach ($apps as $_app) {
                $html .= '<tr rel="' . $_app->id . '">';
                $html .= '<td class="app-name">' . $_app->name . '</td>';
                $html .= '<td class="app-desc">' . $_app->description . '</td>';
                $html .= '<td><div class="checkbox select-app" appId="' . $_app->id . '"></div></td>';
                $html .= '</tr>';
            }
            $html .= '</tbody>';
            $html .= '</table>';
        }
        return $html;
    }

    /**
     * List users
     *
     * @param Zend_Db_Table_Rowset_Abstract $users
     * @return string
     */
    public static function listUsers($users) {
        $html = self::HTML_DEFAULT_EMPTY;
        if ($users && $users->count()) {
            $html .= '<table class="table table-striped table-bordered">';
            $html .= '<thead><tr class="off"><th class="text-center sort" data-sort="username">Name</th><th class="text-center sort" data-sort="email">Email</th>';
            $html .= '<th class="text-center sort" data-sort="client">Client</th><th class="text-center sort" data-sort="usertype">User Type</th></tr></thead>';
            $html .= '<tbody class="list">';
            foreach ($users as $_user) {
                $html .= '<tr rel="' . $_user->id . '">';
                $html .= '<td class="username">' . $_user->name . '</td>';
                $html .= '<td class="email">' . $_user->email . '</td>';
                $html .= '<td class="client">' . $_user->client_name . '</td>';
                $html .= '<td class="usertype">' . $_user->user_type . '</td>';
                $html .= '</tr>';
            }
            $html .= '</tbody>';
            $html .= '</table>';
        }
        return $html;
    }

    /**
     * List service type users
     *
     * @param Zend_Db_Table_Rowset_Abstract $users
     * @return string
     */
    public static function listServiceUsers($users)
    {
        $html = self::HTML_DEFAULT_EMPTY;
        if ($users && $users->count()) {
            $html .= '<table class="table table-striped table-bordered">';
            $html .= '<thead><tr><th class="text-center" style="width: 30%">Email</th>';
            $html .= '<th class="text-center">Description</th></tr></thead>';
            $html .= '<tbody>';
            foreach ($users as $_user) {
                $html .= '<tr rel="' . $_user->id . '">';
                $html .= '<td>' . $_user->email . '</td>';
                $html .= '<td>' . $_user->UDID . '</td>';
                $html .= '</tr>';
            }
            $html .= '</tbody>';
            $html .= '</table>';
        }
        return $html;
    }

    /**
     * List teams.
     *
     * @param Zend_Db_Table_Rowset_Abstract $teams
     * @return string
     */
    public static function listTeams($teams) {
        $html = self::HTML_DEFAULT_EMPTY;
        if ($teams && $teams->count()) {
            $html .= '<table class="table table-striped table-bordered">';
            $html .= '<thead><tr><th class="text-center">Team</th><th class="text-center">Status</th>';
            $html .= '<th class="text-center">Description</th></tr></thead>';
            $html .= '<tbody>';
            foreach ($teams as $_team) {
                $html .= '<tr rel="' . $_team->id . '">';
                $html .= '<td>' . $_team->name . '</td>';
                $html .= '<td>' . Repo_Team::$statusLabels[$_team->status] . '</td>';
                $html .= '<td>' . $_team->description . '</td>';
                $html .= '</tr>';
            }
            $html .= '</tbody>';
            $html .= '</table>';
        }
        return $html;
    }

    /**
     * List device ids.
     *
     * @param Zend_Db_Table_Rowset_Abstract $teams
     * @return string
     */
    public static function listDeviceIds($ids) {
        $html = self::HTML_DEFAULT_EMPTY;
        if ($ids && $ids->count()) {
            $html .= '<table class="table table-striped table-bordered">';
            $html .= '<thead><tr><th class="text-center">Id</th><th class="text-center">First Used</th>';
            $html .= '<th class="text-center">Last Used</th></tr></thead>';
            $html .= '<tbody>';
            foreach ($ids as $_id) {
                $html .= '<tr rel="' . $_id->id . '">';
                $html .= '<td>' . $_id->device_id . '</td>';
                $html .= '<td>' . Functions_Common::formattedDay($_id->first_used) . '</td>';
                $html .= '<td>' . Functions_Common::formattedDay($_id->last_used) . '</td>';
                $html .= '</tr>';
            }
            $html .= '</tbody>';
            $html .= '</table>';
        }
        return $html;
    }

    /**
     * List team content checkboxes.
     *
     * @param Zend_Db_Table_Rowset_Abstract $contents
     * @param Zend_Db_Table_Rowset_Abstract $checkedIds
     * @return string
     */
    public static function listTeamContents($contents, $checkedIds, $repoClassName, $class = '') {
        $checked = array();
        if ($checkedIds) {
            foreach ($checkedIds as $_c) {
                $checked[] = $_c->id;
            }
        }
        $html = self::HTML_DEFAULT_EMPTY;
        if ($contents && $contents->count()) {
            $html .= '<table class="table table-striped table-bordered ' . $class . '">';
            $html .= '<thead><tr><th class="text-center"></th><th class="text-center">Name</th>';
            $html .= '</tr></thead>';
            $html .= '<tbody>';
            foreach ($contents as $_c) {
                if ($repoClassName == 'Repo_Users') {
                    $_name = $_c['surname'] . ', ' . $_c['firstname'];
                } else {
                    $_name = $_c['name'];
                }
                $html .= '<tr rel="' . $_c->id . '">';
                $html .= '<td><div class="checkbox ' . (in_array($_c->id, $checked) ? 'checked' : '') . '"></div></td>';
                $html .= '<td>' . $_name . '</td>';
                $html .= '</tr>';
            }
            $html .= '</tbody>';
            $html .= '</table>';
        }
        return $html;
    }

    /**
     * List media assets.
     *
     * @param Zend_Db_Table_Rowset_Abstract $media_asset
     * @return string
     */
    public static function listMediaAssets( $media, $clientId = 0 ) {

        $folder_path = Repo_MediaAsset::getInstance()->getFolderPath($clientId);

        $exceptionList = array('[PageTemplate]','[HideOnClientSide]');
        $html = self::HTML_DEFAULT_EMPTY;
        if ($media && count($media)>0) {
            $html .= '<table class="table table-striped table-bordered">';
            $html .= '<thead><tr class="off"><th class="text-center sort" data-sort="medianame">Name</th><th class="text-center sort" data-sort="preview">Preview</th><th class="text-center sort" data-sort="description">Description</th>';
            $html .= '<th class="text-center sort" data-sort="created_datetime">Created</th><th class="text-center sort" data-sort="modified_datetime">Modified</th></tr></thead>';
            $html .= '<tbody>';
            foreach ($media as $_media) {
                if(in_array($_media['description'],$exceptionList)) {
                    continue;
                }
                if( $_media['client_visibility'] == 0) {
                    continue;
                }
                $html .= '<tr rel="' . $_media['id'] . '">';
                $html .= '<td class="medianame" width="15%">' . $_media['name'] . '</td>';
                $ext = pathinfo($_media['thumb'], PATHINFO_EXTENSION);
                if($ext=='mp4' || $ext=='webm') {
                    $html .= '<td width="20%" class="preview"><video style="width:200px; height: auto;" controls ><source src="' . $_media['thumb'] . '" type="video/mp4"></video></td>';
                } else if($ext=='pdf') {
                    $html .= '<td width="20%" class="preview"><iframe style="width:200px; height: auto;" src="http://docs.google.com/gview?url=' . $_media['thumb'] . '&embedded=true" frameborder="0"></iframe></td>';
                } else {
                    $html .= '<td width="20%" class="preview"><img style="width:200px; height: auto;" src="' . $_media['thumb'] . '" /></td>';
                }
                $html .= '<td width="35%" class="description">' . $_media['description'] . '</td>';
                $html .= '<td width="15%" class="created_datetime"><input type="hidden" val="'.strtotime($_media['created_datetime']).'" />' . Functions_Common::formattedDay($_media['created_datetime']) . '</td>';
                $html .= '<td width="15%" class="modified_datetime"><input type="hidden" val="'.strtotime($_media['modified_datetime']).'" />' . Functions_Common::formattedDay($_media['modified_datetime']) . '</td>';
                $html .= '</tr>';
            }
            $html .= '</tbody>';
            $html .= '</table>';
        }
        return $html;
    }

    /**
     * List pages.
     *
     * @param Zend_Db_Table_Rowset_Abstract $pages
     * @return string
     */
    public static function listPages($pages) {
        $html = self::HTML_DEFAULT_EMPTY;
        if ($pages && $pages->count()) {
            $html .= '<table class="table table-striped table-bordered">';
            $html .= '<thead><tr class="off"><th class="text-center sort" data-sort="pagename">Name</th><th class="text-center sort" data-sort="status">Status</th>';
            $html .= '<th class="text-center sort" data-sort="created_date">Created</th><th class="text-center sort" data-sort="modified_date">Modified</th></tr></thead>';
            $html .= '<tbody>';
            foreach ($pages as $_page) {
                $html .= '<tr rel="' . $_page->id . '">';
                $html .= '<td class="pagename">' . $_page->name . '</td>';
                $html .= '<td class="status">' . Repo_PageStatus::getInstance()->getLabel($_page->status) . '</td>';
                $html .= '<td class="created_date"><input type="hidden" val="'.strtotime($_page->created_datetime).'" />' . Functions_Common::formattedDay($_page->created_datetime) . '</td>';
                $html .= '<td class="modified_date"><input type="hidden" val="'.strtotime($_page->modified_datetime).'" />' . Functions_Common::formattedDay($_page->modified_datetime) . '</td>';
                $html .= '</tr>';
            }
            $html .= '</tbody>';
            $html .= '</table>';
        }
        return $html;
    }

    /**
     * List PDF templates.
     *
     * @param Zend_Db_Table_Rowset_Abstract $templates
     * @return string
     */
    public static function listPdfTemplates($templates, $baseUrl = '/admin/client/pdf-template-detail/id/') {
        $html = self::HTML_DEFAULT_EMPTY;
        if ($templates && $templates->count()) {
            $html .= '<table class="table table-striped table-bordered">';
            $html .= '<thead><tr><th class="text-center">Name</th>';
            $html .= '<th class="text-center">Created</th><th class="text-center">Modified</th></tr></thead>';
            $html .= '<tbody>';
            foreach ($templates as $_t) {
                $html .= '<tr rel="' . $_t->id . '">';
                $html .= '<td>' . $_t->name . '</td>';
                $html .= '<td>' . Functions_Common::formattedDay($_t->created_datetime) . '</td>';
                $html .= '<td>' . Functions_Common::formattedDay($_t->modified_datetime) . '</td>';
                $html .= '</tr>';
            }
            $html .= '</tbody>';
            $html .= '</table>';
        }
        return $html;
    }

    /**
     * List Page templates.
     *
     * @param Zend_Db_Table_Rowset_Abstract $templates
     * @return string
     */
    public static function listPageTemplates($templates, $baseUrl = '/admin/client/page-template-detail/id/') {
        $html = self::HTML_DEFAULT_EMPTY;
        if ($templates && $templates->count()) {
            $html .= '<table class="table table-striped table-bordered">';
            $html .= '<thead><tr><th class="text-center">Name</th>';
            $html .= '<th class="text-center">Created</th><th class="text-center">Modified</th></tr></thead>';
            $html .= '<tbody>';
            foreach ($templates as $_t) {
                $html .= '<tr rel="' . $_t->id . '">';
                $html .= '<td>' . $_t->name . '</td>';
                $html .= '<td>' . Functions_Common::formattedDay($_t->created_datetime) . '</td>';
                $html .= '<td>' . Functions_Common::formattedDay($_t->modified_datetime) . '</td>';
                $html .= '</tr>';
            }
            $html .= '</tbody>';
            $html .= '</table>';
        }
        return $html;
    }

    /**
     * List titles.
     *
     * @param Zend_Db_Table_Rowset_Abstract $titles
     * @return string
     */
    public static function listTitles($titles, $baseUrl = '/admin/client/title-detail/id/') {
        $html = self::HTML_DEFAULT_EMPTY;
        if ($titles && $titles->count()) {
            $html .= '<table class="table table-striped table-bordered">';
            $html .= '<thead><tr><th class="text-center">Name</th><th class="text-center">Status</th>';
            $html .= '<th class="text-center">Created</th><th class="text-center">Modified</th></tr></thead>';
            $html .= '<tbody>';
            foreach ($titles as $_title) {
                $html .= '<tr rel="' . $_title->id . '">';
                $html .= '<td>' . $_title->name . '</td>';
                $html .= '<td>' . Repo_Title::$statusLabels[$_title->status] . '</td>';
                $html .= '<td>' . Functions_Common::formattedDay($_title->created_datetime) . '</td>';
                $html .= '<td>' . Functions_Common::formattedDay($_title->modified_datetime) . '</td>';
                $html .= '</tr>';
            }
            $html .= '</tbody>';
            $html .= '</table>';
        }
        return $html;
    }

    /**
     * List titles.
     *
     * @param Zend_Db_Table_Rowset_Abstract $titles
     * @return string
     */
    public static function listTitlesSimple($titles)
    {
        $html = self::HTML_DEFAULT_EMPTY;
        if ($titles && $titles->count()) {
            $html .= '<ul>';
            foreach ($titles as $_title) {
                $html .= '<li rel="' . $_title->id . '" class="draggable">';
                $html .= $_title->name . '</li>';
            }
            $html .= '</ul>';
        }
        return $html;
    }

    /**
     * List titles for client.
     * Sortable using list.js
     * @param Zend_Db_Table_Rowset_Abstract $titles
     * @return string
     */
    public static function listTitlesClientSide($titles, $baseUrl = '/admin/client/title-detail/id/') {

        $html = self::HTML_DEFAULT_EMPTY;
        if ($titles && $titles->count()) {
            $html .= '<table class="table table-striped table-bordered">';
            $html .= '<thead><tr class="off"><th class="text-center sort" data-sort="name">Name</th><th class="text-center sort" data-sort="type">Type</th>';
            $html .= '<th class="text-center sort" data-sort="created_date">Created</th><th class="text-center sort" data-sort="modified_date">Modified</th></tr></thead>';
            $html .= '<tbody class="list">';
            foreach ($titles as $_title) {
                $html .= '<tr rel="' . $_title->id . '">';
                $html .= '<td class="name">' . $_title->name . '</td>';
                $html .= '<td class="type">' . $_title->type . '</td>';
                $html .= '<td class="created_date"><input type="hidden" value="'.strtotime($_title->created_datetime).'">' . Functions_Common::formattedDay($_title->created_datetime) . '</td>';
                $html .= '<td class="modified_date"><input type="hidden" value="'.strtotime($_title->modified_datetime).'">' . Functions_Common::formattedDay($_title->modified_datetime) . '</td>';
                $html .= '</tr>';
            }
            $html .= '</tbody>';
            $html .= '</table>';
        }
        return $html;
    }

    /**
     * List package content checkboxes.
     *
     * @param Zend_Db_Table_Rowset_Abstract $contents
     * @param Zend_Db_Table_Rowset_Abstract $checkedIds
     * @return string
     */
    public static function listPackageContents($contents, $checkedIds, $repoClassName, $class = '', $clientSide = false) {
        $checked = array();
        if ($checkedIds) {
            foreach ($checkedIds as $_c) {
                $checked[] = $_c->id;
            }
        }
        $html = self::HTML_DEFAULT_EMPTY;
        $hrefPrefix = ( $clientSide == false ) ? '/admin/client/title-detail/id/' : '/client/title/detail/id/';
        if ($contents && $contents->count()) {
            $html .= '<table class="table table-striped table-bordered ' . $class . '">';
            $html .= '<thead><tr><th class="text-center"></th><th class="text-center">Name</th>';
            $html .= '<th class="text-center">Status</th></tr></thead>';
            $html .= '<tbody>';
            foreach ($contents as $_c) {
                $_link = false;
                if ($repoClassName == 'Repo_Title') {
                    $_link = $hrefPrefix . $_c->id;
                }
                $html .= '<tr rel="' . $_c->id . '">';
                $html .= '<td><div class="checkbox ' . (in_array($_c->id, $checked) ? 'checked' : '') . '"></div></td>';
                $html .= '<td>' . ($_link ? '<a href="' . $_link . '" target="_blank">' : '') . $_c->name . ($_link ? '</a>' : '') . '</td>';
                $html .= '<td>' . $repoClassName::$statusLabels[$_c->status] . '</td>';
                $html .= '</tr>';
            }
            $html .= '</tbody>';
            $html .= '</table>';
        }
        return $html;
    }

    /**
     * List package manifests.
     *
     * @param Zend_Db_Table_Rowset_Abstract $manifests
     * @return string
     */
    public static function listPackageManifests($manifests, $showPreviewLink = true)
    {
        $html = self::HTML_DEFAULT_EMPTY;
        if ($manifests && $manifests->count()) {
            $html .= '<table class="table table-striped table-bordered">';
            $html .= '<thead><tr><th class="text-center">Version</th><th class="text-center">Date Generated</th>' . ($showPreviewLink ? '<th></th>' : '') . '</tr></thead>';
            $html .= '<tbody>';
            foreach ($manifests as $_m) {
                $html .= '<tr rel="' . $_m->id . '">';
                $html .= '<td>' . $_m->version . '</td>';
                $html .= '<td>' . Functions_Common::formattedDay($_m->created_datetime) . '</td>';
                if ($showPreviewLink) {
                    $html .= '<td><a href="/admin/client/package-manifest/manifest/' . $_m->id . '">Preview</a></td>';
                }
                $html .= '</tr>';
            }
            $html .= '</tbody>';
            $html .= '</table>';
        }
        return $html;
    }

    /**
     * List page versions.
     *
     * @param Zend_Db_Table_Rowset_Abstract $pageVersions
     * @return string
     */
    public static function listPageVersions($pageVersions)
    {
        $html = self::HTML_DEFAULT_EMPTY;
        if ($pageVersions && $pageVersions->count()) {
            $page = new Object_Page($pageVersions->getRow(0)->page_id);
            $html .= '<table class="table table-striped table-bordered">';
            $html .= '<thead><tr><th class="text-center">Version</th><th class="text-center">Date Generated</th><th></th></tr></thead>';
            $html .= '<tbody>';
            foreach ($pageVersions as $_p) {
                $html .= '<tr rel="' . $_p->id . '" class="noClickThrough ' . ($_p->is_active ? "active" : "inactive") . '" title="Used in production">';
                $html .= '<td>' . $_p->version . '</td>';
                $html .= '<td>' . Functions_Common::formattedDay($_p->created_datetime) . '</td>';
                $html .= '<td><a target="_blank" href="' . $page->getVersionPreviewLink($_p->id) . '">Preview</a></td>';
                $html .= '</tr>';
            }
            $html .= '</tbody>';
            $html .= '</table>';
        } else {
            $html = '<p>N/A</p>';
        }
        return $html;
    }

    /**
     * List libraries.
     *
     * @param Zend_Db_Table_Rowset_Abstract $libraries
     * @return string
     */
    public static function listLibraries($libraries) {
        $html = self::HTML_DEFAULT_EMPTY;
        if ($libraries && $libraries->count()) {
            $html .= '<table class="table table-striped table-bordered">';
            $html .= '<thead><tr><th class="text-center">Name</th><th class="text-center">Status</th>';
            $html .= '<th class="text-center">Created</th><th class="text-center">Modified</th><th></th></tr></thead>';
            $html .= '<tbody>';
            foreach ($libraries as $_library) {
                $html .= '<tr rel="' . $_library->id . '">';
                $html .= '<td>' . $_library->name . '</td>';
                $html .= '<td>' . Repo_Library::$statusLabels[$_library->status] . '</td>';
                $html .= '<td>' . Functions_Common::formattedDay($_library->created_datetime) . '</td>';
                $html .= '<td>' . Functions_Common::formattedDay($_library->modified_datetime) . '</td>';
                $html .= '<td><div class="pull-right"><a class="btn btn-primary library-detail"';
                $html .= ' href="/admin/client/library-detail/id/' . $_library->id . '">Details</a></div></td>';
                $html .= '</tr>';
            }
            $html .= '</tbody>';
            $html .= '</table>';
        }
        return $html;
    }

    /**
     * List packages.
     *
     * @param Zend_Db_Table_Rowset_Abstract $packages
     * @return string
     */
    public static function listPackages($packages) {
        $html = self::HTML_DEFAULT_EMPTY;
        if ($packages && $packages->count()) {
            $html .= '<table class="table table-striped table-bordered">';
            $html .= '<thead><tr><th class="text-center">Name</th><th class="text-center">Status</th>';
            $html .= '<th class="text-center">Created</th><th class="text-center">Modified</th></tr></thead>';
            $html .= '<tbody>';
            foreach ($packages as $_package) {
                $html .= '<tr rel="' . $_package->id . '">';
                $html .= '<td>' . $_package->name . '</td>';
                $html .= '<td>' . Repo_Package::$statusLabels[$_package->status] . '</td>';
                $html .= '<td>' . Functions_Common::formattedDay($_package->created_datetime) . '</td>';
                $html .= '<td>' . Functions_Common::formattedDay($_package->modified_datetime) . '</td>';
                $html .= '</tr>';
            }
            $html .= '</tbody>';
            $html .= '</table>';
        }
        return $html;
    }

    /**
     * List clients
     *
     * @param Zend_Db_Table_Rowset_Abstract $clients
     * @return string
     */
    public static function listMeetings($meetings) {
        $html = self::HTML_DEFAULT_EMPTY;

        /*
          <div style="">
          <div style="background-color:#eee; height:50px; padding-top:10px; padding-left:20px; font-size:16pt;">
          Scheduled Meetings
          </div>
          <div id="meeting-panel-1" class="meeting-info-panel" style="max-height:60px; border-left:solid #eee 2px; border-right:solid #eee 2px; border-bottom:solid #eee 2px; overflow:hidden;">


          <div class="ion-arrow-right-b meeting-toggle" style="color:#666; font-size:10pt; width:40px; height:55px; text-align:center; padding-top:19px; float:left; cursor:pointer;">

          </div>
          <div style="float:left;">
          <div style="font-size:14pt; padding-top:6px;">
          Name
          </div>
          <div style="font-size:10pt; padding-top:0px; color:#666;">
          June 21, 2014  10:30am EST
          </div>
          </div>

          <div  class="ion-close" style="float:right; color:#666; height:40px; font-size:16pt; padding-left:10px; padding-right:15px; padding-top:14px;">
          </div>
          <div  class="ion-edit" style="float:right; color:#666; height:40px; font-size:16pt; padding-left:10px; padding-right:10px; padding-top:14px;">
          </div>

          <div style="clear:both; margin-left:60px;">
          <div style="font-size:14pt; padding-top:6px; padding-bottom:10px;">
          Invited Contacts
          </div>
          <div>Keith Winn <span style="color:#00aa00; padding-left:20px;">Accepted</span></div>
          <div>nmclean@lifelearn.com <span style="color:#00aa00; padding-left:20px;">Accepted</span></div>
          <div>Matt Jang <span style="color:#aa0000; padding-left:20px;">Declined</span></div>
          <div style="height:20px;"></div>
          </div>


          </div>
          </div>
         */



        //$html .= '<table class="table table-striped table-bordered">';

        $html .= '<div style="">';
        $html .= '<div style="background-color:#eee; height:50px; padding-top:10px; padding-left:20px; font-size:16pt;">Scheduled Meetings</div>';

        $currentMonth = '';
        $currentYear = '';

        foreach ($meetings as $_meeting) {

            $yearOfMeeting = date('Y', strtotime($_meeting->startDate));

            if ($yearOfMeeting != $currentYear) {
                //$html .= '<div style="background-color:#eee; height:30px; padding-top:3px; padding-left:20px; font-size:12pt;">'.$yearOfMeeting.'</div>';
                $currentMonth = '';
                $currentYear = $yearOfMeeting;
            }

            $monthOfMeeting = date('F', strtotime($_meeting->startDate));

            if ($monthOfMeeting != $currentMonth) {
                $html .= '<div style="background-color:#eee; height:30px; padding-top:3px; padding-left:20px; font-size:12pt;">' . $monthOfMeeting . '<span style="padding-left:10px; font-size:10pt; color:#aaaaaa;">' . $yearOfMeeting . '</span></div>';
                $currentMonth = $monthOfMeeting;
            }

            $html .= '<div id="meeting-panel-' . $_meeting->id . '" class="meeting-info-panel" style="max-height:60px; border-left:solid #eee 2px; border-right:solid #eee 2px; border-bottom:solid #eee 2px; overflow:hidden;">';
            $html .= '<div class="ion-arrow-right-b meeting-toggle" style="color:#666; font-size:10pt; width:40px; height:55px; text-align:center; padding-top:19px; float:left; cursor:pointer;"></div>';
            $html .= '<div style="float:left;"><div style="font-size:14pt; padding-top:6px;">' . $_meeting->name . '</div><div style="font-size:10pt; padding-top:0px; color:#666;">' . $_meeting->startDate . '</div></div>';
            $html .= '<div  class="ion-close meeting-delete-button" style="float:right; color:#666; height:60px; font-size:16pt; padding-left:10px; padding-right:15px; padding-top:14px;"></div>';
            $html .= '<div  class="ion-edit meeting-edit-button" style="float:right; color:#666; height:60px; font-size:16pt; padding-left:10px; padding-right:10px; padding-top:14px;"></div>';

            $html .= '<div style="clear:both; margin-left:60px;"><div style="font-size:14pt; padding-top:6px; padding-bottom:10px;">Invited Contacts</div>';


            if (count($_meeting->invites) == 0) {
                $html .= '<div>None</div>';
            } else {
                foreach ($_meeting->invites as $contact) {
                    $html .= '<div style="height:22px;">' . $contact->firstname . " " . $contact->surname;

                    if ($contact->status == "accepted") {
                        $html .= '<span style="color:#00aa00; padding-left:20px;">Accepted</span>';
                    } else {
                        $html .= '<span style="color:#aa0000; padding-left:20px;">Declined</span>';
                    }
                    $html .= '</div>';
                }
            }


            $html .= '<div style="height:20px;"></div>';

            $html .= '</div>';
            $html .= '</div>';
        }


        return $html;
    }

    /**
     * List forms.
     *
     * @param Zend_Db_Table_Rowset_Abstract $pageForms
     * @return string
     */
    public static function listForms($pageForms) {
        $html = self::HTML_DEFAULT_EMPTY;
        if ($pageForms && $pageForms->count()) {
            $html .= '<table class="table table-striped table-bordered">';
            $html .= '<thead><tr><th class="text-center">Name</th><th class="text-center">Status</th>';
            $html .= '<th class="text-center">Created</th><th class="text-center">Modified</th></tr></thead>';
            $html .= '<tbody>';
            foreach ($pageForms as $_pageForm) {
                $html .= '<tr rel="' . $_pageForm->id . '">';
                $html .= '<td>' . $_pageForm->name . '</td>';
                $html .= '<td>' . Repo_Form::$statusLabels[$_pageForm->status] . '</td>';
                $html .= '<td>' . Functions_Common::formattedDay($_pageForm->created_datetime) . '</td>';
                $html .= '<td>' . Functions_Common::formattedDay($_pageForm->modified_datetime) . '</td>';
                $html .= '</tr>';
            }
            $html .= '</tbody>';
            $html .= '</table>';
        }
        return $html;
    }

    /**
     * Build a list of pages. No root container.
     *
     * @param array $pages
     * @param mixed $tagName
     * @param mixed $tagClasses
     *
     * @return string
     */
    public static function listPageGroup($pages, $tagName, $tagClasses, $showCheckbox = true, $clientSide = false) {
        $html = self::HTML_DEFAULT_EMPTY;
        $hrefPrefix = ( $clientSide == false ) ? '/admin/client/page-detail/id/' : '/client/page/detail/id/';
        if ($pages && count($pages)) {
            foreach ($pages as $_page) {
                $_p = new Object_Page($_page["id"]);
                $html .= '<' . $tagName . ' class="' . $tagClasses . '" rel="' . $_page['id'] . '" id="page_' . $_page['id'] . '">';
                $html .= '<img src="' . $_p->getThumbImageLink(false) . '" width="100px" />';
                $html .= '&nbsp;&nbsp;';
                $html .= '<a class="pagename" href="' . $hrefPrefix . $_page['id'] . '" target="_blank">' . $_page['name'] . '</a>';
                if ($showCheckbox) {
                    $html .= '<div class="checkbox portal-page-list"></div>';
                }
                $html .= '</' . $tagName . '>';
            }
        }
        return $html;
    }

    /**
     * Build a list of objects. No root container.
     *
     * @param array $objects
     * @param mixed $tagName
     * @param mixed $tagClasses
     * @param mixed $idPrefix
     *
     * @return string
     */
    public static function listObjectGroup($objects, $tagName, $tagClasses, $idPrefix) {
        $html = self::HTML_DEFAULT_EMPTY;
        if ($objects && count($objects)) {
            foreach ($objects as $_object) {
                $html .= '<' . $tagName . ' class="' . $tagClasses . '" rel="' . $_object['id'] . '" id="' . $idPrefix . $_object['id'] . '">' . $_object['name'];
                $html .= '</' . $tagName . '>';
            }
        }
        return $html;
    }

    /**
     * Build a list of forms items.
     *
     * @param array $formItems
     * @param mixed $tagName
     * @param mixed $tagClasses
     *
     * @return string
     */
    public static function listFormItemGroup($formItems, $tagName, $tagClasses) {
        $html = self::HTML_DEFAULT_EMPTY;
        if ($formItems && count($formItems)) {
            foreach ($formItems as $_formItem) {
                $html .= '<' . $tagName . ' class="' . $tagClasses . '" rel="' . $_formItem['id'] . '" id="form_item_' . $_formItem['id'] . '">';
                $html .= $_formItem['text'] . '<button type="button" class="close delete-form-item-button" rel="' . $_formItem['id'] . '" >&times;</button>';
                $html .= '</' . $tagName . '>';
            }
        }
        return $html;
    }

    public static function calendarPicker() {
        return "";
    }

    /**
     * Create new client modal.
     *
     * @param Form_Abstract $form
     * @return string
     */
    public static function formModal($form, $id = 'form-modal', $titleId = 'formModalLabel', $defaultTitle = 'Create New Client', $backdrop = true) {
        $html = <<<HTML
<div class="modal fade" id="{$id}" tabindex="-1" role="dialog" aria-labelledby="formModalLabel" aria-hidden="true" data-backdrop="{$backdrop}">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <span class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></span>
        <h4 class="modal-title" id="{$titleId}">{$defaultTitle}</h4>
      </div>
      <div class="modal-body">
        {$form}
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary form-modal-submit">Submit</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
HTML;
        return $html;
    }

    /**
     * Dialog (no modal).
     *
     * @param Form_Abstract $form
     * @return string
     */
    public static function jqueryDialog($form, $id = 'form-modal', $titleId = 'formModalLabel', $defaultTitle = 'Create New Client') {
        $html = <<<HTML
        <div id="{$id}" title="Dialog Title" style="display: none">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="{$titleId}">{$defaultTitle}</h4>
              </div>
              <div class="modal-body">
                {$form}
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default dialog-close" class="">Close</button>
                <button type="button" class="btn btn-primary dialog-submit">Submit</button>
              </div>
            </div><!-- /.modal-content -->
        </div>
        <script type="text/javascript">
            $(function(){
                $('#{$id}').dialog({
                    autoOpen: false,
                    minWidth: 600,
                    draggable: true
                });
            });
        </script>
HTML;
        return $html;
    }

    /**
     * General delete modal.
     *
     * @param string $title
     * @param string $message
     * @return string
     */
    public static function deleteModal($title, $message) {
        $html = <<<HTML
<div class="modal fade" id="delete-modal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="deleteModalLabel">{$title}</h4>
      </div>
      <div class="modal-body">
        {$message}
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary delete-modal-submit">Delete</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
HTML;
        return $html;
    }

    /**
     * Get a client button dropdown.
     *
     * @param string $baseLink
     * @return string
     */
    public static function clientFilter($baseLink, $defaultLabel, $linkClass = false, $selected = false, $clients = false) {
        $allClientLabel = 'All Clients';
        $defaultLabel = $defaultLabel ? $defaultLabel : $allClientLabel;
        $html = '
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">'
                . '<span id="client-filter-default" rel="' . $selected . '">' . $defaultLabel . '</span> <span class="caret"></span>
            </button>
            <ul class="dropdown-menu" role="menu">
            <li><a href="' . $baseLink . '"'
                . ($linkClass ? ' class="' . $linkClass . '" ' : '')
                . '><b>' . $allClientLabel . '</b></a></li>';
        if (!$clients) {
            $clients = Repo_Client::getInstance()->getRows();
        }
        // $clients is expected to be an flat array, not nested.
        foreach ($clients as $_c) {
            $html .= self::buildClientOption($_c, $baseLink, $linkClass);
        }
        $html .='</ul>';
        return $html;
    }

    /**
     * Build a client tree like option.
     *
     * @param mixed $client
     * @param mixed $baseLink
     * @param mixed $linkClass
     */
    public static function buildClientOption($client, $baseLink, $linkClass)
    {
        // First build self;
        $clientId = is_array($client) ? $client['id'] : $client->id;
        $clientName = is_array($client) ? $client['name'] : $client->name;
        if ($clientId <= 0) {
            return false;
        }
        $html = '<li><a href="' . $baseLink . $clientId . '" rel="' . $clientId . '"';
        $html .= ($linkClass ? ' class="' . $linkClass . '" ' : '');
        $html .= '>' . $clientName . '</a></li>';
        return $html;
    }

    // clients dropdown on menu
    public static function menuClients($defaultLabel) {
        $allClientLabel = 'All Clients';
        $defaultLabel = $defaultLabel ? $defaultLabel : $allClientLabel;
        $html = '<span class="menu-icon icon-multiple25"></span>';
        $html .= '<div id="menu-clients">';
        $html .= '<div id="menu-clients-selected">';
        $html .= $defaultLabel;
        $html .= '</div>';
        $html .= '<div id="menu-clients-dropdown" class="dropdown">';
        $html .= '<div class="menu-client" clientID="0">All Clients</div>';


        $clientTree = Repo_Client::getInstance()->getClientTree();
        Functions_Common::$tempCache = array();
        Functions_Common::flattenOptionTree($clientTree, Functions_Common::$tempCache, 'id', 'name', 'children', '');
        $clientTreeOptions = Functions_Common::$tempCache;

        foreach ($clientTreeOptions as $_cId => $_cName) {
            $html .= '<div class="menu-client" clientID="' . $_cId . '">' . $_cName . '</div>';
        }
        $html .= '</div>';
        $html .= '</div>';
        return $html;
    }

    public static function configMenu(){
        $html = '<div class="menu-icon-settings menu-icon ion-gear-b"></div>';
        $html .= '<div>Settings</div>';
        $html .= '<div id="menu-config-dropdown" class="dropdown">';
        if (Auth_Wrapper_User::isUserSuperAdmin()){
            $html .= '<p class="config-item"><a href="/admin/config/role/">Roles And Permissions</a></p>';
            $html .= '<p class="config-item"><a href="/admin/config/upload-user">Upload Users</a></p>';
            $html .= '<p class="config-item"><a href="/admin/config/service-user">Services</a></p>';
        }
        $html.='<p class="config-item"><a href="/client/index/change-password">Reset Password</a></p></div>';

        return $html;
    }

      public static function configMenuMobile(){
        $html = "";
        $html .= '<div id="menu-config-dropdown-mobile" class="dropdown" style="top:10px;display:none;">';
        $html .= '<div class="menu-config-mobile">';
         if(Auth_Wrapper_User::isUserSuperAdmin()){
            $html.=  '<a href="/admin/config/role"><p class="config-item-mobile">Roles And Permissions</p></a>';
         }
        $html.='<a href="/client/index/change-password"><p class="config-item-mobile sidebar-list">Reset Password</p></a>';

        $html .= '</div></div>';

        return $html;
    }

    /**
     * Get a title filter for a client.
     *
     * @param integer $clientId
     * @return string
     */
    public static function clientTitleFilter($clientId) {
        $html = '
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">'
                . '<span id="title-filter-default">All Titles</span> <span class="caret"></span>
            </button>
            <ul class="dropdown-menu" role="menu">
            <li><a href="javascript:void(0)" class="client-title-filter"><b>All Titles</b></a></li>';
        $titles = Repo_Title::getInstance()->getClientTitles($clientId);
        foreach ($titles as $_t) {
            $_titlePages = Repo_TitlePage::getInstance()->getTitlePages($_t->id)->toArray();
            $html .= '<li><a href="javascript:void(0)" rel="' . $_t->id . '" class="client-title-filter" pages=\'' . Zend_Json::encode($_titlePages) . '\'>' . $_t->name . '</a></li>';
        }
        $html .='</ul>';
        return $html;
    }

    /**
     * Get a page status button dropdown.
     *
     * @param string $baseLink
     * @return string
     */
    public static function pageStatusFilter($defaultLabel, $linkClass = false, $selected = false) {
        $allLabel = 'All Status';
        $defaultLabel = $defaultLabel ? $defaultLabel : $allLabel;
        $html = '
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">'
                . '<span id="status-filter-default" rel="' . $selected . '">' . $defaultLabel . '</span> <span class="caret"></span>
            </button>
            <ul class="dropdown-menu" role="menu">
            <li><a href="#"'
                . ($linkClass ? ' class="' . $linkClass . '" ' : '')
                . '><b>' . $allLabel . '</b></li>';
        foreach (Repo_PageStatus::getInstance()->getSelectArray() as $_k => $_l) {
            $html .= '<li><a href="#" rel="' . $_k . '"';
            $html .= ($linkClass ? ' class="' . $linkClass . '" ' : '');
            $html .= '>' . $_l . '</a></li>';
        }
        $html .='</ul>';
        return $html;
    }

    /**
     * Get a media asset usage type button dropdown.
     *
     * @param string $baseLink
     * @return string
     */
    public static function mediaAssetUsageTypeFilter($defaultLabel, $linkClass = false, $selected = false)
    {
        $html = '
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">'
                . '<span id="type-filter-default" rel="' . $selected . '">' . $defaultLabel . '</span> <span class="caret"></span>
            </button>
            <ul class="dropdown-menu" role="menu">';
        foreach (Repo_MediaAsset::$usageTypeLabels as $_k => $_l) {
            $html .= '<li><a href="#" rel="' . $_k . '"';
            $html .= ($linkClass ? ' class="' . $linkClass . '" ' : '');
            $html .= '>' . $_l . '</a></li>';
        }
        $html .='</ul>';
        return $html;
    }

    /**
     * Get a title status button dropdown.
     *
     * @param string $baseLink
     * @return string
     */
    public static function titleStatusFilter($defaultLabel, $linkClass = false, $selected = false) {
        $allLabel = 'All Status';
        $defaultLabel = $defaultLabel ? $defaultLabel : $allLabel;
        $html = '
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">'
                . '<span id="status-filter-default" rel="' . $selected . '">' . $defaultLabel . '</span> <span class="caret"></span>
            </button>
            <ul class="dropdown-menu" role="menu">
            <li><a href="#"'
                . ($linkClass ? ' class="' . $linkClass . '" ' : '')
                . '><b>' . $allLabel . '</b></li>';
        foreach (Repo_Title::$statusLabels as $_k => $_l) {
            $html .= '<li><a href="#" rel="' . $_k . '"';
            $html .= ($linkClass ? ' class="' . $linkClass . '" ' : '');
            $html .= '>' . $_l . '</a></li>';
        }
        $html .='</ul>';
        return $html;
    }

    /**
     * Get a library status button dropdown.
     *
     * @param string $baseLink
     * @return string
     */
    public static function libraryStatusFilter($defaultLabel, $linkClass = false, $selected = false) {
        $allLabel = 'All Status';
        $defaultLabel = $defaultLabel ? $defaultLabel : $allLabel;
        $html = '
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">'
                . '<span id="status-filter-default" rel="' . $selected . '">' . $defaultLabel . '</span> <span class="caret"></span>
            </button>
            <ul class="dropdown-menu" role="menu">
            <li><a href="#"'
                . ($linkClass ? ' class="' . $linkClass . '" ' : '')
                . '><b>' . $allLabel . '</b></li>';
        foreach (Repo_Library::$statusLabels as $_k => $_l) {
            $html .= '<li><a href="#" rel="' . $_k . '"';
            $html .= ($linkClass ? ' class="' . $linkClass . '" ' : '');
            $html .= '>' . $_l . '</a></li>';
        }
        $html .='</ul>';
        return $html;
    }

    /**
     * Get a package status button dropdown.
     *
     * @param string $baseLink
     * @return string
     */
    public static function packageStatusFilter($defaultLabel, $linkClass = false, $selected = false) {
        $allLabel = 'All Status';
        $defaultLabel = $defaultLabel ? $defaultLabel : $allLabel;
        $html = '
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">'
                . '<span id="status-filter-default" rel="' . $selected . '">' . $defaultLabel . '</span> <span class="caret"></span>
            </button>
            <ul class="dropdown-menu" role="menu">
            <li><a href="#"'
                . ($linkClass ? ' class="' . $linkClass . '" ' : '')
                . '><b>' . $allLabel . '</b></li>';
        foreach (Repo_Package::$statusLabels as $_k => $_l) {
            $html .= '<li><a href="#" rel="' . $_k . '"';
            $html .= ($linkClass ? ' class="' . $linkClass . '" ' : '');
            $html .= '>' . $_l . '</a></li>';
        }
        $html .='</ul>';
        return $html;
    }

    /**
     * Get a form status button dropdown.
     *
     * @param string $baseLink
     * @return string
     */
    public static function formStatusFilter($defaultLabel, $linkClass = false, $selected = false) {
        $allLabel = 'All Status';
        $defaultLabel = $defaultLabel ? $defaultLabel : $allLabel;
        $html = '
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">'
                . '<span id="status-filter-default" rel="' . $selected . '">' . $defaultLabel . '</span> <span class="caret"></span>
            </button>
            <ul class="dropdown-menu" role="menu">
            <li><a href="#"'
                . ($linkClass ? ' class="' . $linkClass . '" ' : '')
                . '><b>' . $allLabel . '</b></a></li>';
        foreach (Repo_Form::$statusLabels as $_k => $_l) {
            $html .= '<li><a href="#" rel="' . $_k . '"';
            $html .= ($linkClass ? ' class="' . $linkClass . '" ' : '');
            $html .= '>' . $_l . '</a></li>';
        }
        $html .='</ul>';
        return $html;
    }

    /**
     * Get a survey status button dropdown.
     *
     * @param string $baseLink
     * @return string
     */
    public static function surveyStatusFilter($defaultLabel, $linkClass = false, $selected = false) {
        $allLabel = 'All Status';
        $defaultLabel = $defaultLabel ? $defaultLabel : $allLabel;
        $html = '
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">'
                . '<span id="status-filter-default" rel="' . $selected . '">' . $defaultLabel . '</span> <span class="caret"></span>
            </button>
            <ul class="dropdown-menu" role="menu">
            <li><a href="#"'
                . ($linkClass ? ' class="' . $linkClass . '" ' : '')
                . '><b>' . $allLabel . '</b></a></li>';
        foreach (Repo_Survey::$statusLabels as $_k => $_l) {
            $html .= '<li><a href="#" rel="' . $_k . '"';
            $html .= ($linkClass ? ' class="' . $linkClass . '" ' : '');
            $html .= '>' . $_l . '</a></li>';
        }
        $html .='</ul>';
        return $html;
    }

    /**
     * List surveys.
     *
     * @param Zend_Db_Table_Rowset_Abstract $pages
     * @return string
     */
    public static function listSurveys($surveys) {
        $html = self::HTML_DEFAULT_EMPTY;
        if ($surveys && count($surveys)) {
            $html .= '<table class="table table-striped table-bordered">';
            $html .= '<thead><tr><th class="text-center">Name</th><th class="text-center">Status</th>';
            $html .= '<th class="text-center">Created</th><th class="text-center">Modified</th></tr></thead>';
            $html .= '<tbody>';
            foreach ($surveys as $_survey) {
                $html .= '<tr rel="' . $_survey->id . '">';
                $html .= '<td>' . $_survey->name . '</td>';
                $html .= '<td>' . Repo_Survey::$statusLabels[$_survey->status] . '</td>';
                $html .= '<td>' . Functions_Common::formattedDay($_survey->created_datetime) . '</td>';
                $html .= '<td>' . Functions_Common::formattedDay($_survey->modified_datetime) . '</td>';
                $html .= '</tr>';
            }
            $html .= '</tbody>';
            $html .= '</table>';
        }
        return $html;
    }

    /**
     * List pages with surveys.
     *
     * @param $titlePageCollection
     * @return string
    */
    public static function listTitlePageSurveys($titlePageCollection) {
        $html = self::HTML_DEFAULT_EMPTY;
        if ($titlePageCollection && $titlePageCollection->count()) {
            $html .= '<table class="table table-striped table-bordered">';
            $html .= '<thead><tr><th class="text-center">Name</th><th class="text-center">Status</th>';
            $html .= '<th class="text-center">Created</th><th class="text-center">Modified</th></tr></thead>';
            $html .= '<tbody>';
            foreach ($titlePageCollection as $_pageSurvey) {
                $html .= '<tr rel="' . $_pageSurvey->page_id . '">';
                $html .= '<td>' . $_pageSurvey->name . '</td>';
                $html .= '<td>' . Repo_Survey::$statusLabels[$_pageSurvey->status] . '</td>';
                $html .= '<td>' . Functions_Common::formattedDay($_pageSurvey->created_datetime) . '</td>';
                $html .= '<td>' . Functions_Common::formattedDay($_pageSurvey->modified_datetime) . '</td>';
                $html .= '</tr>';
            }
            $html .= '</tbody>';
            $html .= '</table>';
        }
        return $html;
    }

    /**
     * List app registrant summaries.
     *
     * @param $studentLists
     * @return string
     */
    public static function listAppStudentRegistrants( $studentLists ) {
        $html = self::HTML_DEFAULT_EMPTY;
        if ($studentLists && $studentLists->count()) {
            $html .= '<table class="table table-striped table-bordered">';
            $html .= '<thead><tr><th class="text-center">Total Registrants</th><th class="text-center">App Title</th>';
            $html .= '<th class="text-center">Created</th><th class="text-center">Modified</th></tr></thead>';
            $html .= '<tbody>';
            foreach ($studentLists as $_studentList) {
                $html .= '<tr app_id="'.$_studentList->app_id.'">';
                $html .= '<td>' . $_studentList->num . '</td>';
                $html .= '<td>' . $_studentList->app_name . '</td>';
                $html .= '<td>' . Functions_Common::formattedDay($_studentList->created_datetime) . '</td>';
                $html .= '<td>' . Functions_Common::formattedDay($_studentList->modified_datetime) . '</td>';
                $html .= '</tr>';
            }
            $html .= '</tbody>';
            $html .= '</table>';
        }
        return $html;
    }

    /**
     * List questions.
     *
     * @param Zend_Db_Table_Rowset_Abstract $questions
     * @return string
     */
    public static function listQuestionEditGroup($questions, $tagName, $tagClasses) {
        //all data for Questions into sortable list

        $html = self::HTML_DEFAULT_EMPTY;
        if ($questions && count($questions)) {
            foreach ($questions as $_question) {
                $html .= '<' . $tagName . ' class="' . $tagClasses . '" rel="' . $_question['id'] . '" id="question_' . $_question['id'] . '">';
                $html .= $_question['question_english'];

                $html .= '<input type="hidden" id="id"  value="' . $_question['id'] . '" />';
                $html .= '<input type="hidden" id="client_id"  value="' . $_question['client_id'] . '" />';
                $html .= '<input type="hidden" id="survey_id"  value="' . $_question['survey_id'] . '" />';
                //$html .= '<input type="hidden" id="page_id"  value="'.$_question['page_id'].'" />';
                $html .= '<input type="hidden" id="order"  value="' . $_question['order'] . '" />';
                $html .= '<input type="hidden" id="type"  value="' . $_question['qtype'] . '" />';
                $html .= '<input type="hidden" id="question_english"  value="' . $_question['question_english'] . '" />';
                $html .= '<input type="hidden" id="question_french"  value="' . $_question['question_french'] . '" />';
                $html .= '<input type="hidden" id="question_id"  value="' . $_question['question_id'] . '" />';
                $html .= '<input type="hidden" id="correctanswer"  value="' . $_question['correct_answer'] . '" />';
                $html .= '<input type="hidden" id="order"  value="' . $_question['qorder'] . '" />';
                $html .= '<input type="hidden" id="image"  value="' . $_question['qimage'] . '" />';
                $html .= '<input type="hidden" id="video"  value="' . $_question['qvideo'] . '" />';
                $html .= '<input type="hidden" id="israndom"  value="' . $_question['israndom'] . '" />';
                $html .= '<input type="hidden" id="responsescope"  value="' . $_question['responsescope'] . '" />';

                //get the option data
                $repo_options = Repo_QuestionOption::getInstance();
                $options = $repo_options->getQuestionOptions($_question['id'])->toArray();
                $html .= '<input type="hidden" id="num_options"  value="' . count($options) . '" />';
                $count = 1;
                foreach ($options as $_option) {
                    foreach ($_option as &$element) {
                        $element = str_replace('"', '||', $element);
                        $element = str_replace('\'', '\\\'', $element);
                    }
                    $_option = json_encode($_option);
                    $_option = str_replace('"', '\'', $_option);
                    $html .= '<input type="hidden" id="options' . $count . '"  value="' . $_option . '" />';
                    $count++;
                }

                //get the response data
                $repo_responses = Repo_QuestionResponse::getInstance();
                $responses = $repo_responses->getQuestionResponses($_question['id'])->toArray();
                $html .= '<input type="hidden" id="num_reponses"  value="' . count($responses) . '" />';
                $count = 1;

                foreach ($responses as $_response) {
                    foreach ($_response as &$element) {
                        $element = str_replace('"', '||', $element);
                        $element = str_replace('\'', '\\\'', $element);
                    }
                    $_response = json_encode($_response);
                    $_response = str_replace('"', '\'', $_response);
                    $html .= '<input type="hidden" id="responses' . $count . '"  value="' . $_response . '" />';
                    $count++;
                }

                $html .= '<br/><span class="floatRight">';
                $html .= '<input type="button" style="position:absolute;bottom: 5px; right: -25px; box-shadow: 2px 2px 2px rgba(0,0,0,0.6);" class="btn btn-primary btn-xs" onclick="getQuestion(' . $_question['id'] . ')" value="EDIT"/>';
                $html .= '<button type="button" class="close delete-question-item-button" rel="' . $_question['id'] . '" >&times;</button>';
                $html .= '</span><br/>';
                $html .= '</' . $tagName . '>';
            }
        }
        return $html;
    }

    /**
     * List questions.
     *
     * @param Zend_Db_Table_Rowset_Abstract $questions
     * @return string
     */
    public static function listQuestionGroup($questions, $tagName, $tagClasses) {
        //all data for Questions into sortable list
        $html = self::HTML_DEFAULT_EMPTY;
        if ($questions && count($questions)) {
            foreach ($questions as $_question) {
                $html .= '<' . $tagName . ' class="' . $tagClasses . '" rel="' . $_question['id'] . '" id="question_' . $_question['id'] . '|' . $_question['survey_id'] . '">';
                //$html .= $_question['question_english'];
                $html .= $_question['question_id'];
                $html .= '<div class="page-list-item-callout">' . $_question['question_english'] . '</div>';

                $html .= '<input type="hidden" id="id"  value="' . $_question['id'] . '" />';
                $html .= '<input type="hidden" id="client_id"  value="' . $_question['client_id'] . '" />';
                $html .= '<input type="hidden" id="survey_id"  value="' . $_question['survey_id'] . '" />';

                $html .= '<input type="hidden" id="question_english"  value="' . $_question['question_english'] . '" />';
                $html .= '<input type="hidden" id="question_french"  value="' . $_question['question_french'] . '" />';
                $html .= '<input type="hidden" id="question_id"  value="' . $_question['question_id'] . '" />';

                $html .= '</' . $tagName . '>';
            }
        }
        return $html;
    }

    /**
     * List page type values.
     *
     * @param Zend_Db_Table_Rowset_Abstract $questions
     * @return string
     */
    public static function pageFormTypeFilter($baseLink, $linkClass, $selected = 0, $clientId = 0, $titleId = 0, $statusId = 0) {

        $allTypesLabel = 'All Types';
        $formTypes = array('Question' => 1, 'Survey' => 2, 'CEFeedback' => 3);

        if ($selected > 0) {
            foreach ($formTypes as $key => $value) {
                if ($value == $selected) {
                    $defaultLabel = $key;
                }
            }
        }

        $defaultLabel = isset($defaultLabel) ? $defaultLabel : $allTypesLabel;
        $baseLink .= ($clientId > 0) ? "client/" . $clientId . "/" : "";
        $baseLink .= ($statusId > 0) ? "status/" . $statusId . "/" : "";
        $baseLink .= ($titleId > 0) ? "ttlid/" . $titleId . "/" : "";
        $baseLink .= ($selected > 0) ? "pgtype/" . $selected . "/" : "";

        $html = '
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" id="page-type-dropdown" >'
                . '<span id="page-form-type-filter-default" rel="' . $selected . '">' . $defaultLabel . '</span> <span class="caret"></span>
            </button>
            <ul class="dropdown-menu" role="menu">
            <li><a href="' . $baseLink . '"'
                . ($linkClass ? ' class="' . $linkClass . '" ' : '')
                . '><b>' . $allTypesLabel . '</b></a></li>';



        foreach ($formTypes as $key => $value) {

            $html .= '<li><a href="' . $baseLink . $value . '" rel="' . $value . '"';
            $html .= ($linkClass ? ' class="' . $linkClass . '" ' : '');
            $html .= '>' . $key . '</a></li>';
        }
        $html .='</ul>';
        return $html;
    }

    /**
     * List page questions.
     *
     * @param Zend_Db_Table_Rowset_Abstract $questions
     * @return string
     */
    public static function listPageQuestionGroup($questions, $tagName, $tagClasses) {
        //all data for Questions into sortable list
        $html = self::HTML_DEFAULT_EMPTY;
        if ($questions && count($questions)) {
            foreach ($questions as $_question) {
                $html .= '<' . $tagName . ' class="' . $tagClasses . '" rel="' . $_question['id'] . '" id="question_' . $_question['id'] . '">';
                $html .= $_question['question_english'];

                $html .= '<input type="hidden" id="id"  value="' . $_question['id'] . '" />';
                $html .= '<input type="hidden" id="client_id"  value="' . $_question['client_id'] . '" />';
                $html .= '<input type="hidden" id="survey_id"  value="' . $_question['survey_id'] . '" />';
                $html .= '<input type="hidden" id="order"  value="' . $_question['order'] . '" />';
                $html .= '<input type="hidden" id="type"  value="' . $_question['qtype'] . '" />';
                $html .= '<input type="hidden" id="question_english"  value="' . $_question['question_english'] . '" />';
                $html .= '<input type="hidden" id="question_french"  value="' . $_question['question_french'] . '" />';
                $html .= '<input type="hidden" id="correctanswer"  value="' . $_question['correct_answer'] . '" />';
                $html .= '<input type="hidden" id="order"  value="' . $_question['qorder'] . '" />';
                $html .= '<input type="hidden" id="image"  value="' . $_question['qimage'] . '" />';
                $html .= '<input type="hidden" id="video"  value="' . $_question['qvideo'] . '" />';
                $html .= '<input type="hidden" id="israndom"  value="' . $_question['israndom'] . '" />';
                $html .= '<input type="hidden" id="responsescope"  value="' . $_question['responsescope'] . '" />';
                $html .= '</' . $tagName . '>';
            }
        }
        return $html;
    }

    /**
     * Get a title button dropdown.
     *
     * @param string $baseLink
     * @return string
     */
    public static function titleFilter($baseLink, $defaultLabel, $linkClass = false, $selected = false, $clientId = 0) {
        $allTitleLabel = 'All Titles';
        $defaultLabel = $defaultLabel ? $defaultLabel : $allTitleLabel;
        $baseLink .= ($clientId > 0) ? "client/" . $clientId . "/" : "";
        $baseLink .= ($selected !== false) ? "ttlid/" . $selected . "/" : "";

        $html = '
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" id="client-title-dropdown" rel="clientid' . $clientId . '" >'
                . '<span id="title-filter-default" rel="' . $selected . '">' . $defaultLabel . '</span> <span class="caret"></span>
            </button>
            <ul class="dropdown-menu" role="menu">
            <li><a href="' . $baseLink . '"'
                . ($linkClass ? ' class="' . $linkClass . '" ' : '')
                . '><b>' . $allTitleLabel . '</b></a></li>';
        if ($clientId > 0) {
            $titles = Repo_Title::getInstance()->getRows(array(array('where' => 'client_id = ?', 'bind' => $clientId)));
        } else {
            $titles = Repo_Title::getInstance()->getRows();
        }
        /*

          array(
          array(
          'where' => 'user_id = ?',
          'bind' => Auth_Wrapper_User::getUserId()
          )
          ),
          false, 0, 'id DESC'


          );

         */

        foreach ($titles as $_t) {

            $html .= '<li><a href="' . $baseLink . $_t->id . '" rel="' . $_t->id . '"';
            $html .= ($linkClass ? ' class="' . $linkClass . '" ' : '');
            $html .= '>' . $_t->name . '</a></li>';
        }
        $html .='</ul>';
        return $html;
    }

    /**
     * Get a package button dropdown.
     *
     * @param string $baseLink
     * @return string
     */

    public static function packageFilter($baseLink, $defaultLabel, $linkClass = false, $selected = false, $clientId = 0, $includeParentClient = true) {
        $allPackageLabel = 'All Packages:';
        $defaultLabel = $defaultLabel ? $defaultLabel : $allPackageLabel;
        $baseLink .= "package/";

        $html = '<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" id="client-package-dropdown" rel="clientid' . $clientId . '" >'
              . '<span id="package-filter-default" rel="' . $selected . '">' . $defaultLabel . '</span> <span class="caret"></span>
            </button>
            <ul class="dropdown-menu" role="menu">
            <li><a href="' . $baseLink . '"'
            . ($linkClass ? ' class="' . $linkClass . '" ' : '')
            . '><b>' . $allPackageLabel . '</b></a></li>';
        if ($clientId > 0) {
            $packages = Repo_Package::getInstance()->getClientPackages($clientId, false, $includeParentClient);
        } else {
            $packages = Repo_Package::getInstance()->getRows();
        }

        foreach ($packages as $_p) {
            $html .= '<li><a href="' . $baseLink . $_p->id . '" rel="' . $_p->id . '"';
            $html .= ($linkClass ? ' class="' . $linkClass . '" ' : '');
            $html .= '>' . $_p->name . '</a></li>';
        }
        $html .='</ul>';
        return $html;
    }

    /**
     * List questions
     *
     * @param Zend_Db_Table_Rowset_Abstract $questions
     * @return string
     */
    public static function listQuestions($questions) {
        $html = self::HTML_DEFAULT_EMPTY;
        if ($questions && $questions->count()) {
            $html .= '<table class="table table-striped table-bordered">';
            $html .= '<thead><tr><th class="text-center">Name</th><th class="text-center">Status</th>';
            $html .= '<th class="text-center">Created</th><th class="text-center">Modified</th><th></th></tr></thead>';
            $html .= '<tbody>';
            foreach ($questions as $_question) {
                $html .= '<tr rel="' . $_question->id . '">';
                $html .= '<td>' . $_question->question_english . '</td>';
                $html .= '<td>' . Functions_Common::formattedDay($_question->created_datetime) . '</td>';
                $html .= '<td>' . Functions_Common::formattedDay($_question->modified_datetime) . '</td>';
                $html .= '<td><div class="pull-right"><a class="btn btn-primary survey-detail"';
                $html .= ' href="/admin/client/survey-detail/id/' . $_question->id . '">Details</a></div></td>';
                $html .= '</tr>';
            }
            $html .= '</tbody>';
            $html .= '</table>';
        }
        return $html;
    }

    /**
     * List promote sessions for presenter.
     *
     * @param Zend_Db_Table_Rowset_Abstract $sessions
     * @return string
     */
    public static function listPromoteSessions($sessions) {
        $html = self::HTML_DEFAULT_EMPTY;
        if ($sessions && $sessions->count()) {
            $html .= '<table class="table table-striped table-bordered" style="display: table">';
            $html .= '<thead><tr><th class="text-center">Subject</th><th class="text-center">Start Time</th>';
            $html .= '<th></th></tr></thead>';
            $html .= '<tbody>';
            foreach ($sessions as $_session) {
                $html .= '<tr rel="' . $_session->id . '">';
                $html .= '<td>' . $_session->subject . '</td>';
                $html .= '<td>' . Functions_Common::formattedDay($_session->created_datetime, self::SHORT_DATE) . '</td>';
                $html .= '<td><div class="pull-right"><div class="btn-group"><a class="btn btn-primary"';
                $html .= ' href="/promote/presenter/session/' . $_session->ukey . '">Go to Session</a> <a class="btn btn-danger session-delete">Delete</a></div></div></td>';
                $html .= '</tr>';
            }
            $html .= '</tbody>';
            $html .= '</table>';
        }
        return $html;
    }

    /**
     * Creates a modal to add a new tag
     *
     * @return string
     */
    public static function createNewTag() {
        $html = self::HTML_DEFAULT_EMPTY;
        $html = "
    <div id='myModal' class='modal fade' role='dialog' aria-labelledby='myModal' aria-hidden='true'>
        <div class='modal-dialog'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <span class='close' data-dismiss='modal'><span aria-hidden='true'>×</span><span class='sr-only'>Close</span></span>
                    <!-- <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button> -->
                    <h4 class='modal-title'>Add New Tag</h4>
                </div>
                <div class='modal-body'>" . new Form_Admin_Client_Tag_Create() . "
                    <p class='text-warning'><small>If you don't save, your changes will be lost.</small></p>
                </div>
                <div class='modal-footer'>
                    <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
                    <button type='button' class='btn btn-primary'>Save changes</button>
                </div>
            </div>
        </div>
    </div>";
        return $html;
    }

    /**
     * Creates a modal to update an exsiting tag
     *
     * @return string
     */
    public static function updateExistingTag() {
        $html = self::HTML_DEFAULT_EMPTY;
        $html = "   <div id='updateExistingTag' class='modal fade' role='dialog' aria-labelledby='myModal' aria-hidden='true'>
        <div class='modal-dialog'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <span class='close' data-dismiss='modal'><span aria-hidden='true'>×</span><span class='sr-only'>Close</span></span>
                    <!-- <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button> -->
                    <h4 class='modal-title'>Edit Existing Tag</h4>
                </div>
                <div class='modal-body'>" . new Form_Admin_Client_Tag_Create() . "
                    <p class='text-warning'><small>If you don't save, your changes will be lost.</small></p>
                </div>
                <div class='modal-footer'>
                    <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
                    <button type='button' class='btn btn-primary'>Save changes</button>
                </div>
            </div>
        </div>
    </div>";
        return $html;
    }

    public static function addTagToSurveyModal() {
        $html = self::HTML_DEFAULT_EMPTY;
        $html = "   <div id='addTagToSurvey' class='modal fade' role='dialog' aria-labelledby='myModal' aria-hidden='true'>
        <div class='modal-dialog'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                    <h4 class='modal-title'>Add Tag to Survey</h4>
                </div>
                <div class='modal-body'>" . new Form_Admin_Client_Tag_Survey() . "
                </div>
               <div class='modal-footer'>
                    <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
                    <button type='button' class='btn btn-primary'>Save changes</button>
                </div>
            </div>
        </div>
        </div>";
        return $html;
    }

    public static function addTagToQuestionModal() {
        $html = self::HTML_DEFAULT_EMPTY;
        $questions = new Form_Admin_Client_Tag_Question;
        $html = '<div aria-hidden="true" aria-labelledby="myModal" role="dialog" class="modal fade in" id="addTagToQuestion" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" aria-hidden="true" data-dismiss="myModal" type="button">×</button>
                Assign tag to Question
            </div>
            <div class="modal-body">
                ' . $questions . '
            </div>
            <div class="modal-footer">
                <button class="btn btn-default" data-dismiss="modal" type="button">Close</button>
                <button class="btn btn-primary" type="button">Save changes</button>
            </div>
        </div>
    </div>
</div>';
        return $html;
    }

    public static function addOptionType(){
        $html = self::HTML_DEFAULT_EMPTY;
        $questionOptions = new Form_Admin_Client_Question_Option;
        $html = '<div aria-hidden="true" aria-labelledby="addOptionType" role="dialog" class="modal fade in" id="addOptionType" style="display:none;">'
                .'<div class="modal-dialog">'
                .'<div class="modal-content">'
                .'<div class="modal-header">'
                .'<button class="close" aria-hidden="true" data-dismiss="addOptionType" type="button">x</button>'
                .'Assign Option Type to Option'
                .'</div>'
                .'<div class="modal-body">'
                    .$questionOptions
                .'</div>'
                .'<div class="modal-footer">'
                .'<button class="btn btn-default" data-dismiss="modal" type="button">Close</button>'
                .'<button class="btn btn-primary" type="button">Save changes</button>'
                .'</div>'
                ."</div>"
                ."</div>"
                . '</div>';
        return $html;
    }

    /**
     * Create new client modal.
     *
     * @param Form_Abstract $form
     * @return string
     */
    public static function viewResourceLibraryRecord() {

        $html = '<div class="modal fade" id="viewResourceLibraryRecord" tabindex="-1" role="dialog" aria-labelledby="formModalLabel" aria-hidden="true" data-backdrop="">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <span class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></span>
                <h4 class="modal-title" id="">Resource Libarary</h4>
              </div>
              <div class="modal-body">
                        <div class="resourcelibrary_preview">
                            <iframe id="resourcelibrary_preview_pdf_iframe" src="" style="width: 383px;height: 100%;" frameborder="0"></iframe>
                            <img src="" style="width: 383px;height: 100%;" style="display:none;" />
                        </div>
                        <div class="resourcelibrary_detail">
                            <div class="resource_library_title">
                            </div>
                            <div class="div_clr5">
                                <label class="formElementLabel" >File Type :&nbsp;</label>
                                <div class="resource_library_file_type">
                                </div>
                            </div>
                            <div class="div_clr5">
                                <label class="formElementLabel" >Date Added :&nbsp;</label>
                                <div class="resource_library_date_added">
                                </div>
                            </div>
                            <div class="div_clr5" style="margin-top:60px;" >
                                <label class="formElementLabel" >Tags :&nbsp;</label>
                                <div class="resource_library_tags">
                                </div>
                            </div>
                            <div class="div_clr5 resource_library_download">
                                <a class="btn btn-success" id="resource_library_download_link" href="#" target="_blank">Download</a>
                            </div>
                        </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <!--<button type="button" class="btn btn-primary form-modal-submit">Submit</button>-->
              </div>
            </div><!-- /.modal-content -->
          </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->';
        return $html;
    }
}
