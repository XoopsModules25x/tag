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
 * @copyright       The XOOPS project http://sourceforge.net/projects/xoops/
 * @license         http://www.fsf.org/copyleft/gpl.html GNU public license
 * @since           1.0.0
 * @author          Taiwen Jiang <phppp@users.sourceforge.net>
 * @version         $Id: sample.php 10505 2012-12-23 03:33:54Z beckmi $
 * @package         tag
 */
 
/*
This module provides a centralized toolkit including input, display, stats and substantial more comprehensive applications, so that each module does not need to develop its own tag handling scripts.

Check http://en.wikipedia.org/wiki/Tags for more info about "tag"
*/

/*
terms:
itemid:         unique ID of the object to which the tag belonging, for instance topic_id for newbb topic, art_id for article article;
modid:          module ID of the object
catid:          extra parameter to identify an object. Only useful when you have more than one types of objects in the same module, for instance in a gallery module, type #1 tags are for images, type #2 tags for albums: (IDofImage, mid, catid=1), (IDofAlbum, mid, catid=2)
*/

/*
To enable tag for a module ("mymodule"), following steps are need:
1 add tag input box to your item edit form (required)
2 add tag storage to your item submission page (required)
3 define functions to build info of tagged items (required)
4 add tag display API to your item display page and include tag template in your item template (optional)
5 add module tag view page and tag list page (optional)
6 add module tag blocks (optional) 
*/

/* Step 1: add tag input box */
// File: edit.item.php
$itemid = $item_obj->isNew() ? 0 : $item_obj->getVar("itemid");
include_once XOOPS_ROOT_PATH . "/modules/tag/include/formtag.php";
$form_item->addElement(new XoopsFormTag("item_tag", 60, 255, $itemid, $catid = 0));

/* Step 2: add tag storage after item storage */
// File: submit.item.php
$tag_handler = xoops_getmodulehandler('tag', 'tag');
$tag_handler->updateByItem($_POST["item_tag"], $itemid, $xoopsModule->getVar("dirname"), $catid = 0);

/* Step 3: define functions to build info of tagged items */
// File: /modules/tag/plugin/mymodule.php OR /modules/mymodule/include/plugin.tag.php
/** Get item fields: title, content, time, link, uid, uname, tags **/
function mymodule_tag_iteminfo(&$items)
{
    $items_id = array();
    foreach (array_keys($items) as $cat_id) {
        // Some handling here to build the link upon catid
            // If catid is not used, just skip it
        foreach (array_keys($items[$cat_id]) as $item_id) {
            // In article, the item_id is "art_id"
            $items_id[] = intval($item_id);
        }
    }
    $item_handler =& xoops_getmodulehandler("item", "module");
    $items_obj = $item_handler->getObjects(new Criteria("itemid", "(" . implode(", ", $items_id) . ")", "IN"), true);
    
    foreach (array_keys($items) as $cat_id) {
        foreach (array_keys($items[$cat_id]) as $item_id) {
            $item_obj =& $items_obj[$item_id];
            $items[$cat_id][$item_id] = array(
                "title"     => $item_obj->getVar("item_title"),
                "uid"       => $item_obj->getVar("uid"),
                "link"      => "view.item.php?itemid={$item_id}",
                "time"      => $item_obj->getVar("item_time"),
                "tags"      => tag_parse_tag($item_obj->getVar("item_tags", "n")), // optional
                "content"   => "",
                );
        }
    }
    unset($items_obj);    
}
/** Remove orphan tag-item links **/
function mymodule_tag_synchronization($mid) 
{
    // Optional
}


/* Step 4: Display tags on our tiem page */
// File: view.item.php
include_once XOOPS_ROOT_PATH."/modules/tag/include/tagbar.php";
$xoopsTpl->assign('tagbar', tagBar($itemid, $catid = 0));
// File: mymodule_item_template.html
<{include file="db:tag_bar.html"}>

/* Step 5: create tag list page and tag view page */
// File: list.tag.php
include "header.php";
include XOOPS_ROOT_PATH . "/modules/tag/list.tag.php";
// File: view.tag.php
include "header.php";
include XOOPS_ROOT_PATH . "/modules/tag/view.tag.php";

/* Step 6: create tag blocks */
// File: xoops_version.php
/*
 * $options:  
 *                    $options[0] - number of tags to display
 *                    $options[1] - time duration, in days, 0 for all the time
 *                    $options[2] - max font size (px or %)
 *                    $options[3] - min font size (px or %)
 */
$modversion["blocks"][]    = array(
    "file"            => "mymodule_block_tag.php",
    "name"            => "Module Tag Cloud",
    "description"    => "Show tag cloud",
    "show_func"        => "mymodule_tag_block_cloud_show",
    "edit_func"        => "mymodule_tag_block_cloud_edit",
    "options"        => "100|0|150|80",
    "template"        => "mymodule_tag_block_cloud.html",
    );
/*
 * $options:
 *                    $options[0] - number of tags to display
 *                    $options[1] - time duration, in days, 0 for all the time
 *                    $options[2] - sort: a - alphabet; c - count; t - time
 */
$modversion["blocks"][]    = array(
    "file"            => "mymodule_block_tag.php",
    "name"            => "Module Top Tags",
    "description"    => "Show top tags",
    "show_func"        => "mymodule_tag_block_top_show",
    "edit_func"        => "mymodule_tag_block_top_edit",
    "options"        => "50|30|c",
    "template"        => "mymodule_tag_block_top.html",
    );
// File: module_block_tag.php
function mymodule_tag_block_cloud_show($options) 
{
    include_once XOOPS_ROOT_PATH . "/modules/tag/blocks/block.php";
    return tag_block_cloud_show($options, $moduleDirName);
}
function mymodule_tag_block_cloud_edit($options) 
{
    include_once XOOPS_ROOT_PATH . "/modules/tag/blocks/block.php";
    return tag_block_cloud_edit($options);
}
function mymodule_tag_block_top_show($options) 
{
    include_once XOOPS_ROOT_PATH . "/modules/tag/blocks/block.php";
    return tag_block_top_show($options, $moduleDirName);
}
function mymodule_tag_block_top_edit($options) 
{
    include_once XOOPS_ROOT_PATH . "/modules/tag/blocks/block.php";
    return tag_block_top_edit($options);
}
// File: mymodule_tag_block_cloud.html
<{include file="db:tag_block_cloud.html"}>
// File: mymodule_tag_block_top.html
<{include file="db:tag_block_top.html"}>

/* Over */
?>