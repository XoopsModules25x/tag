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
 * @subpackage     admin
 * @copyright      {@link http://sourceforge.net/projects/xoops/ The XOOPS Project}
 * @license        {@link http://www.fsf.org/copyleft/gpl.html GNU public license}
 * @author         Taiwen Jiang <phppp@users.sourceforge.net>
 * @since          1.00
 * @version        $Id: index.php 12898 2014-12-08 22:05:21Z zyspec $
 */

require_once __DIR__ . '/admin_header.php';
xoops_cp_header();
include $GLOBALS['xoops']->path("/modules/tag/include/vars.php");

$indexAdmin = new ModuleAdmin();

$tag_handler =& xoops_getmodulehandler("tag", $thisModuleDir);
$count_tag = $tag_handler->getCount();

$count_item = 0;
$sql  = "SELECT COUNT(DISTINCT tl_id) FROM " . $GLOBALS['xoopsDB']->prefix("tag_link");
if (false === ($result = $GLOBALS['xoopsDB']->query($sql))) {
    xoops_error($GLOBALS['xoopsDB']->error());
} else {
    list($count_item) = $GLOBALS['xoopsDB']->fetchRow($result);
}

$sql  = "SELECT tag_modid, SUM(tag_count) AS count_item, COUNT(DISTINCT tag_id) AS count_tag";
$sql .= " FROM " . $GLOBALS['xoopsDB']->prefix("tag_stats");
$sql .= " GROUP BY tag_modid";
$counts_module = array();
if (false === ($result = $GLOBALS['xoopsDB']->query($sql))) {
    xoops_error($GLOBALS['xoopsDB']->error());
} else {
    while ($myrow = $GLOBALS['xoopsDB']->fetchArray($result)) {
        $counts_module[$myrow["tag_modid"]] = array("count_item" => $myrow["count_item"], "count_tag" => $myrow["count_tag"]);
    }
    if (!empty($counts_module)) {
        $module_handler =& xoops_gethandler("module");
        $module_list = $module_handler->getList(new Criteria("mid", "(" . implode(", ", array_keys($counts_module)) . ")", "IN"));
    } else {

    }
}

$indexAdmin->addInfoBox(_AM_TAG_STATS) ;
$indexAdmin->addInfoBoxLine(_AM_TAG_STATS, "<infolabel>" . _AM_TAG_COUNT_TAG . "</infolabel>" , $count_tag) ;
$indexAdmin->addInfoBoxLine(_AM_TAG_STATS, "<infolabel>" . _AM_TAG_COUNT_ITEM . "</infolabel>" , $count_item ."<br /><br />") ;
$indexAdmin->addInfoBoxLine(_AM_TAG_STATS, "<infolabel>" . _AM_TAG_COUNT_MODULE . "</infolabel>" . "<infotext>" . _AM_TAG_COUNT_MODULE_TITLE . "</infotext>") ;

foreach ($counts_module as $module => $count) {
    $moduleStat = "<infolabel>" . $module_list[$module] . ":</infolabel>\n"
                . "<infotext>" . $count["count_tag"] . " / " . $count["count_item"] . "\n"
                . "  [<a href='" . $GLOBALS['xoops']->url("www/modules/tag/admin/admin.tag.php?modid={$module}") . "'>" . _AM_TAG_EDIT . "</a>]\n"
                . "  [<a href='" . $GLOBALS['xoops']->url("www/modules/tag/admin/syn.tag.php?modid={$module}") . "'>" . _AM_TAG_SYNCHRONIZATION . "</a>]\n"
                . "</infotext> \n";
    $indexAdmin->addInfoBoxLine( _AM_TAG_STATS, $moduleStat);
}

if (empty($counts_module)) {  // there aren't any so just display "none"
    $moduleStat = "<infolabel>%s</infolabel><infotext>0 / 0</infotext> \n";
    $indexAdmin->addInfoBoxLine( _AM_TAG_STATS, $moduleStat, _NONE);
}

echo $indexAdmin->addNavigation('index.php');
echo $indexAdmin->renderIndex();

include __DIR__ . '/admin_footer.php';
//xoops_cp_footer();
