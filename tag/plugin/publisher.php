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
 * @copyright       The XUUPS Project http://sourceforge.net/projects/xuups/
 * @license         http://www.fsf.org/copyleft/gpl.html GNU public license
 * @package         tag
 * @subpackage      plugin
 * @since           1.0
 * @author          trabis <lusopoemas@gmail.com>
 * @author          The SmartFactory <www.smartfactory.ca>
 * @version         $Id: publisher.php 12898 2014-12-08 22:05:21Z zyspec $
 */

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

/**
 * Get item fields: title, content, time, link, uid, uname, tags
 *
 */
function publisher_tag_iteminfo(&$items)
{
    if (empty($items) || !is_array($items)) {
        return false;
    }

    $items_id = array();

    foreach (array_keys($items) as $cat_id) {
        // Some handling here to build the link upon catid
        foreach (array_keys($items[$cat_id]) as $item_id) {
            // In publisher, the item_id is "itemid"
            $items_id[] = intval($item_id);
        }
    }
    $item_handler = xoops_getmodulehandler("item", "publisher");
    $criteria = new Criteria("itemid", "(" . implode(", ", $items_id) . ")", "IN");
    $items_obj = $item_handler->getObjects($criteria, 'itemid');

    foreach (array_keys($items) as $cat_id) {
        foreach (array_keys($items[$cat_id]) as $item_id) {
            $item_obj = $items_obj[$item_id];
            $items[$cat_id][$item_id] = array("title" => $item_obj->getVar("title"),
                                                "uid" => $item_obj->getVar("uid"),
                                               "link" => "item.php?itemid={$item_id}",
                                               "time" => $item_obj->getVar("datesub"),
                                               "tags" => tag_parse_tag($item_obj->getVar("item_tag", "n")), // optional
                                            "content" => "",
            );
        }
    }
    unset($items_obj);

    return true;
}

/** Remove orphan tag-item links **/
function publisher_tag_synchronization($mid)
{
    include_once $GLOBALS['xoops']->path('/modules/publisher/include/constants.php');
    $item_handler =& xoops_getmodulehandler("item", "publisher");
    $link_handler =& xoops_getmodulehandler("link", "tag");

    $mid = XoopsFilterInput::clean($mid, 'INT');

    /* clear tag-item links */
    $sql =  "    DELETE FROM {$link_handler->table}" .
            "    WHERE " .
            "        tag_modid = {$mid}" .
            "        AND " .
            "        ( tag_itemid NOT IN " .
            "            ( SELECT DISTINCT {$item_handler->keyName} " .
            "                FROM {$item_handler->table} " .
            "                WHERE {$item_handler->table}.status = " . _PUBLISHER_STATUS_PUBLISHED .
            "            ) " .
            "        )";
    $result = $link_handler->db->queryF($sql);

    return ($result) ? true : false;
}
