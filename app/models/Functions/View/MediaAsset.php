<?php
/**
 * The central place to host all of the view helpers for media asset.
 */
class Functions_View_MediaAsset extends Functions_View
{
    /**
     * List.
     *
     * @param Zend_Db_Table_Rowset_Abstract $portals
     * @return string
     */
    public static function listAssets($assets)
    {
        $html = Functions_View::HTML_DEFAULT_EMPTY;
        if ($assets && $assets->count()) {
            $html .= '<table class="table table-striped table-bordered">';
            $html .= '<thead><tr class="off"><th class="text-center sort" data-sort="name">Document</th><th class="text-center sort" data-sort="created">Created</th>';
            $html .= '<th class="text-center sort" data-sort="modified">Last Modified</th>';
            $html .= '<th class="text-center sort" data-sort="desc">Description</th></tr></thead>';
            $html .= '<tbody>';
            foreach ($assets as $_asset) {
                $_a = new Object_MediaAsset($_asset->id);
                $html .= '<tr rel="' . $_asset->id . '" class="noClickThrough">';
                $html .= '<td class="selectMedia name" rel="' . $_a->getPublicLink() . '"><div>' . $_a->getPreviewHtml('300px') .  '</div>' . $_asset->name . '</td>';
                $html .= '<td class="created">' . Functions_Common::formattedDay($_asset->created_datetime, parent::STD_DATE_FORMAT) . '</td>';
                $html .= '<td class="modified">' . Functions_Common::formattedDay($_asset->modified_datetime, parent::STD_DATE_FORMAT) . '</td>';
                $html .= '<td class="desc">' . $_asset->description . '</td>';
                $html .= '</tr>';
            }
            $html .= '</tbody>';
            $html .= '</table>';
        }
        return $html;
    }
}