<?php
/**
 * This is the abstract class for repositories.
 * Basically, the repository will be dealing with database interaction
 * and database row holding.
 *
 * The reason for a middle repository class is to hold all of the information
 * that has been retrieved from the dabase and hold them in the memory so that
 * no repetitive query against the database is needed.
 */
abstract class Repo_Abstract
{
    /**
     * The database table.
     *
     * @var Zend_Db_Table_Abstract
     */
    protected $_dbTable;

    /**
     * The database view table if any
     *
     * @var Zend_Db_Table_Abstract
     */
    protected $_viewTable;

    /**
     * The rows saved. Indexed with primary key.
     *
     * @var array
     */
    protected $_rows;

    /**
     * Singleton protecting clone.
     *
     * @return Repo_Abstract
     */
    protected function __clone()
    {}

    /**
     * Singleton protecting construct.
     *
     * @return Repo_Abstract
     */
    protected function __construct()
    {
        $this->init();
    }

    /**
     * Init function for extending classes.
     *
     * @return void
     */
    protected function init()
    {}

    /**
     * Get the table for fetching rows.
     *
     * @return Zend_Db_Table_Abstract
     */
    protected function getFetchTable()
    {
        if ($this->_viewTable) {
            return $this->_viewTable;
        }
        return $this->_dbTable;
    }

    /**
     * Get the table for updating rows.
     *
     * @return Zend_Db_Table_Abstract
     */
    protected function getUpdateTable()
    {
        return $this->_dbTable;
    }

    /**
     * Update the row if a row is updated.
     *
     * @param Zend_Db_Table_Row_Abstract $row
     * @return boolean
     */
    public function updateRow($row)
    {
        if ($row instanceof Zend_Db_Table_Row_Abstract) {
            $dbTable = $this->getFetchTable();
            $primaryKey = $dbTable->info('primary');
            $this->_rows[$row->$primaryKey[1]] = $row;
            return true;
        }
        return false;
    }

    /**
     * Remove a row if a row is physically deleted.
     *
     * @param Zend_Db_Table_row_Abstract $row
     * @return boolean
     */
    public function removeRow($row)
    {
        if ($row instanceof Zend_Db_Table_Row_Abstract) {
            $dbTable = $this->getFetchTable();
            $primaryKey = $dbTable->info('primary');
            $key = $row->$primaryKey[1];
            if (isset($this->_rows[$key])) {
                unset($this->_rows[$key]);
                return true;
            }
        }
        return false;
    }

    /**
     * Get a row.
     *
     * @param mixed $search The search criteria for a row.
     * @param boolean $useUpdateTable Whether to use the update table.
     * @return Zend_Db_Table_Row_Abstract | false The row found or false if not found.
     */
    public function getRow($search, $useUpdateTable = false)
    {
        if ($useUpdateTable) {
            $dbTable = $this->getUpdateTable();
        } else {
            $dbTable = $this->getFetchTable();
        }
        $select = $dbTable->select();
        if (is_array($search)) {
            // Complex where with param binding
            foreach ($search as $_s) {
                if (is_array($_s) && isset($_s['where']) && isset($_s['bind'])) {
                    $select->where($_s['where'], $_s['bind']);
                } else {
                    $select->where($_s);
                }
            }
        } else {
            // Simple where
            $select->where($search);
        }
        $row = $dbTable->fetchRow($select);
        if ($row) {
            $primaryKey = $dbTable->info('primary');
            $this->_rows[$row->$primaryKey[1]] = $row;
        } else {
            return false;
        }
        return $row;
    }

    /**
     * Find the row by primary key.
     *
     * @param integer $id The primary id.
     * @return Zend_Db_Table_Row_Abstract | false The row found or false if not found.
     */
    public function findRow($id)
    {
        if (!isset($this->_rows[$id])) {
            $dbTable = $this->getFetchTable();
            $row = $dbTable->find($id)->current();
            if ($row) {
                $this->_rows[$id] = $row;
            } else {
                return false;
            }
        }
        return $this->_rows[$id];
    }

    /**
     * Find the update row.
     *
     * @param integer $id
     * @return Zend_Db_Table_Row_Abstract
     */
    public function findUpdateRow($id)
    {
        $dbTable = $this->getUpdateTable();
        $row = $dbTable->find($id)->current();
        if ($row) {
            return $row;
        }
        return false;
    }

    /**
     * Find the rows.
     *
     * @param mixed $search
     * @param integer $limit
     * @param integer $start
     * @param mixed $order
     * @param boolean $useUpdateTable Whether to use the update table.
     * @return Zend_Db_Table_Rowset_Abstract The found rowset or false if no row found.
     */
    public function getRows($search = false, $limit = false, $start = 0, $order = false, $useUpdateTable = false)
    {
        if ($useUpdateTable) {
            $dbTable = $this->getUpdateTable();
        } else {
            $dbTable = $this->getFetchTable();
        }
        $select = $dbTable->select();
        if ($search) {
           if (is_array($search)) {
                // Complex where with param binding
                foreach ($search as $_s) {
                    if (is_array($_s) && isset($_s['where']) && isset($_s['bind'])) {
                        $select->where($_s['where'], $_s['bind']);
                    } else {
                        $select->where($_s);
                    }
                }
            } else {
                // Simple where
                $select->where($search);
            }
        }

        if ($limit) {
            $select->limit($limit, $start);
        }
        if ($order) {
            $select->order($order);
        }
        $rows = $dbTable->fetchAll($select);
        if ($rows->count()) {
            $primaryKey = $dbTable->info('primary');
            foreach ($rows as $_row) {
                $this->_rows[$_row->$primaryKey[1]] = $_row;
            }
        }
        return $rows;
    }

    /**
     * Delete the rows by search.
     *
     * @param mixed $search
     * @return boolean
     */
    public function removeRowsBySearch($search = false)
    {
        $rows = $this->getRows($search, false, false, false, true);
        if ($rows) {
            foreach ($rows as $_r) {
                $_r->delete();
            }
        }
        return true;
    }
}