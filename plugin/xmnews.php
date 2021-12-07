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
 * xmnews module
 *
 * @copyright       XOOPS Project (https://xoops.org)
 * @license         GNU GPL 2 (https://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @author          Mage Gregory (AKA Mage)
 */

//use XoopsModules\Tag\Helper;
use Xmf\Module\Helper;

/**
 * @param $items
 * @return bool
 */
function xmnews_tag_iteminfo(&$items)
{
    if (empty($items) || !is_array($items)) {
        return false;
    }

    $items_id = [];
    foreach (array_keys($items) as $cat_id) {
        // Some handling here to build the link upon catid
        // If catid is not used, just skip it
        foreach (array_keys($items[$cat_id]) as $item_id) {
            // In article, the item_id is "art_id"
            $items_id[] = (int)$item_id;
        }
    }
	$helper      = Helper::getHelper('xmnews');
	$newsHandler = $helper->getHandler('xmnews_news');
    $items_obj   = $newsHandler->getObjects(new \Criteria('news_id', '(' . implode(', ', $items_id) . ')', 'IN'), true);
	if (count($items_obj) > 0){
		foreach (array_keys($items) as $cat_id) {
			foreach (array_keys($items[$cat_id]) as $item_id) {
				$item_obj                 = $items_obj[$item_id];
				$items[$cat_id][$item_id] = [
					'title'   => $item_obj->getVar('news_title'),
					'uid'     => $item_obj->getVar('news_userid'),
					'link'    => "article.php?news_id={$item_id}",
					'time'    => $item_obj->getVar('news_date'),
					'tags'    => \XoopsModules\Tag\Utility::tag_parse_tag($item_obj->getVar('news_mkeyword', 'n')), // optional
					'content' => '',
				];
			}
		}
		unset($items_obj);
		return true;
	}
	return false;
}

/** Remove orphan tag-item links *
 * @param int $mid
 * @return bool
 */
function publisher_tag_synchronization($mid)
{
    //$itemHandler = \XoopsModules\xmnews\Helper::getInstance()->getHandler('xmnews_news');
	$helper      = Helper::getHelper('xmnews');
	$newsHandler = $helper->getHandler('xmnews_news');

    /** @var \XoopsModules\Tag\LinkHandler $itemHandler */
    $linkHandler = Helper::getInstance()->getHandler('Link');

    //    $mid = XoopsFilterInput::clean($mid, 'INT');
    $mid = \Xmf\Request::getInt('mid');

    /* clear tag-item links */
    $sql    = "    DELETE FROM {$linkHandler->table}"
              . '    WHERE '
              . "        tag_modid = {$mid}"
              . '        AND '
              . '        ( tag_itemid NOT IN '
              . "            ( SELECT DISTINCT {$newsHandler->keyName} "
              . "                FROM {$newsHandler->table} "
              . "                WHERE {$newsHandler->table}.status = 1"
              . '            ) '
              . '        )';
    $result = $linkHandler->db->queryF($sql);

    return (bool)$result;
}
