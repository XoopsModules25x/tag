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
 * @copyright      {@link http://sourceforge.net/projects/xoops/ The XOOPS Project}
 * @license        {@link http://www.fsf.org/copyleft/gpl.html GNU public license}
 * @author         Taiwen Jiang <phppp@users.sourceforge.net>
 * @author         ZySpec <owners@zyspec.com>
 * @since          2.33
 * @version        $Id: $
 */

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

XoopsLoad::load('XoopsFilterInput');

/** Get item fields: title, content, time, link, uid, tags
 *
 * Note that $items is "by reference" so modifying it in this
 * routine in effect passes it back...
 *
 * @param array $items
 *
 * @return bool always returns true
 **/
function randomquote_tag_iteminfo(&$items)
{
    xoops_load('constants', 'randomquote');

    $items_id = array();
    $cats_id = array();

    foreach (array_keys($items) as $cat_id) {
        $cats_id[] = intval($cat_id);
        foreach (array_keys($items[$cat_id]) as $item_id) {
            $items_id[] = intval($item_id);
        }
    }

    $criteria = new CriteriaCompo();
    $criteria->add(new Criteria("id", "(" . implode(",", $items_id) . ")", "IN"));
    $criteria->add(new Criteria('quote_status', RandomquoteConstants::STATUS_ONLINE));

    $quote_handler =& xoops_getmodulehandler('quotes', 'randomquote');
    $quoteObjs = $quote_handler->getObjects($criteria, true);

    foreach ($cats_id as $cat_id) {
        foreach ($items_id as $item_id) {
            $quoteObj = $quoteObjs[$item_id];
            $items[$cat_id][$item_id] = array("title" => $quoteObj,
//                                                "uid" => $quoteObj->getVar("uid"),
                                               "link" => "index.php?id={$item_id}",
                                               "time" => strtotime($quoteObj->getVar("create_date")),
//                                               "tags" => tag_parse_tag($quoteObj->getVar("item_tag", "n")), // optional
                                            "content" => "",
            );
        }
    }

    unset($items_obj);

    return true;
}

/**
 * Remove orphan tag-item links
 *
 *  @param int $mid module ID
 */
function mymodule_tag_synchronization($mid)
{
    xoops_load('constants', 'randomquote');
    $item_handler =& xoops_getmodulehandler('quotes', 'randomquote');
    $link_handler =& xoops_getmodulehandler('link', 'tag');

    if (!$item_handler || !$link_handler) {
        $result = false;
    } else {
        $mid = XoopsFilterInput::clean($mid, 'INT');
        $module_handler =& xoops_gethandler('module');
        $rqModule       =& XoopsModule::getByDirname('randomquote');

        // check to make sure module is active and trying to sync randomquote
        if (($rqModule instanceof XoopsModule) && ($rqModule->isactive()) && ($rqModule->mid() == $mid)) {
            // clear tag-item links
            $sql = "DELETE FROM {$link_handler->table}"
                 . " WHERE tag_modid = {$mid}"
                 . "    AND "
                 . "    (tag_itemid NOT IN "
                 . "        (SELECT DISTINCT {$item_handler->keyName} "
                 . "           FROM {$item_handler->table} "
                 . "           WHERE {$item_handler->table}.quote_status = " . RandomquoteConstants::STATUS_ONLINE
                 . "        )"
                 . "    )";
            $result = $link_handler->db->queryF($sql);
        } else {
            $result = false;
        }
    }

    return ($result) ? true : false;
}
