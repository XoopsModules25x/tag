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
 * @copyright       {@link http://sourceforge.net/projects/xoops/ The XOOPS Project}
 * @license         {@link http://www.fsf.org/copyleft/gpl.html GNU public license}
 * @author          Taiwen Jiang <phppp@users.sourceforge.net>
 * @since           1.00
 * @version         $Id: syn.tag.php 12898 2014-12-08 22:05:21Z zyspec $
 */

require_once __DIR__ . '/admin_header.php';
require_once $GLOBALS['xoops']->path("/class/xoopsformloader.php");
xoops_load('xoopsrequest');
//include_once $GLOBALS['xoops']->path("/modules/" . $GLOBALS['xoopsModule']->getVar("dirname") . "/class/admin.php");

include $GLOBALS['xoops']->path("/modules/tag/include/vars.php");

xoops_cp_header();

$index_admin = new ModuleAdmin();
echo $index_admin->addNavigation('syn.tag.php');

$modid = XoopsRequest::getInt('modid', TagConstants::DEFAULT_ID);
$start = XoopsRequest::getInt('start', TagConstants::BEGINNING);
$limit = XoopsRequest::getInt('limit', TagConstants::DEFAULT_LIMIT);

$sql  = "SELECT tag_modid, COUNT(DISTINCT tag_id) AS count_tag";
$sql .= " FROM " . $GLOBALS['xoopsDB']->prefix("tag_link");
$sql .= " GROUP BY tag_modid";
$counts_module = array();
$module_list = array();
if ( $result = $GLOBALS['xoopsDB']->query($sql)) {
    while ($myrow = $GLOBALS['xoopsDB']->fetchArray($result)) {
        $counts_module[$myrow["tag_modid"]] = $myrow["count_tag"];
    }
    if (!empty($counts_module)) {
        $module_handler =& xoops_gethandler("module");
        $module_list = $module_handler->getList(new Criteria("mid", "(" . implode(", ", array_keys($counts_module)) . ")", "IN"));
    }
}

$opform = new XoopsSimpleForm('', 'moduleform', xoops_getenv("PHP_SELF"), "get");
$tray = new XoopsFormElementTray('');
$mod_select = new XoopsFormSelect(_SELECT, 'modid', $modid);
$mod_select->addOption(-1, _AM_TAG_GLOBAL);
$mod_select->addOption(0, _AM_TAG_ALL);
foreach ($module_list as $module => $module_name) {
    $mod_select->addOption($module, $module_name . " (" . $counts_module[$module] . ")");
}
$tray->addElement($mod_select);
$num_select = new XoopsFormSelect(_AM_TAG_NUM, 'limit', $limit);
$num_select->addOptionArray(array(0 => _ALL, 10 => 10, 50 => 50, 100 => 100, 500 => 500));
$tray->addElement($num_select);
$tray->addElement(new XoopsFormButton("", "submit", _SUBMIT, "submit"));
$tray->addElement(new XoopsFormHidden("start", $start));
$opform->addElement($tray);
$opform->display();

if (isset($_GET['start'])) {
    $tag_handler =& xoops_getmodulehandler("tag", $thisModuleDir);

    $criteria = new CriteriaCompo();
    $criteria->setStart($start);
    $criteria->setLimit($limit);
    if ($modid > TagConstants::DEFAULT_ID) {
        $criteria->add(new Criteria("l.tag_modid", $modid));
    }
    $tags = $tag_handler->getByLimit($criteria, false);
    if (empty($tags)) {
        echo "<h2>" . _AM_TAG_FINISHED . "</h2>\n";
    } else {
        foreach (array_keys($tags) as $tag_id) {
            $tag_handler->update_stats($tag_id, (-1 == $modid) ? TagConstants::DEFAULT_ID : $tags[$tag_id]["modid"]);
        }
        redirect_header("syn.tag.php?modid={$modid}&amp;start=" . ($start + $limit) . "&amp;limit={$limit}", TagConstants::REDIRECT_DELAY_SHORT, _AM_TAG_IN_PROCESS);
    }
}
include __DIR__ . '/admin_footer.php';
