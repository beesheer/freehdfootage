<?php
/**
 * This is the repository for holding all of the teams.
 */
class Repo_Tag extends Repo_Abstract{

    const ADD_TAG_CLIENT_ERROR_NAME_EXISTS = 'tag-for-client-exists';

     /**
     * The only available instance of Repo_Tag.
     *
     * @var Repo_Tag
     */
    protected static $_instance;

    /**
     * An array of hierarchical tags.
     *
     * @var array
     */
    protected $_tagTreeCache = array();

    /**
     * Returns an instance.
     *
     * Singleton pattern implementation.
     *
     * @return Repo_Tag
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
        $this->_dbTable = new Db_Table_Tag();
    }

    /*
     * Add a new tag
     *
     * @param array $data
     */
    public function addTag($data) {

        $dbTable = $this->getUpdateTable();

        // Check name/client existence.
        $row = $this->getRow(
            array(
                array(
                    'where' => 'name = ?',
                    'bind' => $data['name']
                ),
                array(
                    'where' => 'client_id = ?',
                    'bind' => $data['client_id']
                )
            )
        );

        if ($row) {
            return self::ADD_TAG_CLIENT_ERROR_NAME_EXISTS;
        }

        $this->getFetchTable()->insert($data);
    }

    /**
     * Add tag.
     * @param integer $tag
     * @param integer $clientId
     * @param integer $parentTagId
     * @return integer
     */
    public function addNew( $tag, $clientId, $parentTagId = false )
    {

        $dbTable = $this->getUpdateTable();

        // Check name/client existence.
        $row = $this->getRow(
            array(
                array(
                    'where' => 'name = ?',
                    'bind' => $tag
                ),
                array(
                    'where' => 'client_id = ?',
                    'bind' => $clientId
                )
            )
        );

        if ($row) {
            return self::ADD_TAG_CLIENT_ERROR_NAME_EXISTS;
        }

        $rowData = (is_numeric($parentTagId) && (int)($parentTagId) !== 0 ) ? array( 'name' => $tag, 'client_id' => $clientId, 'parent_id' => $parentTagId ) : array( 'name' => $tag, 'client_id' => $clientId ) ;

        $newRow = $dbTable->createRow(
            $rowData
        );
        try {
            $newRow->save();
        } catch (Exception $e) {
            return 'Creating tag error: ' . $e->getMessage();
        }

        return $newRow->id;
    }


    /*
     *Delete an existing child tag
     *
     * @param string $where
     */
    public function removeTag($where){
        try{
        $this->getFetchTable()->delete($where);
        }catch(Exception $e){
            echo $e->getMessage();
        }
    }

    /*
     * Updates an exsiting child tag
     *
     * @param array $data
     * @param string $where
     */
    public function updateTag($data,$where){
        $this->getFetchTable()->update($data, $where);
    }

    /**
     * Get a list of tags for a client based on a search.
     *
     * @param integer $clientId
     * @param string $tag
     */
    public function getClientTags($clientId, $tag)
    {
        $dbTable = $this->getFetchTable();
        $sql = 'select name from tag where client_id = ?';
        $binds = array($clientId);
        $tag = trim($tag);
        if (!empty($tag)) {
            $tag = '%' . $tag . '%';
            $sql .= ' AND name like ?';
            $binds[] = $tag;
        }
        $tags = $dbTable->getAdapter()->fetchCol($sql, $binds);
        return $tags;
    }

    /**
     * Get tag id by client id and tag name.
     *
     * @param mixed $clientId
     * @param mixed $tag
     * @return mixed
     */
    public function getTagId($clientId, $tag)
    {
        $row = $this->getRow(array(
            array(
                'where' => 'client_id = ?',
                'bind' => $clientId
            ),
            array(
                'where' => 'name = ?',
                'bind' => $tag
            )
        ));
        if ($row) {
            return $row->id;
        } else {
            return $this->addNew($tag, $clientId);
        }
        return false;
    }



    // parent/child tree methods

    /**
     * Get tag hierarchical array.
     *
     * @return array
     */
    public function getTagTree( $clientId = false )
    {
        if (empty($this->_tagTreeCache)) {
            $this->_tagTreeCache = $this->_generateTagTree( $clientId );
        }

        return $this->_tagTreeCache;
    }

    /**
     * Generate tag tree.
     *
     */
    protected function _generateTagTree( $clientId = false )
    {
        $tags = $this->getChildTag( false, $clientId );
        return $tags;
    }

    /**
     * Get a list of child tags based on parent id.
     *
     * @param integer $parentId
     * @return array
     */
    public function getChildTag($parentId = false, $clientId = false)
    {
        $tagArray = array();
        $search = array();
        if ($parentId || $clientId) {
            if($parentId)
            $search[] = array(
                'where' => 'parent_id = ?',
                'bind' => (int) $parentId
            );
            if ($clientId) {
                if (!$parentId) {
                    $search[] = 'parent_id IS NULL OR parent_id = 0';
                }
                $search[] = array(
                    'where' => 'client_id = ?',
                    'bind' => (int) $clientId
                );
            }

        } else {
            $search = 'parent_id IS NULL OR parent_id = 0';
        }

        $tags = $this->getRows($search, false, false, 'name ASC');

        if ($tags) {
            foreach ($tags as $_tag) {
                $_tagItem = $_tag->toArray();
                $_childTags = $this->getChildTag( $_tag->id, $clientId);
                $_tagItem['children'] = $_childTags;
                $tagArray[] = $_tagItem;
            }
        }

        return $tagArray;
    }

    /**
     * Get parent tags for a tag.
     *
     * @param mixed $tagId
     * @return array Parent tag ids.
     */
    public function getParentTags($tagId)
    {
        if (!$tagId) {
            return false;
        }

        $tags = $this->getRows(false, false, false, false, true)->toArray();
        if (empty($tags)) {
            return false;
        }

        $currentTagId = $tagId;
        $parentTags = array();
        while($currentTagId) {
            foreach ($tags as $_tag) {
                if ($_tag['id'] == $currentTagId) {
                    $currentTagId = $_tag['parent_id'];
                    if ($_tag['id'] != $tagId) {
                        $parentTags[] = $_tag['id'];
                    }
                    break;
                }
            }
        }
        return $parentTags;
    }

    /**
     * Flatten a tree tags list to an array of tags.
     *
     * @param array $tags
     * @return array
     */
    public function flattenTags($tags)
    {
        if (!$tags || !is_array($tags)) {
            return false;
        }
        $flatTags = array();
        foreach ($tags as $_c) {
            $flatTags[] = $_c;
            if (isset($_c['children']) && is_array($_c['children']) && count($_c['children'])) {
                $childTags = $this->flattenTags($_c['children']);
                $flatTags = array_merge($flatTags, $childTags);
            }
        }
        return $flatTags;
    }


}