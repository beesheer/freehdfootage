<?php
/**
 * The central place to host all of the view helpers for document.
 */
class Functions_View_ResourceLibrary extends Functions_View
{
    /**
     * List portals.
     *
     * @param Zend_Db_Table_Rowset_Abstract $portals
     * @return string
     */
    public static function listResourceLibrary($docs)
    {
        $html = Functions_View::HTML_DEFAULT_EMPTY;

        if($docs->count()==0)
        {
            $html .= '<table class="table table-striped table-bordered">';
            $html .= '<thead>
                        <tr><th class="text-center">Sorry, No record(s) found!</th>
                     </thead>';
            $html .= '<tbody>';
            return $html;
            exit;
        }
        
        if ($docs && $docs->count()) {
            $html .= '<table class="table table-striped table-bordered">';
            $html .= '<thead>
                        <tr >
                            <th class="text-center"><div onclick="event.stopPropagation()"><a href="javascript:void(0)" onclick="setSortOrder(\'name\')">File Name</a></div></th>
                            <th class="text-center"><div onclick="event.stopPropagation()">Kind</div></th>
                            <th class="text-center"><div onclick="event.stopPropagation()"><a href="javascript:void(0)" onclick="setSortOrder(\'created_datetime\')">Date Added</a></div></th>
                            <th class="text-center" style="width: 320px;"><div onclick="event.stopPropagation()">Tags</div></th>
                            <th class="text-center"><div onclick="event.stopPropagation()">Download Link</div></th>
                        </tr>
                        </thead>';
            $html .= '<tbody>';
            foreach ($docs as $_doc) {

                $current_doc_boj = new Object_Document($_doc->id);

                //getting docuemnt tags
                $doc_tags ="";
                $doc_tags = Repo_TagEntity::getInstance()->getEntityTags("document_asset",$_doc->id);
                $tagArray= array();
                foreach ($doc_tags as $tag)
                {
                    $tagArray[] = (isset($tag->tag_name)?$tag->tag_name:"");
                }
                $tags_str=@implode(", ", $tagArray);

                $limit=100;
                if (strlen($tags_str) > $limit)
                $tags_str = substr($tags_str, 0, strrpos(substr($tags_str, 0, $limit), ' ')) . '...';
                //echo $tags_str;
                
                $html .= '<tr rel="' . $_doc->id . '" class="noClickThrough">';
                $html .= '<td>' . $_doc->name . '</td>';
                $html .= '<td>' . strtoupper(pathinfo($_doc->file_path,PATHINFO_EXTENSION)) . '</td>';
                $html .= '<td>' . Functions_Common::formattedDay($_doc->created_datetime, parent::STD_DATE_FORMAT) . '</td>';
                $html .= '<td>' . $tags_str . '</td>';
                $html .= '<td><a class="btn btn-success" id="download" onclick="event.stopPropagation()" href="'.$current_doc_boj->getDownloadLink().'" target="_blank">Download</a></td>';
                $html .= '</tr>';
            }
            $html .= '</tbody>';
            $html .= '</table>';
        }
        return $html;
    }
}