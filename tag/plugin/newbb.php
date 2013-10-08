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
 * @copyright       The XOOPS project http://sourceforge.net/projects/xoops/
 * @license         http://www.fsf.org/copyleft/gpl.html GNU public license
 * @since           1.0.0
 * @author          Taiwen Jiang <phppp@users.sourceforge.net>
 * @version         $Id: newbb.php 8164 2011-11-06 22:36:42Z beckmi $
 * @package         tag
 */
if (!defined('XOOPS_ROOT_PATH')) { exit(); }

/**
 * Get item fields:
 * title
 * content
 * time
 * link
 * uid
 * uname
 * tags
 *
 * @var        array    $items    associative array of items: [modid][catid][itemid]
 *
 * @return    boolean
 * 
 */
function newbb_tag_iteminfo(&$items)
{
    if (empty($items) || !is_array($items)) {
        return false;
    }
    
    $items_id = array();
    foreach (array_keys($items) as $cat_id) {
        // Some handling here to build the link upon catid
        // catid is not used in newbb, so just skip it
        foreach (array_keys($items[$cat_id]) as $item_id) {
            // In newbb, the item_id is "topic_id"
            $items_id[] = intval($item_id);
        }
    }
    $item_handler =& xoops_getmodulehandler('topic', 'newbb');
    $items_obj = $item_handler->getObjects(new Criteria("topic_id", "(" . implode(", ", $items_id) . ")", "IN"), true);
    
    foreach (array_keys($items) as $cat_id) {
        foreach (array_keys($items[$cat_id]) as $item_id) {
            $item_obj =& $items_obj[$item_id];
            $items[$cat_id][$item_id] = array(
                "title"     => $item_obj->getVar("topic_title"),
                "uid"       => $item_obj->getVar("topic_poster"),
                "link"      => "viewtopic.php?topic_id={$item_id}",
                "time"      => $item_obj->getVar("topic_time"),
                "tags"      => tag_parse_tag($item_obj->getVar("topic_tags", "n")),
                "content"   => "",
                );
        }
    }
    unset($items_obj);    
}

/**
 * Remove orphan tag-item links
 *
 * @return    boolean
 * 
 */
function newbb_tag_synchronization($mid)
{
    $item_handler =& xoops_getmodulehandler("topic", "newbb");
    $link_handler =& xoops_getmodulehandler("link", "tag");
        
    /* clear tag-item links */
    if (version_compare( mysql_get_server_info(), "4.1.0", "ge" )):
    $sql =  "    DELETE FROM {$link_handler->table}" .
            "    WHERE " .
            "        tag_modid = {$mid}" .
            "        AND " .
            "        ( tag_itemid NOT IN " .
            "            ( SELECT DISTINCT {$item_handler->keyName} " .
            "                FROM {$item_handler->table} " .
            "                WHERE {$item_handler->table}.approved > 0" .
            "            ) " .
            "        )";
    else:
    $sql =  "    DELETE {$link_handler->table} FROM {$link_handler->table}" .
            "    LEFT JOIN {$item_handler->table} AS aa ON {$link_handler->table}.tag_itemid = aa.{$item_handler->keyName} " .
            "    WHERE " .
            "        tag_modid = {$mid}" .
            "        AND " .
            "        ( aa.{$item_handler->keyName} IS NULL" .
            "            OR aa.approved < 1" .
            "        )";
    endif;
    if (!$result = $link_handler->db->queryF($sql)) {
        //xoops_error($link_handler->db->error());
    }
}
?>