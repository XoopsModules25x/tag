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
 * @param $items
 * @return bool
 * @author      Gregory Mage (Aka Mage)
 * @copyright   Gregory Mage (Aka Mage)
 * @license     GNU GPL 2 (https://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 */

use XoopsModules\Tag\Helper;

/**
 * Get item fields: title, content, time, link, uid, tags
 * @param array $items
 */
function tdmdownloads_tag_iteminfo(array &$items): bool
{
    if (empty($items) || !is_array($items)) {
        return false;
    }

    $items_id = [];
    foreach (array_keys($items) as $cat_id) {
        foreach (array_keys($items[$cat_id]) as $item_id) {
            $items_id[] = (int)$item_id;
        }
    }

    //    $itemHandler = $helper->getHandler('Tdmdownloads_downloads', 'tdmdownloads');

    /** @var \XoopsModules\Tdmdownloads\DownloadsHandler $itemHandler */
    $itemHandler = \XoopsModules\Tdmdownloads\Helper::getInstance()->getHandler('Downloads');

    $items_obj = $itemHandler->getObjects(new \Criteria('lid', '(' . implode(', ', $items_id) . ')', 'IN'), true);

    foreach (array_keys($items) as $cat_id) {
        foreach (array_keys($items[$cat_id]) as $item_id) {
            if (isset($items_obj[$item_id])) {
                $item_obj                 = $items_obj[$item_id];
                $items[$cat_id][$item_id] = [
                    'title'   => $item_obj->getVar('title'),
                    'uid'     => $item_obj->getVar('submitter'),
                    'link'    => "singlefile.php?cid={$item_obj->getVar('cid')}&lid={$item_id}",
                    'time'    => $item_obj->getVar('date'),
                    'tags'    => '',
                    'content' => '',
                ];
            }
        }
    }
    unset($items_obj);

    return true;
}

/**
 * @param $mid
 */
function tdmdownloads_tag_synchronization($mid): void
{
    //    $itemHandler = $helper->getHandler('Downloads', 'tdmdownloads');

    /** @var \XoopsModules\Tdmdownloads\DownloadsHandler $itemHandler */
    $itemHandler = \XoopsModules\Tdmdownloads\Helper::getInstance()->getHandler('Downloads');

    /** @var \XoopsModules\Tag\LinkHandler $linkHandler */
    $linkHandler = Helper::getInstance()->getHandler('Link'); //@var \XoopsModules\Tag\Handler $tagHandler

    /* clear tag-item links */
    if (version_compare($GLOBALS['xoopsDB']->getServerVersion(), '4.1.0', 'ge')) :
        $sql = "    DELETE FROM {$linkHandler->table}"
               . '    WHERE '
               . "        tag_modid = {$mid}"
               . '        AND '
               . '        ( tag_itemid NOT IN '
               . "            ( SELECT DISTINCT {$itemHandler->keyName} "
               . "                FROM {$itemHandler->table} "
               . "                WHERE {$itemHandler->table}.status > 0"
               . '            ) '
               . '        )';
    else :
        $sql = "    DELETE {$linkHandler->table} FROM {$linkHandler->table}"
               . "    LEFT JOIN {$itemHandler->table} AS aa ON {$linkHandler->table}.tag_itemid = aa.{$itemHandler->keyName} "
               . '    WHERE '
               . "        tag_modid = {$mid}"
               . '        AND '
               . "        ( aa.{$itemHandler->keyName} IS NULL"
               . '            OR aa.status < 1'
               . '        )';
    endif;
    if (!$result = $linkHandler->db->queryF($sql)) {
        //xoops_error($linkHandler->db->error());
    }
}
