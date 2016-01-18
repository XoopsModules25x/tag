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
 * @copyright       Gregory Mage (Aka Mage)
 * @license         {@link http://www.fsf.org/copyleft/gpl.html GNU public license}
 * @author          Gregory Mage (Aka Mage)
 * @since           1.00
 * @version         $Id: xfaq.php 12898 2014-12-08 22:05:21Z zyspec $
 */

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

function xfaq_tag_iteminfo(&$items)
{
    if (empty($items) || !is_array($items)) {
        return false;
    }

    $items_id = array();
    foreach (array_keys($items) as $cat_id) {
        foreach (array_keys($items[$cat_id]) as $item_id) {
            $items_id[] = intval($item_id);
        }
    }

    $item_handler =& xoops_getmodulehandler('faq', 'xfaq');
    $items_obj = $item_handler->getObjects(new Criteria("faq_id", "(" . implode(", ", $items_id) . ")", "IN"), true);

    foreach (array_keys($items) as $cat_id) {
        foreach (array_keys($items[$cat_id]) as $item_id) {
            if (isset($items_obj[$item_id])) {
                $item_obj =& $items_obj[$item_id];
                $items[$cat_id][$item_id] = array('title' => $item_obj->getVar("faq_question"),
                                                    'uid' => $item_obj->getVar("faq_submitter"),
                                                   'link' => "faq.php?faq_id={$item_id}",
                                                   'time' => $item_obj->getVar("faq_date_created"),
                                                   'tags' => '',
                                                'content' => ''
                );
            }
        }
    }
    unset($items_obj);

    return true;
}

function xfaq_tag_synchronization($mid)
{
   // Optional
}
