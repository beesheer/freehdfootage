<?php
/**
 * This is the repository for holding all of the videos.
 */
class Repo_Video extends Repo_Abstract
{

     /**
     * The only available instance of Repo_Video.
     *
     * @var Repo_Video
     */
    protected static $_instance;

    /**
     * Returns an instance.
     *
     * Singleton pattern implementation.
     *
     * @return Repo_Video
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Initialize some of the basic properties.
     *
     * @return void
     */
    protected function init()
    {
        $this->_dbTable = new Db_Table_Video();
    }

    /**
     * Add.
     *
     * @param integer $tag
     * @param integer $clientId
     * @param integer $parentTagId
     * @return integer
     */
    public function addNew($name, $description, $youtubeId, $filePath = '')
    {
        $dbTable = $this->getUpdateTable();
        $rowData = array(
            'name' => $name,
            'description' => $description,
            'created_datetime' => date('Y-m-d H:i:s'),
            'youtube_id' => $youtubeId,
            'filepath' => $filePath
        );

        $newRow = $dbTable->createRow(
            $rowData
        );
        try {
            $newRow->save();
        } catch (Exception $e) {
            return 'Creating video error: ' . $e->getMessage();
        }

        return $newRow->id;
    }

    /**
     * Get a list of videos.
     *
     * @param mixed $q
     * @param mixed $start
     * @param mixed $limit
     * @param mixed $order
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function getVideos($q = false, $start = 0, $limit = 10, $order = 'name ASC')
    {
        $where = false;
        if ($q) {
            $q = '%' . $q . '%';
            $where = array(
                array(
                    'where' => 'name like ?',
                    'bind' => $q
                )
            );
        }
        return $this->getRows($where, $limit, $start, $order);
    }

    /**
     * Get count.
     *
     * @return integer
     */
    public function getVideosCount($q = false)
    {
        $sql = 'select count(*) from video';
        if ($q) {
            $q = '%' . $q . '%';
            $count = $this->_dbTable->getDefaultAdapter()->fetchOne($sql . ' where name like ?', array($q));
        } else {
            $count = $this->_dbTable->getDefaultAdapter()->fetchOne($sql);
        }
        return (int)$count;
    }
}