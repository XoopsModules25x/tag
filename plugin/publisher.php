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
 * @copyright       The XUUPS Project https://sourceforge.net/projects/xuups/
 * @license         https://www.fsf.org/copyleft/gpl.html GNU public license
 * @since           1.0
 * @author          trabis <lusopoemas@gmail.com>
 * @author          The SmartFactory <www.smartfactory.ca>
 */

use Xmf\Request;
use XoopsModules\Publisher\Helper as PublisherHelper;
use XoopsModules\Tag\Helper;
use XoopsModules\Tag\Utility;

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

/**
 * Get item fields: title, content, time, link, uid, tags
 */
function publisher_tag_iteminfo(array &$items): bool
{
    if (empty($items)) {
        return false;
    }

    $items_id = [];

    foreach (array_keys($items) as $cat_id) {
        // Some handling here to build the link upon catid
        foreach (array_keys($items[$cat_id]) as $item_id) {
            // In publisher, the item_id is "itemid"
            $items_id[] = (int)$item_id;
        }
    }
    /** @var \XoopsModules\Publisher\ItemHandler $itemHandler */
    $itemHandler = PublisherHelper::getInstance()->getHandler('Item');

    $criteria  = new \Criteria('itemid', '(' . implode(', ', $items_id) . ')', 'IN');
    $items_obj = $itemHandler->getObjects($criteria, 'itemid');
    //$myts      = \MyTextSanitizer::getInstance(); //uncomment it if you use the summary for the content
    foreach (array_keys($items) as $cat_id) {
        foreach (array_keys($items[$cat_id]) as $item_id) {
            $item_obj                 = $items_obj[$item_id];
            $items[$cat_id][$item_id] = [
                'title'   => $item_obj->getVar('title'),
                'uid'     => $item_obj->getVar('uid'),
                'link'    => "item.php?itemid={$item_id}",
                'time'    => $item_obj->getVar('datesub'),
                'tags'    => Utility::tag_parse_tag($item_obj->getVar('item_tag', 'n')), // optional
                'content' => '',
//                'content' => $myts->displayTarea($item_obj->summary(), 1, 1, 1, 1, 1), // in case you want to show the summary of the article
            ];
        }
    }
    unset($items_obj);

    return true;
}

/** Remove orphan tag-item links *
 */
function publisher_tag_synchronization(int $mid): bool
{
    /** @var \XoopsModules\Publisher\ItemHandler $itemHandler */
    $itemHandler = \XoopsModules\Publisher\Helper::getInstance()->getHandler('Item');

    /** @var \XoopsModules\Tag\LinkHandler $itemHandler */
    $linkHandler = Helper::getInstance()->getHandler('Link');

    //    $mid = XoopsFilterInput::clean($mid, 'INT');
    $mid = Request::getInt('mid');

    /* clear tag-item links */
    $sql    = "    DELETE FROM {$linkHandler->table}"
              . '    WHERE '
              . "        tag_modid = {$mid}"
              . '        AND '
              . '        ( tag_itemid NOT IN '
              . "            ( SELECT DISTINCT {$itemHandler->keyName} "
              . "                FROM {$itemHandler->table} "
              . "                WHERE {$itemHandler->table}.status = "
              . _CO_PUBLISHER_PUBLISHED
              . '            ) '
              . '        )';
    $result = $linkHandler->db->queryF($sql);

    return (bool)$result;
}
