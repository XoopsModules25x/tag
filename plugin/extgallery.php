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
 * @package         tag
 * @copyright       {@link http://sourceforge.net/projects/xoops/ The XOOPS Project}
 * @license         {@link http://www.fsf.org/copyleft/gpl.html GNU public license}
 * @since           1.00
 */

defined('XOOPS_ROOT_PATH') || die('Restricted access');
/**
 *
 * Generate tag item information
 *
 * @param array $items is an array containing category and item information
 *
 * @return bool
 */
function extgallery_tag_iteminfo(&$items)
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

    /** @var \ExtgalleryPublicPhotoHandler $itemHandler */
    $itemHandler = xoops_getModuleHandler('publicphoto', 'extgallery');
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
                    'content' => ''
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
 * @return boolean
 *
 */
function extgallery_tag_synchronization($mid)
{
    /** @var \ExtgalleryPublicPhotoHandler $itemHandler */
    $itemHandler = xoops_getModuleHandler('publicphoto', 'extgallery');
    /** @var \TagLinkHandler $linkHandler */
    $linkHandler = \XoopsModules\Tag\Helper::getInstance()->getHandler('Link'); //@var \XoopsModules\Tag\Handler $tagHandler

    $mid = XoopsFilterInput::clean($mid, 'INT');

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

    return $result ? true : false;
}
