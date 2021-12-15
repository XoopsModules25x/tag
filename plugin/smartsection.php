<?php declare(strict_types=1);

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
 * @copyright       XOOPS Project (https://xoops.org)
 * @license         https://www.fsf.org/copyleft/gpl.html GNU public license
 * @since           1.0.0
 * @author          Taiwen Jiang <phppp@users.sourceforge.net>
 * @version         $Id: article.php 2292 2008-10-12 04:53:18Z phppp $
 */

use XoopsModules\Tag\Helper;
use XoopsModules\Tag\Utility;

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

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
 *
 * @param array $items associative array of items: [modid][catid][itemid]
 */
function smartsection_tag_iteminfo(array &$items): bool
{
    if (empty($items)) {
        return false;
    }

    $items_id = [];
    foreach (array_keys($items) as $cat_id) {
        // Some handling here to build the link upon catid
        // catid is not used in article, so just skip it
        foreach (array_keys($items[$cat_id]) as $item_id) {
            // In article, the item_id is "art_id"
            $items_id[] = (int)$item_id;
        }
    }

    /** @var \XoopsModules\Smartsection\ItemHandler $itemHandler */
    $itemHandler = \XoopsModules\Smartsection\Helper::getInstance()->getHandler('Item');

    $items_obj = $itemHandler->getObjects(new Criteria('itemid', '(' . implode(', ', $items_id) . ')', 'IN'), true);

    foreach (array_keys($items) as $cat_id) {
        foreach (array_keys($items[$cat_id]) as $item_id) {
            $item_obj                 = &$items_obj[$item_id];
            $items[$cat_id][$item_id] = [
                'title'   => $item_obj->getVar('title'),
                'uid'     => $item_obj->getVar('uid'),
                'link'    => "item.php?itemid={$item_id}",
                'time'    => $item_obj->getVar('datesub'),
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
 */
function article_tag_synchronization(int $mid): void
{
    /** @var \XoopsModules\Smartsection\ItemHandler $itemHandler */
    $itemHandler = \XoopsModules\Smartsection\Helper::getInstance()->getHandler('Item');

    /** @var \XoopsModules\Tag\LinkHandler $itemHandler */
    $linkHandler = Helper::getInstance()->getHandler('Link');

    /* clear tag-item links */
    if (version_compare($GLOBALS['xoopsDB']->getServerVersion(), '4.1.0', 'ge')) :
        $sql = "    DELETE FROM {$linkHandler->table}"
               . '    WHERE '
               . "        tag_modid = {$mid}"
               . '        AND '
               . '        ( tag_itemid NOT IN '
               . "            ( SELECT DISTINCT {$itemHandler->keyName} "
               . "                FROM {$itemHandler->table} "
               . "                WHERE {$itemHandler->table}.art_time_publish > 0"
               . '            ) '
               . '        )';
    else :
        $sql = "    DELETE {$linkHandler->table} FROM {$linkHandler->table}"
               . "    LEFT JOIN {$itemHandler->table} AS aa ON {$linkHandler->table}.tag_itemid = aa.{$itemHandler->keyName} "
               . '    WHERE '
               . "        tag_modid = {$mid}"
               . '        AND '
               . "        ( aa.{$itemHandler->keyName} IS NULL"
               . '            OR aa.art_time_publish < 1'
               . '        )';
    endif;
    if (!$result = $linkHandler->db->queryF($sql)) {
        //xoops_error($linkHandler->db->error());
    }
}
