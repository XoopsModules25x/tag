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
 * @since           1.00
 */

use Xmf\Request;
use XoopsModules\Tag\Helper;

defined('XOOPS_ROOT_PATH') || exit('Restricted access');
/**
 * Generate tag item information
 *
 * @param array $items is an array containing category and item information
 *
 */
function extgallery_tag_iteminfo(array &$items): bool
{
    if (empty($items)) {
        return false;
    }

    $items_id = [];
    foreach (array_keys($items) as $cat_id) {
        foreach (array_keys($items[$cat_id]) as $item_id) {
            $items_id[] = (int)$item_id;
        }
    }

    /** @var \XoopsModules\Extgallery\PublicPhotoHandler $itemHandler */
    $itemHandler = \XoopsModules\Extgallery\Helper::getInstance()->getHandler('PublicPhoto');
    $items_obj   = $itemHandler->getObjects(new \Criteria('photo_id', '(' . implode(', ', $items_id) . ')', 'IN'), true);

    foreach (array_keys($items) as $cat_id) {
        foreach (array_keys($items[$cat_id]) as $item_id) {
            if (isset($items_obj[$item_id])) {
                $item_obj                 = $items_obj[$item_id];
                $items[$cat_id][$item_id] = [
                    'title'   => $item_obj->getVar('photo_title'),
                    'uid'     => $item_obj->getVar('uid'),
                    'link'    => "public-photo.php?photoId={$item_id}#photoNav",
                    'time'    => $item_obj->getVar('photo_date'),
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
 * Remove orphan tag-item links
 *
 * @param int $mid module id
 *
 */
function extgallery_tag_synchronization(int $mid): bool
{
    /** @var \XoopsModules\Extgallery\PublicPhotoHandler $itemHandler */
    $itemHandler = \XoopsModules\Extgallery\Helper::getInstance()->getHandler('PublicPhoto');
    /** @var \XoopsModules\Tag\LinkHandler $linkHandler */
    $linkHandler = Helper::getInstance()->getHandler('Link'); //@var \XoopsModules\Tag\Handler $tagHandler

    //    $mid = XoopsFilterInput::clean($mid, 'INT');
    $mid = Request::getInt('mid');

    /* clear tag-item links */
    /** {@internal the following statement isn't really needed any more (MySQL is really old)
     *   and some hosting companies block the $GLOBALS['xoopsDB']->getServerVersion() function for security
     *   reasons.}
     */
    //    if (version_compare( $GLOBALS['xoopsDB']->getServerVersion(), "4.1.0", "ge" )):
    $sql = "DELETE FROM {$linkHandler->table}" . " WHERE tag_modid = {$mid}" . ' AND (tag_itemid NOT IN ' . "       (SELECT DISTINCT {$itemHandler->keyName} " . "        FROM {$itemHandler->table} " . "          WHERE {$itemHandler->table}.photo_approved > 0" . '       )' . '     )';
    /*
        else:
        $sql =  "    DELETE {$linkHandler->table} FROM {$linkHandler->table}" .
                "    LEFT JOIN {$itemHandler->table} AS aa ON {$linkHandler->table}.tag_itemid = aa.{$itemHandler->keyName} " .
                "    WHERE " .
                "        tag_modid = {$mid}" .
                "        AND " .
                "        ( aa.{$itemHandler->keyName} IS NULL" .
                "            OR aa.photo_approved < 1" .
                "        )";
        endif;
    */
    if (!$result = $linkHandler->db->queryF($sql)) {
        //xoops_error($linkHandler->db->error());
    }

    return (bool)$result;
}
