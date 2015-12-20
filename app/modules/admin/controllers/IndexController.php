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
        $identity = Zend_Auth::getInstance()->getIdentity();
        $user = new Object_User($identity->id);
        if ($user->isUserSuperAdmin()) {
            // Get the list of sql files need to be run
            $uninstalledFiles = Manager_Install_UpdateSqlFile::getInstance()->getUninstalledFiles();
            if (count($uninstalledFiles)) {
                $this->_redirect('/admin/index/sqls');
            }
        }
	}

    /**
     * Run sql files first.
     *
     */
    public function sqlsAction()
    {
        // List the uninstalled sql files
        $this->view->uninstalledFiles = Manager_Install_UpdateSqlFile::getInstance()->getUninstalledFiles();
    }

    public function setInstallDateAction()
    {
        $installDate = $this->_request->getParam('date');
        if (Manager_Install_UpdateSqlFile::getInstance()->setInstallDate($installDate)) {
            $this->_helper->getHelper('FlashMessenger')->addMessage("Set install date to: " . $installDate);
            $this->_redirect('/admin/index/sqls');
        } else {
            print 'Failed to set install date: ' . $installDate;
            die;
        }
    }

    /**
     * Run update sql.
     *
     */
    public function runSqlAction()
    {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        if (isset($_REQUEST['updateSql']) && ($file = $_REQUEST['updateSql'])) {
            $result = Manager_Install_UpdateSqlFile::getInstance()->runFileUpdate($file, $this->_helper->getHelper('Redirector'));
            if ($result === true) {
                $this->_helper->getHelper('FlashMessenger')->addMessage("Installed update sql file: " . $file);
                $this->_redirect('/admin/index/sqls');
            } else {
                die($result);
            }
        }
        exit(0);
    }

    /**
     * View update sql.
     *
     */
    public function viewSqlAction()
    {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        if (isset($_REQUEST['updateSql']) && ($file = $_REQUEST['updateSql'])) {
            $sql = Manager_Install_UpdateSqlFile::getInstance()->viewFile($file);
            print '<pre>' . $sql . '</pre>';
        }
        exit(0);
    }

    /**
     * Show db content.
     *
     */
    public function testDbTableAction()
    {
        $tableName = $this->_request->getParam('t');
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        print '<pre>' . print_r($db->fetchAll('select * from ' . $tableName), 1) . '</pre>';
        die;
    }

    /**
     * Screen capture test;
     *
     */
    public function screenCaptureAction()
    {
        $id = $this->_request->getParam('id');
        $image = Manager_ScreenCapture_Page::getInstance()->getScreenshot($id);
        var_dump($image);
        exit(0);
    }

    /**
     * PDF test.
     *
     */
    public function pdfAction()
    {
        $id = $this->_request->getParam('id');
        $pdf = Manager_Pdf_ReviewPortal::getInstance()->getPdf($id);
        var_dump($pdf);
        exit(0);
    }

    /**
     * Public screenshots test.
     *
     */
    public function screenshotsAction()
    {
        $id = $this->_request->getParam('id');
        $page = new Object_page($id);
        $files = $page->getStaticScreenshots();
        Functions_Common::pre($files);
        exit(0);
    }

    /**
     * Screen capture for pdf template test.
     *
     */
    public function screenCapturePdfTemplateAction()
    {
        $id = $this->_request->getParam('id');
        // Set tokens test
        /*$tokens = array(
            'first_name' => 'BINinnnn',
            'last_name' => 'Xu'
        );*/
        $pdf = Manager_ScreenCapture_PdfTemplate::getInstance()->getPdf($id);
        $template = new Object_PdfTemplate($id);

        // Download it
        // Streaming out
        $filePath = $template->getFolderPath() . DS . $pdf . '.pdf';
        $fileName = $pdf . '.pdf';
        if (file_exists($filePath)) {
            $fp = fopen($filePath, 'rb');
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header("Content-Disposition: attachment; filename=" . $fileName);
            // Download
            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: public");
            fpassthru($fp);
            exit(0);
        } else {
            print 'Can not preview pdf: ' . $id;
            exit(0);
        }
        exit(0);
    }

    /**
     * Test publish a new version for a page.
     *
     */
    public function testPagePublishNewVersionAction()
    {
        $id = $this->_request->getParam('id');
        $page = new Object_Page($id);
        if (!$page->getId()) {
            die('Invalid page id ' . $id);
        }
        $pageVersionId = $page->publishNewVersion();
        if ((int)$pageVersionId == 0) {
            print $pageVersionId;
            exit(0);
        }
        print $page->getVersionPreviewLink($pageVersionId);
        exit(0);
    }
}