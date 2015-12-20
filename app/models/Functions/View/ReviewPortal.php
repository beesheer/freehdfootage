<?php
/**
 * The central place to host all of the view helpers for review portal.
 */
class Functions_View_ReviewPortal extends Functions_View
{
    /**
     * Nav tabs on top.
     *
     * @param integer $clientId
     * @param integer $portalId
     * @return string
     */
    public static function navTabs($clientId = false, $portalId = false, $activeSection = 'portals', $baseLink = '/admin/review-portal', $showPortals = false)
    {
        $portalLink = $showPortals ? '<li id="portal-nav-portals"><a href="' . $baseLink . '/index/client/' . $clientId . '">Portals</a></li>' : '';
        $html = <<<NAV
            <ul class="nav nav-tabs">
                {$portalLink}
                <li id="portal-nav-pages"><a href="{$baseLink}/pages/id/{$portalId}" class="portalLinks">Pages</a></li>
                <li id="portal-nav-comments"><a href="{$baseLink}/comments/id/{$portalId}" class="portalLinks">Comments</a></li>
                <li id="portal-nav-pdf"><a href="{$baseLink}/pdf/id/{$portalId}" class="portalLinks">PDF</a></li>
                <li id="portal-nav-docs"><a href="{$baseLink}/documents/id/{$portalId}" class="portalLinks">Documents</a></li>
                <li id="portal-nav-details"><a href="{$baseLink}/details/id/{$portalId}" class="portalLinks">Details</a></li>
                <li id="portal-nav-settings"><a href="{$baseLink}/settings/id/{$portalId}" class="portalLinks">Page Settings</a></li>
            </ul>
            <script type="text/javascript">
                $(function(){
                    $("#portal-nav-{$activeSection}").addClass("active");
                });
            </script>
            <div class="spacer">&nbsp;</div>
NAV;
        return $html;
    }

    /**
     * Vertical page nav for pages tab in review portal.
     *
     * @param Zend_Db_Table_Rowset_Abstract $pages
     * @param integer $currentPageId
     * @return string
     */
    public static function pageNav($pages, $currentPageId = false, $isAdmin = true)
    {
        $html = Functions_View::HTML_DEFAULT_EMPTY;
        $baseLink = $isAdmin ? '/admin/review-portal' : '/client/review-portal';
        if ($pages && $pages->count()) {
            $html .= '<div style="max-height: 800px; overflow-y: auto; overflow-x: hidden;"><div id="page-thumbnail" class="list-group">';
            foreach ($pages as $_p) {
                $_page = new Object_Page($_p->id);
                $status= Repo_PageStatus::getInstance()->getLabel($_p->portal_page_status);
                $html .= '<a href="' . $baseLink . '/pages/id/' . $_p->portal_id . '/page/' . $_p->id . '" rel="' . $_p->id . '" class="list-group-item' . ($currentPageId == $_p->id ? ' active' : '') . '" title="' . $status . '">';
                $html .= '<img src="' . $_page->getThumbImageLink(false) . '" width="100%" />';
                $html .= '<span class="page name">' . $_p->name . '</span>';
                $icon = "progress";
                if ( $status == "In progress")
                    $icon = "progress";
                else if ( $status == "Ready for Review")
                    $icon = "complete";
                else if ( $status == "Approved")
                    $icon = "approved";
                else if ( $status == "Unapproved")
                    $icon = "unapproved";
                $html .= '<div class="page status ' . $icon . '"></div>';
                $html .= '</a>';
            }
            $html .= '</div></div>';
        }
        return $html;
    }

    /**
     * Vertical doc nav for documents tab in review portal.
     *
     * @param Zend_Db_Table_Rowset_Abstract $docs
     * @param integer $currentDocId
     * @return string
     */
    public static function docNav($docs, $currentDocId = false, $isAdmin = true)
    {
        $html = Functions_View::HTML_DEFAULT_EMPTY;
        $baseLink = $isAdmin ? '/admin/review-portal' : '/client/review-portal';
        if ($docs && $docs->count()) {
            $html .= '<div style="max-height: 800px; overflow-y: auto; overflow-x: hidden;"><div id="page-thumbnail" class="list-group">';
            foreach ($docs as $_d) {
                $html .= '<a href="' . $baseLink . '/documents/id/' . $_d->portal_id . '/doc/' . $_d->id . '" rel="' . $_d->id . '" class="list-group-item' . ($currentDocId == $_d->id ? ' active' : '') . '">';
                $html .= $_d->name;
                $html .= '</a>';
            }
            $html .= '</div></div>';
        }
        return $html;
    }

    /**
     * List portals.
     *
     * @param Zend_Db_Table_Rowset_Abstract $portals
     * @return string
     */
    public static function listPortals($portals, $showClientName = false, $clientIdFilter = array())
    {
        $html = Functions_View::HTML_DEFAULT_EMPTY;
        if ($portals && count($portals)) {
            $html .= '<table class="table table-striped table-bordered" style="display: table">';
            $html .= '<thead><tr>';
            if ($showClientName) {
                $html .= '<th class="text-center">Client</th>';
            }
            $html .= '<th class="text-center">Portal</th><th class="text-center">Created</th>';
            $html .= '<th class="text-center">Last Modified</th><th class="text-center">Status</th>';
            $html .= '<th class="text-center">Description</th></tr></thead>';
            $html .= '<tbody>';
            foreach ($portals as $_portal) {
                if (!empty($clientIdFilter)) {
                    if (!in_array($_portal['client_id'], $clientIdFilter)) {
                        continue;
                    }
                }
                $html .= '<tr rel="' . $_portal['id'] . '" class="activatePortal">';
                if ($showClientName) {
                    $html .= '<td>' . $_portal['client_name'] . '</td>';
                }
                $html .= '<td>' . $_portal['name'] . '</td>';
                $html .= '<td>' . Functions_Common::formattedDay($_portal['created_datetime'], parent::STD_DATE_FORMAT) . '</td>';
                $html .= '<td>' . Functions_Common::formattedDay($_portal['modified_datetime'], parent::STD_DATE_FORMAT) . '</td>';
                $html .= '<td>' . Repo_ReviewPortal::$statusLabels[$_portal['status']] . '</td>';
                $html .= '<td>' . $_portal['description'] . '</td>';
                $html .= '</tr>';
            }
            $html .= '</tbody>';
            $html .= '</table>';
        }
        return $html;
    }

    /**
     * List portal threads.
     *
     * @param Zend_Db_Table_Rowset_Abstract $portals
     * @return string
     */
    public static function listThreadTable($threads, $baseLink = '/admin/review-portal')
    {
        $html = Functions_View::HTML_DEFAULT_EMPTY;
        if ($threads && $threads->count()) {
            $html .= '<table class="table table-striped sortable">';
            $html .= '<thead><tr><th class="text-center">Approved</th><th class="text-center">Page</th>';
            $html .= '<th class="text-center">Author</th><th class="text-center" data-defaultsort="desc" width="12%">Created On</th><th class="text-center| width="12%">Last Modified</th>';
            $html .= '<th class="text-center" data-defaultsort="disabled" width="40%">Comment</th></tr></thead>';
            $html .= '<tbody>';
            foreach ($threads as $_thread) {
                $_portalPage = new Object_ReviewPortalPage($_thread->portal_page_id);
                $_page = new Object_Page($_portalPage->page_id);
                $html .= '<tr rel="' . $_thread->id . '" ppid="' . $_thread->portal_page_id . '" class="noClickThrough">';
                $html .= '<td data-value=' . $_thread->is_approved . '"><input class="comment-approved" type="checkbox" rel="' . $_thread->id. '" ' . ($_thread->is_approved ? ' checked="checked"' : '') . '/></td>';
                $html .= '<td data-value=' . $_page->name . '"><a href="' . $baseLink . '/pages/id/' . $_portalPage->portal_id . '/page/' . $_page->id . '">' . $_page->name . '</a> (' .  Repo_PageStatus::getInstance()->getLabel($_portalPage->status) . ')</td>';
                $html .= '<td>' . $_thread->firstname . ' ' . $_thread->surname . '</td>';
                $html .= '<td>' . Functions_Common::formattedDay($_thread->post_datetime, parent::SHORT_DATE) . '</td>';
                $html .= '<td>' . ($_thread->last_modified ? Functions_Common::formattedDay($_thread->last_modified, parent::SHORT_DATE) : 'n/a') . '</td>';
                $html .= '<td><div class="pull-right"><a class="reply-handle" href="#" rel="thread-reply-' . $_thread->id . '">Reply</a>';
                $html .= ($_thread->user_id == Auth_Wrapper_User::getUserId() ? ' | <a class="edit-handle" href="#" rel="thread-update-' . $_thread->id . '">Edit</a>' : '');
                $html .= '</div><div>' . $_thread->body;
                $html .= '</div>';
                // Edit
                $html .= '<div class="row thread-update-' . $_thread->id . '" . style="display: none">
                            <div class="col-md-8">
                                <textarea rows="3" width="100%" class="form-control edit-thread-content">' . $_thread->body . '</textarea>
                            </div>
                            <div class="col-md-4">
                                <div class="btn-group"><button class="btn btn-primary edit-thread" rel="' . $_thread->id . '">Update Comment</button><button class="btn btn-warning edit-thread-cancel" rel="' . $_thread->id . '">Cancel</button></div>
                            </div>
                        </div>';
                // Reply
                $html .= '<div class="row thread-reply-' . $_thread->id . '" . style="display: none">
                            <div class="col-md-8">
                                <textarea rows="3" width="100%" class="form-control new-thread"></textarea>
                            </div>
                            <div class="col-md-4">
                                <div class="btn-group"><button class="btn btn-primary add-new-thread" rel="' . $_thread->id . '">Reply</button><button class="btn btn-warning add-new-thread-cancel" rel="' . $_thread->id . '">Cancel</button></div>
                            </div>
                        </div></td>';
                $html .= '</tr>';
            }
            $html .= '</tbody>';
            $html .= '</table>';
        }
        return $html;
    }

    /**
     * List page status history.
     *
     * @param Zend_Db_Table_Rowset_Abstract $portals
     * @return string
     */
    public static function listStatusHistory($statuses)
    {
        $html = Functions_View::HTML_DEFAULT_EMPTY;
        if ($statuses && $statuses->count()) {
            $html .= '<table class="table table-striped">';
            $html .= '<tbody>';
            foreach ($statuses as $_status) {
                $html .= '<tr rel="' . $_status->id . '" class="noClickThrough">';
                $html .= '<td>' . Repo_PageStatus::getInstance()->getLabel($_status->status) . '</td>';
                $html .= '<td>' . $_status->firstname . ' ' . $_status->surname . '</td>';
                $html .= '<td>' . Functions_Common::formattedDay($_status->status_datetime, parent::STD_DATE_FORMAT) . '</td>';
                $html .= '</tr>';
            }
            $html .= '</tbody>';
            $html .= '</table>';
        }
        return $html;
    }

    /**
     * New status.
     *
     * @return string
     */
    public static function newStatus($status)
    {
        $currentStatus = $status ? Repo_PageStatus::getInstance()->getLabel($status) : 'Change Status';
        $statuses = Repo_PageStatus::getInstance()->getSelectArray();
        $html = '<div class="btn-group"><button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">';
        $html .= '<span id="status-label" rel="0">' . $currentStatus . '</span> <span class="caret"></span></button>';
        $html .= '<ul class="dropdown-menu">';
        foreach ($statuses as $_k => $_l) {
            $html .= '<li class="changeStatus" rel="' . $_k . '" ><a href="javascript:void(0)" onclick="changePageStatus(this)">' . $_l . '</a></li>';
        }
        $html .= '</ul></div> <div class="btn-group"><button class="btn btn-primary" id="add-new-status">Save</button></div>';

        return $html;
    }

    /**
     * Output a thread tree.
     *
     * @param Zend_Db_Table_Rowset_Abstract $threads
     * @return string
     */
    public static function threadTree($threads)
    {
        $html = Functions_View::HTML_DEFAULT_EMPTY;
        if ($threads && $threads->count()) {
            $threads = $threads->toArray();
            $html .= '<ul class="ul-comments">';
            $html .= self::threadList($threads, false);
            $html .= '</ul>';
        }
        return $html;
    }

    /**
     * List one level of threads. Used as a recursive function.
     *
     * @param array $threads
     * @param integer $parentId
     * @return string
     */
    public static function threadList($threads, $parentId = false)
    {
        $html = Functions_View::HTML_DEFAULT_EMPTY;
        $childHtml = '';
        if ($threads && is_array($threads)){
            foreach ($threads as $_thread) {
                if ((int)$_thread['parent_id'] == (int)$parentId) {
                    $html .= '<li class="list-group-item">';
                    $html .= '<h6><div class="pull-right">';
                    $html .= '</div><!--<input class="comment-approved" type="checkbox" rel="' . $_thread['id'] . '" ' . ($_thread['is_approved'] ? ' checked="checked"' : '') . '/>--> ';
                    $html .= $_thread['firstname'] . ' ' . $_thread['surname'] . ' says on ';
                    $html .= '<span class="small">' . Functions_Common::formattedDay($_thread['post_datetime'], parent::STD_DATE_FORMAT) . '</span></h6>';
                    $html .= '<div class="row thread-update-' . $_thread['id'] . '-body">' . $_thread['body'];
                    $html .= '</div>';
                    $html .= '<div class="row thread-update-' . $_thread['id'] . '-action" style="display: none">
                                    <div class="btn-group"><button class="btn btn-primary edit-thread" rel="' . $_thread['id'] . '">Update Comment</button><button class="btn btn-warning edit-thread-cancel" rel="' . $_thread['id'] . '">Cancel</button></div>
                                </div>';
                    $html .= '<div>';
                    $html .= '<a class="reply-handle" href="#" rel="thread-reply-' . $_thread['id'] . '">Reply</a>';
                    $html .= ($_thread['user_id'] == Auth_Wrapper_User::getUserId() ? ' | <a class="edit-handle" href="#" rel="thread-update-' . $_thread['id'] . '">Edit</a>' : '');
                    $html .= ($_thread['user_id'] == Auth_Wrapper_User::getUserId() ? ' | <a class="delete-handle" href="#" rel="' . $_thread['id'] . '">Delete</a>' : '');
                    $html .= '</div>';
                    // Edit
                    /*$html .= '<div class="row thread-update-' . $_thread['id'] . '" . style="display: none">
                                <div class="col-md-8">
                                    <textarea rows="3" width="100%" class="form-control edit-thread-content">' . $_thread['body'] . '</textarea>
                                </div>
                                <div class="col-md-4">
                                    <div class="btn-group"><button class="btn btn-primary edit-thread" rel="' . $_thread['id'] . '">Update Comment</button><button class="btn btn-warning edit-thread-cancel" rel="' . $_thread['id'] . '">Cancel</button></div>
                                </div>
                            </div>';*/
                    // Reply
                    $html .= '<div class="row thread-reply-' . $_thread['id'] . '" . style="display: none">
                                <div class="col-md-8">
                                    <textarea rows="3" width="100%" class="form-control new-thread"></textarea>
                                </div>
                                <div class="col-md-4">
                                    <div class="btn-group"><button class="btn btn-primary add-new-thread" rel="' . $_thread['id'] . '">Reply</button><button class="btn btn-warning add-new-thread-cancel" rel="' . $_thread['id'] . '">Cancel</button></div>
                                </div>
                            </div>';
                    $childHtml = self::threadList($threads, $_thread['id']);
                    if (!empty($childHtml)) {
                        $html .= '<div>&nbsp;</div><ul>' . $childHtml . '</ul></li>';
                    } else {
                        $html .= '</li>';
                    }
                }
            }
        }
        return $html;
    }

    /**
     * Using carousel to show a list of images.
     *
     * @param array $imageLinks Array of image public links
     * @return string
     */
    public static function slider($imageLinks, $id = 'imageSlider')
    {
        if (!is_array($imageLinks) || empty($imageLinks)) {
            return parent::HTML_DEFAULT_EMPTY;
        }
        $html = '<div class="previewScreenshots" style="display: none"><div id="' . $id . '" class="carousel slide" data-ride="carousel">';
        $indicatorHtml = '<ol class="carousel-indicators">';
        $wrapperHtml = '<div class="carousel-inner">';
        $count = 0;
        foreach ($imageLinks as $_img) {
            $indicatorHtml .= '<li data-target="#' . $id . '" data-slide-to="' . $count . '"' . ($count == 0 ? ' class="active"' : '') . '</li>';
            $wrapperHtml .= '<div class="item ' . ($count == 0 ? 'active' : '') . '"><center><img src="' . $_img . '" style="max-width: 1024px; max-height: 768px;" />' . '</center></div>';
            $count++;
        }
        $indicatorHtml .= '</ol>';
        $wrapperHtml .= '</div>';

        $html .= $indicatorHtml . $wrapperHtml
            . '<!-- Controls -->
              <a class="left carousel-control" href="#' . $id . '" data-slide="prev">
                <span class="glyphicon glyphicon-chevron-left"></span>
              </a>
              <a class="right carousel-control" href="#' . $id . '" data-slide="next">
                <span class="glyphicon glyphicon-chevron-right"></span>
              </a>
            </div></div>
        ';
        return $html;
    }

    /**
     * List page list for screenshot settings.
     *
     * @param Zend_Db_Table_Rowset_Abstract $pages
     * @return string
     */
    public static function pageScreenshotSettings($pages, $title = "Use static screenshots in PDF", $key = 'screenshot_type')
    {
        $html = Functions_View::HTML_DEFAULT_EMPTY;
        if ($pages && $pages->count()) {
            $html .= '<table class="table table-striped">';
            $html .= '<thead><tr>';
            $html .= '<th><div class="checkbox" id="checkAll"></div> ' . $title . '</th>';
            $html .= '<th>Portal Pages</th></tr></thead>';
            $html .= '<tbody>';
            foreach ($pages as $_page) {
                $html .= '<tr rel="' . $_page->id . '" class="noClickThrough">';
                $html .= '<td><div class="checkbox ' . ($_page->$key == Repo_Page::SCREENSHOT_TYPE_STATIC ? 'checked' : '') . '"></div></td>';
                $html .= '<td>' . $_page->name . '</td>';
                $html .= '</tr>';
            }
            $html .= '</tbody>';
            $html .= '</table>';
        }
        return $html;
    }
}