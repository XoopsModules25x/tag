<?php
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/

/**
 * XOOPS tag management module
 *
 * @package        tag
 * @copyright       {@link http://sourceforge.net/projects/xoops/ The XOOPS Project}
 * @license         {@link http://www.fsf.org/copyleft/gpl.html GNU public license}
 * @since           1.00
 * @version         $Id: extgallery.php 12898 2014-12-08 22:05:21Z zyspec $
 */

defined('XOOPS_ROOT_PATH') || exit('Restricted access');
/**
 *
 * Generate tag item information
 *
 * @param array $items is an array containing category and item information
 *
 * @return null
 */
function extgallery_tag_iteminfo(&$items)
{
    if (empty($items) || !is_array($items)) {
        return false;
    }

    $items_id = array();
    foreach (array_keys($items) as $cat_id) {
        foreach (array_keys($items[$cat_id]) as $item_id) {
            $items_id[] = intval($item_id);
        }
    }

    $item_handler =& xoops_getmodulehandler('publicphoto', 'extgallery');
    $items_obj = $item_handler->getObjects(new Criteria("photo_id", "(" . implode(", ", $items_id) . ")", "IN"), true);

    foreach (array_keys($items) as $cat_id) {
        foreach (array_keys($items[$cat_id]) as $item_id) {
            if (isset($items_obj[$item_id])) {
                $item_obj =& $items_obj[$item_id];
                $items[$cat_id][$item_id] = array('title' => $item_obj->getVar("photo_title"),
                        'uid' => $item_obj->getVar("uid"),
                       'link' => "public-photo.php?photoId={$item_id}#photoNav",
                       'time' => $item_obj->getVar("photo_date"),
                       'tags' => '',
                    'content' => '',
                );
            }
        }
    }
    unset($items_obj);

    return true;
}

/**
 * Remove orphan tag-item links
 *
 * @param int $mid module id
 *
 * @return boolean
 *
 */
function extgallery_tag_synchronization($mid)
{
    $item_handler =& xoops_getmodulehandler('publicphoto', 'extgallery');
    $link_handler =& xoops_getmodulehandler("link", "tag");

    $mid = XoopsFilterInput::clean($mid, 'INT');

    /* clear tag-item links */
    /** {@internal the following statement isn't really needed any more (MySQL is really old)
     *   and some hosting companies block the mysql_get_server_info() function for security
     *   reasons.}
     */
//    if (version_compare( mysql_get_server_info(), "4.1.0", "ge" )):
    $sql = "DELETE FROM {$link_handler->table}"
         . " WHERE tag_modid = {$mid}"
         . " AND (tag_itemid NOT IN "
         . "       (SELECT DISTINCT {$item_handler->keyName} "
         . "        FROM {$item_handler->table} "
         . "          WHERE {$item_handler->table}.photo_approved > 0"
         . "       )"
         . "     )";
/*
    else:
    $sql =  "    DELETE {$link_handler->table} FROM {$link_handler->table}" .
            "    LEFT JOIN {$item_handler->table} AS aa ON {$link_handler->table}.tag_itemid = aa.{$item_handler->keyName} " .
            "    WHERE " .
            "        tag_modid = {$mid}" .
            "        AND " .
            "        ( aa.{$item_handler->keyName} IS NULL" .
            "            OR aa.photo_approved < 1" .
            "        )";
    endif;
*/
    if (!$result = $link_handler->db->queryF($sql)) {
        //xoops_error($link_handler->db->error());
    }

    return ($result) ? true : false;
}
