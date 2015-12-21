<?php
/**
 * This is the repository for holding all of the videos.
 */
class Repo_Query extends Repo_Abstract
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
        $this->_dbTable = new Db_Table_Query();
    }

    /**
     * Add.
     *
     * @param integer $q
     * @return integer
     */
    public function addNew($q)
    {
        $dbTable = $this->getUpdateTable();
        $row = $this->getRow(array(
            array(
                'where' => 'q = ?',
                'bind' => $q
            )
        ), true);
        if ($row) {
            $row->count++;
            $row->save();
        } else {
            $rowData = array(
                'q' => $q,
                'count' => 1
            );

            $row = $dbTable->createRow(
                $rowData
            );
            try {
                $row->save();
            } catch (Exception $e) {
                return 'Creating video error: ' . $e->getMessage();
            }
        }

        return $row->id;
    }

    /**
    * put your comment there...
    *
    */
    public function getTopQueries()
    {
        return $this->getRows(false, false, false, 'count DESC', false);
    }
}