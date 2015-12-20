<?php
/**
 * The central place to host all of the view helpers for resource access.
 */
class Functions_View_ResourceAccess extends Functions_View
{
    /**
     * List access client tree.
     *
     * @param array $clients
     * @param Zend_Db_Table_Rowset_Abstract $accessClients
     * @return string
     */
    public static function listClientsRecursively($clients, $accessClients)
    {
        $html = '';
        if (!$clients || !count($clients)) {
            return $html;
        }
        $html .= '<ul class="accessTree">';
        foreach ($clients as $_client) {
            if (Manager_Resource_Access::getInstance()->clientInAccess($_client['id'], $accessClients->toArray())) {
                $_checked = true;
            } else {
                $_checked = false;
            }
            $_clientHtml = '<li rel="' . $_client['id'] . '">';
            $_clientHtml .= '<input type="checkbox"' . ($_checked ? ' checked="checked"' : '') . ' />';
            $_clientHtml .= $_client['name'];
            if (isset($_client['children']) && count($_client['children'])) {
                $_clientHtml .= self::listClientsRecursively($_client['children'], $accessClients);
            }
            $_clientHtml .= '</li>';
            $html .= $_clientHtml;
        }
        $html .= '</ul>';
        return $html;
    }
}