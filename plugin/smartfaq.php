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
use XoopsModules\Smartfaq\FaqHandler;
use XoopsModules\Tag\Helper;
use XoopsModules\Tag\Utility;

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

/**
 * Get item fields:
 * title
 * smartfaq
 * time
 * link
 * uid
 * uname
 * tags
 *
 * @param mixed $items
 * @var array   $items associative array of items: [modid][catid][itemid]
 *
 */

function smartfaq_tag_iteminfo(array &$items): bool
{
    if (empty($items) || !is_array($items)) {
        return false;
    }

    $items_id = [];
    foreach (array_keys($items) as $cat_id) {
        // Some handling here to build the link upon catid
        // catid is not used in smartfaq, so just skip it
        foreach (array_keys($items[$cat_id]) as $item_id) {
            // In smartfaq, the item_id is "topic_id"
            $items_id[] = (int)$item_id;
        }
    }
    /** @var \XoopsDatabase $db */
    $db          = \XoopsDatabaseFactory::getDatabase();
    $itemHandler = new FaqHandler($db);

    $items_obj = $itemHandler->getObjects(new \Criteria('faqid', '(' . implode(', ', $items_id) . ')', 'IN'), true);
    $myts      = \MyTextSanitizer::getInstance();
    foreach (array_keys($items) as $cat_id) {
        foreach (array_keys($items[$cat_id]) as $item_id) {
            $item_obj = $items_obj[$item_id];
            if (is_object($item_obj)) {
                $items[$cat_id][$item_id] = [
                    'title'   => $item_obj->getVar('question'),
                    'uid'     => $item_obj->getVar('uid'),
                    'link'    => 'faq.php?faqid=' . $item_id,
                    'time'    => strtotime($item_obj->getVar('datesub')),
                    'tags'    => Utility::tag_parse_tag($item_obj->getVar('tags', 'n')),
                    'content' => $myts->displayTarea($item_obj->answer(), 1, 1, 1, 1, 1),
                ];
            }
        }
    }
    unset($items_obj);

    return true;
}

/**
 * Remove orphan tag-item links
 *
 */
function smartfaq_tag_synchronization(int $mid): bool
{
    /** @var \XoopsDatabase $db */
    $db = \XoopsDatabaseFactory::getDatabase();

    $itemHandler = new FaqHandler($db);

    /** @var \XoopsModules\Tag\LinkHandler $itemHandler */
    $linkHandler = Helper::getInstance()->getHandler('Link');

    //    $mid = XoopsFilterInput::clean($mid, 'INT');
    $mid = Request::getInt('mid');

    /* clear tag-item links */ /** {@internal the following statement isn't really needed any more (MySQL is really old)
 *   and some hosting companies block the $GLOBALS['xoopsDB']->getServerVersion() function for security
 *   reasons. }}
 */
    //    if (version_compare( $GLOBALS['xoopsDB']->getServerVersion(), "4.1.0", "ge" )):
    $sql = "DELETE FROM {$linkHandler->table}" . " WHERE tag_modid = {$mid}" . '    AND ' . '    (tag_itemid NOT IN ' . "        (SELECT DISTINCT {$itemHandler->keyName} " . "           FROM {$itemHandler->table} " . "           WHERE {$itemHandler->table}.approved > 0" . '        )' . '    )';
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

    return (bool)$result;
}
