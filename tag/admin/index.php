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
 * @version         $Id: index.php 10505 2012-12-23 03:33:54Z beckmi $
 * @package         tag
 */
include_once 'admin_header.php';
xoops_cp_header();

include XOOPS_ROOT_PATH . "/modules/tag/include/vars.php";
//echo function_exists("loadModuleAdminMenu") ? loadModuleAdminMenu(0) : "";

$indexAdmin = new ModuleAdmin();

$tag_handler =& xoops_getmodulehandler("tag", $xoopsModule->getVar("dirname"));
$count_tag = $tag_handler->getCount();

$count_item = 0;   
$sql  = "    SELECT COUNT(DISTINCT tl_id) FROM " . $xoopsDB->prefix("tag_link");
if ( ($result = $xoopsDB->query($sql)) == false) {
    xoops_error($xoopsDB->error());
} else {
    list($count_item) = $xoopsDB->fetchRow($result);
}

$sql  = "    SELECT tag_modid, SUM(tag_count) AS count_item, COUNT(DISTINCT tag_id) AS count_tag";
$sql .= "    FROM " . $xoopsDB->prefix("tag_stats");
$sql .= "    GROUP BY tag_modid";
$counts_module = array();
if( ($result = $xoopsDB->query($sql)) == false) {
    xoops_error($xoopsDB->error());
} else {
    while ($myrow = $xoopsDB->fetchArray($result)) {
        $counts_module[$myrow["tag_modid"]] = array("count_item" => $myrow["count_item"], "count_tag" => $myrow["count_tag"]);
    }
    if (!empty($counts_module)) {
        $module_handler =& xoops_gethandler("module");
        $module_list = $module_handler->getList(new Criteria("mid", "(" . implode(", ", array_keys($counts_module)) . ")", "IN"));
    }
}

$indexAdmin->addInfoBox(TAG_AM_STATS) ;
$indexAdmin->addInfoBoxLine(TAG_AM_STATS, "<infolabel>" .TAG_AM_COUNT_TAG. "</infolabel>" , $count_tag) ;
$indexAdmin->addInfoBoxLine(TAG_AM_STATS, "<infolabel>" .TAG_AM_COUNT_ITEM. "</infolabel>" , $count_item ."<br /><br />") ;
$indexAdmin->addInfoBoxLine(TAG_AM_STATS, "<infolabel>" . TAG_AM_COUNT_MODULE. "</infolabel><infotext>" .TAG_AM_COUNT_MODULE_TITLE."</infotext>") ;

foreach ($counts_module as $module => $count) {
    $indexAdmin->addInfoBoxLine( TAG_AM_STATS,("<infolabel>" . $module_list[$module] . ":</infolabel><infotext>" . $count["count_tag"] . " / " . $count["count_item"] . "  [<a href=\"" . XOOPS_URL . "/modules/tag/admin/admin.tag.php?modid={$module}\">" . TAG_AM_EDIT . "</a>]  [<a href=\"" . XOOPS_URL . "/modules/tag/admin/syn.tag.php?modid={$module}\">" . TAG_AM_SYNCHRONIZATION . "</a>]</infotext> "));
}

echo $indexAdmin->addNavigation('index.php');
echo $indexAdmin->renderIndex();
 
include "admin_footer.php";
//xoops_cp_footer();
