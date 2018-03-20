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
 */

use XoopsModules\Randomquote\Constants;

defined('XOOPS_ROOT_PATH') || die('Restricted access');

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
    $items_id = [];
    $cats_id  = [];

    foreach (array_keys($items) as $cat_id) {
        $cats_id[] = (int)$cat_id;
        foreach (array_keys($items[$cat_id]) as $item_id) {
            $items_id[] = (int)$item_id;
        }
    }

    $criteria = new \CriteriaCompo();
    $criteria->add(new \Criteria('id', '(' . implode(',', $items_id) . ')', 'IN'));
    $criteria->add(new \Criteria('quote_status', Constants::STATUS_ONLINE));

    /** @var \ \XoopsModules\Randomquote\Helper $quoteHandler */
    $quoteHandler = \XoopsModules\Randomquote\Helper::getInstance()->getHandler('Quotes');

    $quoteObjs    =& $quoteHandler->getObjects($criteria, true);

    foreach ($cats_id as $cat_id) {
        foreach ($items_id as $item_id) {
            $quoteObj                 = $quoteObjs[$item_id];
            $items[$cat_id][$item_id] = [
                'title'   => $quoteObj,
                //                                                "uid" => $quoteObj->getVar("uid"),
                'link'    => "index.php?id={$item_id}",
                'time'    => strtotime($quoteObj->getVar('create_date')),
                //                                               "tags" => tag_parse_tag($quoteObj->getVar("item_tag", "n")), // optional
                'content' => ''
            ];
        }
    }

    unset($items_obj);

    return true;
}

/**
 * Remove orphan tag-item links
 *
 * @param  int $mid module ID
 * @return bool
 */
function randomquote_tag_synchronization($mid)
{
    /** @var \ \XoopsModules\Randomquote\Helper $itemHandler */
    $itemHandler = \XoopsModules\Randomquote\Helper::getInstance()->getHandler('Quotes');
    /** @var \XoopsModules\Tag\LinkHandler $itemHandler */
    $linkHandler = \XoopsModules\Tag\Helper::getInstance()->getHandler('Link');

    if (!$itemHandler || !$linkHandler) {
        $result = false;
    } else {
        $mid           = XoopsFilterInput::clean($mid, 'INT');
        $moduleHandler = xoops_getHandler('module');
        $rqModule      = XoopsModule::getByDirname('randomquote');

        // check to make sure module is active and trying to sync randomquote
        if (($rqModule instanceof XoopsModule) && $rqModule->isactive() && ($rqModule->mid() == $mid)) {
            // clear tag-item links
            $sql    = "DELETE FROM {$linkHandler->table}"
                      . " WHERE tag_modid = {$mid}"
                      . '    AND '
                      . '    (tag_itemid NOT IN '
                      . "        (SELECT DISTINCT {$itemHandler->keyName} "
                      . "           FROM {$itemHandler->table} "
                      . "           WHERE {$itemHandler->table}.quote_status = "
                      .Constants::STATUS_ONLINE
                      . '        )'
                      . '    )';
            $result = $linkHandler->db->queryF($sql);
        } else {
            $result = false;
        }
    }

    return $result ? true : false;
}
