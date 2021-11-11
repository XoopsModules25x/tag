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
 * @package         \XoopsModuels\Tag
 * @copyright       {@link http://sourceforge.net/projects/xoops/ The XOOPS Project}
 * @license         {@link http://www.fsf.org/copyleft/gpl.html GNU public license}
 * @author          Taiwen Jiang <phppp@users.sourceforge.net>
 * @since           1.00
 */

use Xmf\Request;
use XoopsModules\Tag\Helper;
use XoopsModules\Tag\Utility;

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

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
 * @param array $items associative array of items: [modid][catid][itemid]
 *
 * @return bool
 */
function newbb_tag_iteminfo(&$items)
{
    if (empty($items) || !is_array($items)) {
        return false;
    }

    $items_id = [];
    foreach (array_keys($items) as $cat_id) {
        // Some handling here to build the link upon catid
        // catid is not used in newbb, so just skip it
        foreach (array_keys($items[$cat_id]) as $item_id) {
            // In newbb, the item_id is "topic_id"
            $items_id[] = (int)$item_id;
        }
    }
    $helper = \XoopsModules\Newbb\Helper::getInstance();
    /** @var \XoopsModules\Newbb\TopicHandler $itemHandler */
    $itemHandler = $helper->getHandler('Topic');
    $items_obj   = $itemHandler->getObjects(new \Criteria('topic_id', '(' . implode(', ', $items_id) . ')', 'IN'), true);

    foreach (array_keys($items) as $cat_id) {
        foreach (array_keys($items[$cat_id]) as $item_id) {
            $item_obj                 = $items_obj[$item_id];
            $items[$cat_id][$item_id] = [
                'title'   => $item_obj->getVar('topic_title'),
                'uid'     => $item_obj->getVar('topic_poster'),
                'link'    => "viewtopic.php?topic_id={$item_id}",
                'time'    => $item_obj->getVar('topic_time'),
                'tags'    => Utility::tag_parse_tag($item_obj->getVar('topic_tags', 'n')),
                'content' => '',
            ];
        }
    }
    unset($items_obj);

    return true;
}

/**
 * Remove orphan tag-item links
 *
 * @param int $mid module id
 * @return bool
 */
function newbb_tag_synchronization($mid)
{
    /** @var \XoopsModules\Newbb\TopicHandler $itemHandler */
    $itemHandler = \XoopsModules\Newbb\Helper::getInstance()->getHandler('Topic');
    /** @var \XoopsModules\Tag\LinkHandler $linkHandler */
    $linkHandler = Helper::getInstance()->getHandler('Link'); //@var \XoopsModules\Tag\Handler $tagHandler

    //    $mid = XoopsFilterInput::clean($mid, 'INT');
    $mid = Request::getInt('mid');

    /* clear tag-item links */ 
    /** {@internal the following statement isn't really needed any more (MySQL is really old)
 *   and some hosting companies block the $GLOBALS['xoopsDB']->getServerVersion() function for security
 *   reasons. }
 */
    //    if (version_compare( $GLOBALS['xoopsDB']->getServerVersion(), "4.1.0", "ge" )):
    $sql = "DELETE FROM {$linkHandler->table}" . " WHERE tag_modid = {$mid}" . '   AND (tag_itemid NOT IN ' . "         (SELECT DISTINCT {$itemHandler->keyName} " . "          FROM {$itemHandler->table} " . "          WHERE {$itemHandler->table}.approved > 0" . '          )' . '       )';
    /*
        else:
        $sql =  "    DELETE {$linkHandler->table} FROM {$linkHandler->table}" .
                "    LEFT JOIN {$itemHandler->table} AS aa ON {$linkHandler->table}.tag_itemid = aa.{$itemHandler->keyName} " .
                "    WHERE " .
                "        tag_modid = {$mid}" .
                "        AND " .
                "        ( aa.{$itemHandler->keyName} IS NULL" .
                "            OR aa.approved < 1" .
                "        )";
        endif;
    */
    if (!$result = $linkHandler->db->queryF($sql)) {
        //xoops_error($linkHandler->db->error());
    }

    return $result ? true : false;
}
