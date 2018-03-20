<?php
/**
 * TDMDownload
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright   Gregory Mage (Aka Mage)
 * @license     GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @author      Gregory Mage (Aka Mage)
 * @param $items
 * @return bool
 */

function tdmdownloads_tag_iteminfo($items)
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

    $itemHandler = xoops_getModuleHandler('tdmdownloads_downloads', 'tdmdownloads');
    $items_obj   = $itemHandler->getObjects(new \Criteria('lid', '(' . implode(', ', $items_id) . ')', 'IN'), true);

    foreach (array_keys($items) as $cat_id) {
        foreach (array_keys($items[$cat_id]) as $item_id) {
            if (isset($items_obj[$item_id])) {
                $item_obj = $items_obj[$item_id];
                $items[$cat_id][$item_id] = [
                    'title' => $item_obj->getVar('title'),
                    'uid' => $item_obj->getVar('submitter'),
                    'link' => "singlefile.php?cid={$item_obj->getVar('cid')}&lid={$item_id}",
                    'time' => $item_obj->getVar('date'),
                    'tags' => '',
                    'content' => '',
                    ];
            }
        }
    }
    unset($items_obj);
    return '';
}

/**
 * @param $mid
 */
function tdmdownloads_tag_synchronization($mid)
{
    $itemHandler = xoops_getModuleHandler('tdmdownloads_downloads', 'tdmdownloads');
    /** @var \TagLinkHandler $linkHandler */
    $linkHandler = \XoopsModules\Tag\Helper::getInstance()->getHandler('Link'); //@var \XoopsModules\Tag\Handler $tagHandler

    /* clear tag-item links */
    if (version_compare($GLOBALS['xoopsDB']->getServerVersion(), '4.1.0', 'ge')):
    $sql =  "    DELETE FROM {$linkHandler->table}" . '    WHERE ' .
            "        tag_modid = {$mid}" . '        AND ' . '        ( tag_itemid NOT IN ' .
            "            ( SELECT DISTINCT {$itemHandler->keyName} " .
            "                FROM {$itemHandler->table} " .
            "                WHERE {$itemHandler->table}.status > 0" . '            ) ' . '        )'; else:
    $sql =  "    DELETE {$linkHandler->table} FROM {$linkHandler->table}" .
            "    LEFT JOIN {$itemHandler->table} AS aa ON {$linkHandler->table}.tag_itemid = aa.{$itemHandler->keyName} " . '    WHERE ' .
            "        tag_modid = {$mid}" . '        AND ' .
            "        ( aa.{$itemHandler->keyName} IS NULL" . '            OR aa.status < 1' . '        )';
    endif;
    if (!$result = $linkHandler->db->queryF($sql)) {
        //xoops_error($linkHandler->db->error());
    }
}
