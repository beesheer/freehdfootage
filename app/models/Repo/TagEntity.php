<?php
/**
 * This is the repository for holding all of the entity tag relations.
 */
class Repo_TagEntity extends Repo_Abstract
{
    /**
     * The only available instance of Repo_TagEntity.
     *
     * @var Repo_TagEntity
     */
    protected static $_instance;

    /**
     * Returns an instance.
     *
     * Singleton pattern implementation.
     *
     * @return Repo_TagEntity
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
        $this->_dbTable = new Db_Table_TagEntity();
    }

    /**
     * Add a new entity tag relationship.
     *
     * @param string $entityType
     * @param integer $entityId
     * @param integer $tagId
     *
     * @return integer | string Id or error message.
     */
    public function addNew($entityType, $entityId, $tagId)
    {
        $dbTable = $this->getUpdateTable();

        // Check existence.
        $row = $this->getRow(
            array(
                array(
                    'where' => 'entity_type = ?',
                    'bind' => $entityType
                ),
                array(
                    'where' => 'entity_id = ?',
                    'bind' => $entityId
                ),
                array(
                    'where' => 'tag_id = ?',
                    'bind' => $tagId
                )
            )
        );
        if ($row) {
            return $row->id;
        }

        // Add
        $rowData = array(
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'tag_id' => $tagId
        );
        $newRow = $dbTable->createRow(
            $rowData
        );
        try {
            $newRow->save();
        } catch (Exception $e) {
            return 'Creating tag entity error: ' . $e->getMessage();
        }

        return $newRow->id;
    }

    /**
     * Get list of tags for an entity.
     *
     * @param string $entityType
     * @param integer $entityId
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function getEntityTags($entityType, $entityId)
    {
        $dbTable = $this->getFetchTable();
        $select = $dbTable->select()->setIntegrityCheck(false)
            ->from(
                array('te' => 'tag_entity'),
                array(
                    'tag_id' => 'te.tag_id',
                    'tag_name' => 't.name'
                )
            )
            ->joinLeft(
                array('t' => 'tag'),
                'te.tag_id = t.id',
                't.*'
            );
        $select->where('te.entity_type = ?', $entityType);
        $select->where('te.entity_id = ?', $entityId);
        return $dbTable->fetchAll($select);
    }

    /**
     * Get list of entities for a tag for a certain type.
     *
     * @param integer $tagId
     * @param string $entityType
     *
     * @return array
     */
    public function getTagEntities($tagId, $entityType)
    {
        if (!$tagId) {
            return array();
        }
        $dbTable = $this->getFetchTable();
        $select = $dbTable->select()->setIntegrityCheck(false)
            ->from(
                array('te' => 'tag_entity'),
                array(
                    'id' => 'te.entity_id'
                )
            );
        $select->where('te.entity_type = ?', $entityType);
        $select->where('te.tag_id = ?', $tagId);
        $entities = $dbTable->fetchAll($select);
        $entityIds = array();
        foreach ($entities as $_e) {
            $entityIds[] = $_e->id;
        }
        return $entityIds;
    }

    /**
     * Get list of entities for an array of tag ids for a certain type.
     *
     * @param array $tagIds
     * @param string $entityType
     *
     * @return array
     */
    public function getEntitiesForTags($tagIds, $entityType)
    {
        if (empty($tagIds)) {
            return array();
        }
        $dbTable = $this->getFetchTable();
        $select = $dbTable->select()->setIntegrityCheck(false)
            ->from(
                array('te' => 'tag_entity'),
                array(
                    'id' => 'te.entity_id'
                )
            );
        $select->where('te.entity_type = ?', $entityType);
        $tagIds = Functions_Common::arrayOfNumbers($tagIds);
        $select->where('te.tag_id IN (' . implode(',', $tagIds) . ')');
        $entities = $dbTable->fetchAll($select);
        $entityIds = array();
        foreach ($entities as $_e) {
            $entityIds[] = $_e->id;
        }
        return $entityIds;
    }

    /**
     * Remove entity tags.
     *
     * @param mixed $entityId
     * @param mixed $entityType
     * @return boolean
     */
    public function removeEntityTags($entityId, $entityType)
    {
        // Check existence.
        $rows = $this->getRows(
            array(
                array(
                    'where' => 'entity_type = ?',
                    'bind' => $entityType
                ),
                array(
                    'where' => 'entity_id = ?',
                    'bind' => $entityId
                )
            ),
            false, false, false, true
        );
        if ($rows && $rows->count()) {
            foreach ($rows as $_row) {
                $_row->delete();
            }
        }
        return true;
    }

    /**
     * Update entity tags.
     *
     * @param mixed $entityId
     * @param mixed $entityType
     * @param mixed $tagIds
     * @return boolean
     */
    public function updateEntityTags($entityId, $entityType, $tagIds)
    {
        // Remove all
        $this->removeEntityTags($entityId, $entityType);

        // Add one by one
        if (!is_array($tagIds)) {
            return false;
        }

        foreach ($tagIds as $_tagId) {
            $this->addNew($entityType, $entityId, $_tagId);
        }
        return true;
    }

    /**
     * Add a list of tags for an entity. Need to find tag id first.
     *
     * @param integer $clientId
     * @param string $tags
     * @param string $entityType
     * @param integer $entityId
     * @return boolean
     */
    public function addEntityTags($clientId, $tags, $entityType, $entityId)
    {
        if(!is_array($tags) || empty($tags) || !$clientId || !$entityType || !$entityId) {
            return false;
        }

        $this->removeEntityTags($entityId, $entityType);

        foreach ($tags as $_tag) {
            $_tagId = Repo_Tag::getInstance()->getTagId($clientId, $_tag);
            if ($_tagId && $_tagId > 0) {
                $this->addNew($entityType, $entityId, $_tagId);
            }
        }
        return true;
    }

    /**
     * Client entity tags.
     *
     * @param integer $clientId
     * @param string $entityType
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function getClientEntityTags($clientId, $entityType)
    {
        $dbTable = $this->getFetchTable();
        $select = $dbTable->select()->setIntegrityCheck(false)
            ->distinct(true)
            ->from(
                array('te' => 'tag_entity'),
                array(
                    'tag_id' => 'te.tag_id',
                    'tag_name' => 't.name'
                )
            )
            ->joinLeft(
                array('t' => 'tag'),
                'te.tag_id = t.id',
                array(
                    't.client_id'
                )

            );
        $select->where('te.entity_type = ?', $entityType);
        $select->where('t.client_id = ?', $clientId);
        return $dbTable->fetchAll($select);
    }
}