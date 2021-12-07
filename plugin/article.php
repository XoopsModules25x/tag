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
 * @copyright       {@link https://sourceforge.net/projects/xoops/ The XOOPS Project}
 * @license         {@link https://www.fsf.org/copyleft/gpl.html GNU public license}
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
 */
function article_tag_iteminfo(&$items): bool
{
    if (empty($items) || !is_array($items)) {
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

    /** @var \XoopsModules\Article\ArticleHandler $itemHandler */
    $itemHandler = \XoopsModules\Article\Helper::getInstance()->getHandler('Article');
    $items_obj   = $itemHandler->getObjects(new \Criteria('art_id', '(' . implode(', ', $items_id) . ')', 'IN'), true);

    foreach (array_keys($items) as $cat_id) {
        foreach (array_keys($items[$cat_id]) as $item_id) {
            $item_obj                 = $items_obj[$item_id];
            $items[$cat_id][$item_id] = [
                'title'   => $item_obj->getVar('art_title'),
                'uid'     => $item_obj->getVar('uid'),
                'link'    => "view.article.php?article={$item_id}",
                'time'    => $item_obj->getVar('art_time_publish'),
                'tags'    => Utility::tag_parse_tag($item_obj->getVar('art_keywords', 'n')),
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
 * @param int $mid module ID
 *
 */
function article_tag_synchronization($mid): bool
{
    /** @var \XoopsModules\Article\ArticleHandler $itemHandler */
    $itemHandler = \XoopsModules\Article\Helper::getInstance()->getHandler('Article', 'article');
    // /** @var \TagLinkHandler $linkHandler */
    // $linkHandler = \XoopsModules\Tag\Helper::getInstance()->getHandler('Link'); //@var \XoopsModules\Tag\Handler $tagHandler
    /** @var \XoopsModules\Tag\LinkHandler $itemHandler */
    $linkHandler = Helper::getInstance()->getHandler('Link');

    //    $mid = XoopsFilterInput::clean($mid, 'INT');
    $mid = Request::getInt('mid');
    /* clear tag-item links */
    /** {@internal the following statement isn't really needed any more (MySQL is really old)
     *   and some hosting companies block the $GLOBALS['xoopsDB']->getServerVersion() function for security
     *   reasons. }}
     */
    //    if (version_compare( $GLOBALS['xoopsDB']->getServerVersion(), "4.1.0", "ge" )) {
    $sql = "DELETE FROM {$linkHandler->table}"
           . " WHERE tag_modid = {$mid}"
           . '   AND (tag_itemid NOT IN'
           . "          (SELECT DISTINCT {$itemHandler->keyName} "
           . "            FROM {$itemHandler->table} "
           . "            WHERE {$itemHandler->table}.art_time_publish > 0"
           . '          )'
           . '       )';
    /*
        } else {
            $sql = "DELETE {$linkHandler->table} FROM {$linkHandler->table}"
                 . " LEFT JOIN {$itemHandler->table} AS aa ON {$linkHandler->table}.tag_itemid = aa.{$itemHandler->keyName} "
                 . " WHERE tag_modid = {$mid}"
                 . " AND (aa.{$itemHandler->keyName} IS NULL"
                 . " OR aa.art_time_publish < 1)";
        }
    */
    if (!$result = $linkHandler->db->queryF($sql)) {
        //xoops_error($linkHandler->db->error());
    }

    return (bool)$result;
}
