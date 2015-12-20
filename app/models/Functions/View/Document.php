<?php
/**
 * The central place to host all of the view helpers for document.
 */
class Functions_View_Document extends Functions_View
{
    /**
     * List portals.
     *
     * @param Zend_Db_Table_Rowset_Abstract $portals
     * @return string
     */
    public static function listDocument($docs)
    {
        $html = Functions_View::HTML_DEFAULT_EMPTY;
        if ($docs && $docs->count()) {
            $html .= '<table class="table table-striped table-bordered">';
            $html .= '<thead><tr><th class="text-center">Document</th><th class="text-center">Created</th>';
            $html .= '<th class="text-center">Last Modified</th>';
            $html .= '<th class="text-center">Description</th></tr></thead>';
            $html .= '<tbody>';
            foreach ($docs as $_doc) {
                $html .= '<tr rel="' . $_doc->id . '" class="noClickThrough">';
                $html .= '<td>' . $_doc->name . '</td>';
                $html .= '<td>' . Functions_Common::formattedDay($_doc->created_datetime, parent::STD_DATE_FORMAT) . '</td>';
                $html .= '<td>' . Functions_Common::formattedDay($_doc->modified_datetime, parent::STD_DATE_FORMAT) . '</td>';
                $html .= '<td>' . $_doc->description . '</td>';
                $html .= '</tr>';
            }
            $html .= '</tbody>';
            $html .= '</table>';
        }
        return $html;
    }
}