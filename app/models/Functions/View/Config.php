<?php
/**
 * The central place to host all of the view helpers.
 */
class Functions_View_Config extends Functions_View
{
    const HTML_DEFAULT_EMPTY = '';
    const HTML_NOT_AVAILABLE = 'not-available';

    /**
     * List roles
     *
     * @param Zend_Db_Table_Rowset_Abstract $roles
     * @return string
     */
    public static function listRoles($roles)
    {
        $html = self::HTML_DEFAULT_EMPTY;
        if ($roles && $roles->count()) {
            $html .= '<table class="table table-striped table-bordered table-condensed">';
            $html .= '<thead><tr><th class="text-center">Name</th><th></th></tr></thead>';
            $html .= '<tbody>';
            foreach ($roles as $_role) {
                if ($_role->id == 1) {
                    continue;
                }
                $html .= '<tr rel="' . $_role->id . '" class="activateRole noClickThrough">';
                $html .= '<td class="role-name">' . $_role->name . '</td>';
                $html .= '<td><div class="pull-right"><button type="button" class="btn btn-primary edit-role" rel="' . $_role->id . '" details=\'' . Zend_Json::encode($_role->toArray()) . '\'>Details</button>';
                $html .= '</tr>';
            }
            $html .= '</tbody>';
            $html .= '</table>';
        }
        return $html;
    }

    /**
     * List permissions
     *
     * @param Zend_Db_Table_Rowset_Abstract $permissions
     * @return string
     */
    public static function listPermissions($permissions)
    {
        $html = self::HTML_DEFAULT_EMPTY;
        if ($permissions && $permissions->count()) {
            $html .= '<table class="table table-striped table-bordered table-condensed">';
            $html .= '<thead><tr><th></th><th class="text-center">Name</th><th></th></tr></thead>';
            $html .= '<tbody>';
            foreach ($permissions as $_p) {
                $html .= '<tr rel="' . $_p->id . '" class="noClickThrough">';
                $html .= '<td class="role-permission"><input type="checkbox" value="' . $_p->id . '" disabled /></td>';
                $html .= '<td class="permission-name">' . $_p->name . '</td>';
                $html .= '<td><div class="pull-right"><button type="button" class="btn btn-primary edit-permission" rel="' . $_p->id . '" details=\'' . Zend_Json::encode($_p->toArray()) . '\'>Details</button>';
                $html .= '</tr>';
            }
            $html .= '</tbody>';
            $html .= '</table>';
        }
        return $html;
    }

    /**
     * List sql files.
     *
     * @param mixed $files
     * @return string
     */
    public static function listSqlFiles($files)
    {
        $html = self::HTML_DEFAULT_EMPTY;
        if ($files && count($files)) {
            $html .= '<table class="table table-striped table-bordered table-condensed">';
            $html .= '<thead><tr><th class="text-center">Name</th><th></th><th></th></tr></thead>';
            $html .= '<tbody>';
            foreach ($files as $_f) {
                $html .= '<tr class="noClickThrough">';
                $html .= '<td>' . $_f . '</td>';
                $html .= '<td><a href="/admin/index/run-sql?updateSql=' . $_f . '">Install</td>';
                $html .= '<td><a href="/admin/index/view-sql?updateSql=' . $_f . '">View</td>';
                $html .= '</tr>';
            }
            $html .= '</tbody>';
            $html .= '</table>';
        }
        return $html;
    }
}