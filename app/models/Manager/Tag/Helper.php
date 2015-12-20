<?php
/**
 * Manager class for object tagging.
 */
class Manager_Tag_Helper extends Manager_Abstract
{
    private $_tagGroupCounter = 1;

    private $_tagTree;

    private $_entityTags;

    private $_clientId;

    private $_categoryName;

    /**
     * The only available instance of Manager_Tag_Helper.
     *
     * @var Manager_Tag_Helper
     */
    protected static $_instance;

    /**
     * Returns an instance.
     *
     * Singleton pattern implementation.
     *
     * @return Manager_Tag_Helper
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

    }

    /**
     * Filter to this client's tag tree
     *
     * @return void
     */
    public function initTagTree($tagTree, $clientId, $categoryName, $entityTags ) {

        $this->_tagTree = $tagTree;
        $this->_clientId = $clientId;
        $this->_categoryName = $categoryName;
        $this->_entityTags = $entityTags;

        //removes all other client tags
        for($h=0; $h < count($this->_tagTree); $h++) {
            $tag = $this->_tagTree[$h];
            $remove = false;
            if ($tag['client_id'] != $this->_clientId) {
                $remove = true;
            }
            //remove
            if($remove === true) array_splice($this->_tagTree,$h,1);
        }

        $this->setUpTableForCategoryTags();
        $this->setUpTableForDefaultTags();

    }

    /**
     * Looks for a tag with format "category:[resourcename] where resourcename is page,document,media-asset etc
     *
     * @return array or 0
     */
    private function getTagTreeResourceCategory( ) {
        for($h=0; $h < count($this->_tagTree); $h++) {
            $tag = $this->_tagTree[$h];
            if (strtolower(preg_replace('/\s+/', '', $tag['name'])) === "category:".$this->_categoryName ) {
                return  $tag;
            }
        }
        return 0;
    }

    private function setUpTableForCategoryTags( ) {

        //finds a tag called category:media-asset
        $categories = $this->getTagTreeResourceCategory( $this->_tagTree, $this->_clientId );

        if( $categories != 0 ) {
            $categoryTree = $categories['children'];
            //print the category head
            echo "<tr class='noClickThrough' style='border-top: 3px solid #ddd;'><td colspan='2' style='padding-top:20px;'><strong>PAGE SPECIFIC TAGS:</strong></td></tr>";
            for($r=0; $r < count($categoryTree); $r++) {
                //print the category head
                echo "<tr class='noClickThrough' style='border-top: 3px solid #ddd;'><td colspan='2' style='padding-top:20px;'><strong>" . $categoryTree[$r]['name'] . "</strong></td></tr>";
                //set a counter for level 2 tags
                $this->_tagGroupCounter = 0;
                $this->createCategoryTable($categoryTree[$r]['children'], $this->_clientId, 1, $this->pageTags );
            }
        }
        //prints an All Tags heading if was page-category tag list, and if there more non page-category tags
        if( $categories != 0 && count($this->_tagTree) != 0) {
            echo "<tr class='noClickThrough' style='border-top: 3px solid #ddd;'><td colspan='2' style='padding-top:20px;'><strong>ALL TAGS:</strong></td></tr>";
        }
    }

    private function setUpTableForDefaultTags() {
        //reset teg-level tracker
        $this->_tagGroupCounter = 0;
        $this->createDefaultTable( $this->_tagTree, $this->_clientId, 1, $this->_entityTags );
    }

    /**
     * Recursively generates a table of all the tags belonging to this client.
     *
     * @param array $tagTree (node of tags for looping through)
     * @param int $clientId
     * @param int $level
     * @param array $entityTags (the tags for this entity)
     * @return void
     */
    private function createDefaultTable( $tagTree, $clientId, $level, $entityTags ) {

        if($level==2) {
            //not implemented: this counter would be used to configure a collapsing table
            $this->_tagGroupCounter++;
        }

        $tagClass = "level".$level."tag";
        for($r=0; $r < count($tagTree); $r++) {
            $tag = $tagTree[$r];
            //don't print tags within category tags, just general tags
            if(strpos(strtolower(preg_replace('/\s+/', '', $tag['name'])),"category:") === FALSE ) {
                $parentId = ($level == 1) ? "" : $tag['parent_id'];
                $checkedStatus = ( in_array( $tag['id'], $entityTags) ) ? " checked" : "";
                echo "<tr class='noClickThrough'><td class='tagname ".$tagClass."' style='padding-left:".(10+($level-1)*20)."px;'>" . $tag['name'] . "</td>";
                echo "<td><div class='checkbox" . $checkedStatus . "' tag-level='".$level."' tag-id='".$tag['id']."' parent-id='".$parentId."' tag-name='" . $tag['name'] . "'></div>";
                //does it have children?
                if(sizeof($tag['children']) > 0 ) {
                    $this->createDefaultTable($tag['children'], $clientId, $level+1, $entityTags );
                }
                echo "</td></tr>";
            }
        }
    }

    /**
     * Recursively generates a table of all the tags specific to this asset type: document, page, media-asset etc.
     *
     * @param array $tagTree (node of tags for looping through)
     * @param int $clientId
     * @param int $level
     * @param array $entityTags (the tags for this entity)
     * @return void
     */
    private function createCategoryTable($tagTree, $clientId, $level, $entityTags ) {

        if($level==2) {
            //this counter would be used to configure a collapsing table
            $this->_tagGroupCounter++;
        }
        for($h=0; $h < count($tagTree); $h++) {
            $tag = $tagTree[$h];
            $parentId = ($level == 1) ? "" : $tag['parent_id'];

            $checkedStatus = ( in_array( $tag['id'], $entityTags) ) ? " checked" : "";
            //checks on right
            echo "<tr class='noClickThrough'><td class='tagname' style='padding-left:".(10+($level-1)*20)."px;'>" . $tag['name'] . "</td>";
            echo "<td><div class='checkbox" . $checkedStatus . "' tag-level='".$level."' tag-id='".$tag['id']."' parent-id='".$parentId."' tag-name='" . $tag['name'] . "'></div>";
            //does it have children?
            if(sizeof($tag['children']) > 0 ) {
                $this->createCategoryTable($tag['children'], $clientId, $level+1, $entityTags );
            }
            echo "</td></tr>";
        }
    }


    /**
     * Not implemented: list would be in place of a table, in order to be collapsing
     *
     * @return void
     */
    public function traceTagTreeAsList($tagTree, $level) {
        echo "<ul>";
        for($h=0; $h < count($tagTree); $h++) {
            $tag = $tagTree[$h];
            if($tag['client_id'] == $this->_clientId) {
                echo "<li class='tag-level-".$level." hide' tag-id='".$tag['id']."'>" . $tag['name'] ;
                //does it have children?
                if(sizeof($tag['children']) > 0 ) {
                    $this->traceTagTreeAsList($tag['children'], $level+1);
                }
                echo "</li>";
            }
        }
        echo "</ul>";
    }

    /**
     * Get tags belonging to an entity.
     *
     * @return array
     */
    public function getTagIds( $entityType, $entityId ) {
        $tags = Repo_TagEntity::getInstance()->getEntityTags($entityType, $entityId);
        $tagArray = array();
        if ($tags) {
            foreach ($tags as $_tag) {
                $tagArray[] = $_tag->tag_id;
            }
        }
        return $tagArray;
    }

}