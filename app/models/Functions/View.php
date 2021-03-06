<?php

/**
 * The central place to host all of the view helpers.
 */
class Functions_View {

    const STD_DATE_FORMAT = 'F j, Y h:i a';
    const SHORT_DATE = 'M j, Y H:i';
    const HTML_DEFAULT_EMPTY = '';
    const HTML_NOT_AVAILABLE = 'not-available';

    /**
     * List Videos
     *
     * @param Zend_Db_Table_Rowset_Abstract $videos
     * @return string
     */
    public static function listVideos($videos)
    {
        $html = '';
        foreach ($videos as $_v) {
            $html .= '<div class="col-sm-6 col-md-4">
            <div class="thumbnail">
              <iframe width="100%" height="60%" src="https://www.youtube.com/embed/' . $_v->youtube_id . '" frameborder="0" allowfullscreen></iframe>
              <div class="caption">
                <h3><a href="http://www.youtube.com/watch?v=' . $_v->youtube_id . '" target="_blank">' . $_v->name . '</a></h3>
                <p>' . $_v->description . '</p>
              </div>
            </div>
          </div>';
        }
        return $html;
    }

    /**
     * List Videos
     *
     * @param Zend_Db_Table_Rowset_Abstract $videos
     * @return string
     */
    public static function listQueries($rows)
    {
        $html = '';
        foreach ($rows as $_r) {
            $html .= '<div class="col-sm-1 col-md-1">
            <h4><a href="/?q=' . $_r->q . '">' . $_r->q . '</a></h4>
          </div>';
        }
        return $html;
    }
}
