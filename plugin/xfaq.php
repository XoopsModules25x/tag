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
 * @copyright       Gregory Mage (Aka Mage)
 * @license         {@link http://www.fsf.org/copyleft/gpl.html GNU public license}
 * @author          Gregory Mage (Aka Mage)
 * @since           1.00
 */

defined('XOOPS_ROOT_PATH') || die('Restricted access');

/**
 * @param $items
 * @return bool
 */
function xfaq_tag_iteminfo(&$items)
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

    /** @var XfaqFaqHandler $itemHandler */
    $itemHandler = xoops_getModuleHandler('faq', 'xfaq');
    $items_obj   =& $itemHandler->getObjects(new \Criteria('faq_id', '(' . implode(', ', $items_id) . ')', 'IN'), true);

    foreach (array_keys($items) as $cat_id) {
        foreach (array_keys($items[$cat_id]) as $item_id) {
            if (isset($items_obj[$item_id])) {
                $item_obj                 = $items_obj[$item_id];
                $items[$cat_id][$item_id] = [
                    'title'   => $item_obj->getVar('faq_question'),
                    'uid'     => $item_obj->getVar('faq_submitter'),
                    'link'    => "faq.php?faq_id={$item_id}",
                    'time'    => $item_obj->getVar('faq_date_created'),
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
 * @param $mid
 */
function xfaq_tag_synchronization($mid)
{
    // Optional
}
