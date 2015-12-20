<?php
/**
 * The tag model.
 */
class Object_Tag extends Object_Abstract
{
    /**
     * Children array.
     *
     * @var array
     */
    public $children = array();

    /**
     * The construct function. Must providing the id.
     *
     * @param integer $id
     * @return Object_Tag
     */
    public function __construct($id)
    {
        $this->_repo = Repo_Tag::getInstance();
        $this->_dataRow = $this->_repo->findRow($id);
        if ($this->_dataRow) {
            $this->_data = $this->_dataRow->toArray();
            $this->_id = $id;
        }
    }

    /**
     * Get child tag tree.
     *
     * @return array
     */
    public function getChildTags()
    {
        return Repo_Tag::getInstance()->getChildTag($this->getId());
    }
}
