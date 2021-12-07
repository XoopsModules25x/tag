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
 * @copyright      {@link https://sourceforge.net/projects/xoops/ The XOOPS Project}
 * @license        {@link https://www.fsf.org/copyleft/gpl.html GNU public license}
 * @author         Taiwen Jiang <phppp@users.sourceforge.net>
 * @author         ZySpec <zyspec@yahoo.com>
 * @since          2.33
 */

use Xmf\Request;
use XoopsModules\Randomquote\Constants;
use XoopsModules\Tag\Helper;

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
function randomquote_tag_iteminfo(array &$items): bool
{
    $items_id = [];
    $cats_id  = [];
    /*
    $cats_id = array_map('intval', array_keys($items));
    foreach ($cats_id as $cat_id => $itemArray) {
        $items_id = array_merge($items_id, $itemArray);
    }
    $items_id = array_map('intval', $items_id);
    $items_id = array_unique($items_id); // remove duplicate item_ids if they exist
    */
    foreach (array_keys($items) as $cat_id) {
        $cats_id[] = (int)$cat_id;
        foreach (array_keys($items[$cat_id]) as $item_id) {
            $items_id[] = (int)$item_id;
        }
    }

    $criteria = new \CriteriaCompo();
    $criteria->add(new \Criteria('id', '(' . implode(',', $items_id) . ')', 'IN'));
    $criteria->add(new \Criteria('quote_status', Constants::STATUS_ONLINE));

    /** @var \XoopsModules\Randomquote\QuotesHandler $itemHandler */
    $itemHandler = \XoopsModules\Randomquote\Helper::getInstance()->getHandler('Quotes');

    $quoteObjs = $itemHandler->getObjects($criteria, true);

    foreach ($cats_id as $cat_id) {
        foreach ($items_id as $item_id) {
            $quoteObj                 = $quoteObjs[$item_id];
            $items[$cat_id][$item_id] = [
                'title'   => $quoteObj,
                //'uid' => $quoteObj->getVar("uid"),
                'link'    => "index.php?id={$item_id}",
                'time'    => strtotime($quoteObj->getVar('create_date')),
                //'tags' => \XoopsModules\Tag\Utility::tag_parse_tag($quoteObj->getVar("item_tag", "n")), // optional
                'content' => '',
            ];
        }
    }
    unset($quoteObjs);

    return true;
}

/**
 * Remove orphan tag-item links
 *
 * @param int $mid module ID
 */
function randomquote_tag_synchronization(int $mid): bool
{
    /** @var \XoopsModules\Randomquote\QuotesHandler $itemHandler */
    $itemHandler = \XoopsModules\Randomquote\Helper::getInstance()->getHandler('Quotes');
    /** @var \XoopsModules\Tag\LinkHandler $itemHandler */
    $linkHandler = Helper::getInstance()->getHandler('Link');

    $result = false;
    if ($itemHandler && $linkHandler) {
        $mid = Request::getInt('mid', 0);
        /** @var \XoopsModuleHandler $moduleHandler */
        $moduleHandler = xoops_getHandler('module');
        $rqModule      = XoopsModule::getByDirname('randomquote');

        // check to make sure module is active and trying to sync randomquote
        if (($rqModule instanceof \XoopsModule) && $rqModule->isactive() && ($rqModule->mid() == $mid)) {
            // clear tag-item links
            $sql    = "DELETE FROM {$linkHandler->table}" . " WHERE tag_modid = {$mid}" . ' AND (tag_itemid NOT IN' . " (SELECT DISTINCT {$itemHandler->keyName}" . " FROM {$itemHandler->table} WHERE {$itemHandler->table}.quote_status = " . Constants::STATUS_ONLINE . '))';
            $result = $linkHandler->db->queryF($sql);
        }
    }

    return (bool)$result;
}
