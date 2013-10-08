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
 * @version         $Id: admin.tag.php 11906 2013-08-14 05:54:12Z beckmi $
 * @package         tag
 */
 
include_once 'admin_header.php';
require_once XOOPS_ROOT_PATH . "/class/xoopsformloader.php";
$indexAdmin = new ModuleAdmin();

xoops_cp_header();

include XOOPS_ROOT_PATH . "/modules/tag/include/vars.php";
//echo function_exists("loadModuleAdminMenu") ? loadModuleAdminMenu(1) : "";
    echo $indexAdmin->addNavigation('admin.tag.php');

global $xoopsModuleConfig;

$limit = $xoopsModuleConfig['items_perpage'];
$modid = intval( empty($_GET['modid']) ? @$_POST['modid'] : $_GET['modid'] );
$start = intval( empty($_GET['start']) ? @$_POST['start'] : $_GET['start'] );
//$status = intval( empty($_GET['status']) ? @$_POST['status'] : $_GET['status']);

$status = intval( (isset($_GET['status']) && $_GET['status'] >-1) ? $_GET['status'] : -1);


$tag_handler =& xoops_getmodulehandler("tag", $xoopsModule->getVar("dirname"));

if (!empty($_POST['tags'])) {
    $msgDBUpdated='';
    foreach ($_POST['tags'] as $tag => $tag_status) {
        $tag_obj =& $tag_handler->get($tag);
        if (!is_object($tag_obj) || !$tag_obj->getVar("tag_id")) continue;
        if ($tag_status < 0) {
            $tag_handler->delete($tag_obj);
        } elseif ($tag_status != $tag_obj->getVar("tag_status")) {
            $tag_obj->setVar("tag_status", $tag_status);
            $tag_handler->insert($tag_obj);
            $msgDBUpdated = _AM_TAG_DB_UPDATED;
        }
    }
    redirect_header("admin.tag.php?modid={$modid}&amp;start={$start}&amp;status={$status}", 2, $msgDBUpdated);
    exit();
}

$sql  = "    SELECT tag_modid, COUNT(DISTINCT tag_id) AS count_tag";
$sql .= "    FROM " . $xoopsDB->prefix("tag_link");
$sql .= "    GROUP BY tag_modid";
$counts_module = array();
$module_list = array();
if ( ($result = $xoopsDB->query($sql)) == false) {
    xoops_error($xoopsDB->error());
} else {
    while ($myrow = $xoopsDB->fetchArray($result)) {
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
$mod_select->addOption(0, _ALL);
foreach ($module_list as $module => $module_name) {
    $mod_select->addOption($module, $module_name." (" . $counts_module[$module] . ")");
}
$tray->addElement($mod_select);
$status_select = new XoopsFormRadio("", 'status', $status);
$status_select->addOption(-1, _ALL);
$status_select->addOption(1, TAG_AM_ACTIVE);
$status_select->addOption(0, TAG_AM_INACTIVE);
$tray->addElement($status_select);
$tray->addElement(new XoopsFormButton("", "submit", _SUBMIT, "submit"));
$opform->addElement($tray);
$opform->display();

$criteria = new CriteriaCompo();
$criteria->setSort("a");
$criteria->setOrder("ASC");
$criteria->setStart($start);
$criteria->setLimit($limit);
if ($status >= 0) {
    $criteria->add( new Criteria("o.tag_status", $status) );
}
if (!empty($modid)) {
    $criteria->add( new Criteria("l.tag_modid", $modid) );
}
$tags = $tag_handler->getByLimit($criteria, false);

$form_tags = "<form name='tags' method='post' action='" . xoops_getenv("PHP_SELF") . "'>";
$form_tags .= "<table border='0' cellpadding='4' cellspacing='1' width='100%' class='outer'>";
$form_tags .= "<tr align='center'>";
$form_tags .= "<th class='bg3'>" . TAG_AM_TERM . "</td>";
$form_tags .= "<th class='bg3' width='10%'>" . TAG_AM_ACTIVE . "</td>";
$form_tags .= "<th class='bg3' width='10%'>" . TAG_AM_INACTIVE . "</td>";
$form_tags .= "<th class='bg3' width='10%'>" . _DELETE . "</td>";
$form_tags .= "</tr>";
if (empty($tags)) {
    $form_tags .= "<tr><td colspan='4'>" . _NONE . "</td></tr>";
} else {
    $class_tr = array("odd", "even");
    $i = 0;
    foreach (array_keys($tags) as $key) {
        $form_tags .= "<tr class='" . $class_tr[(++$i) % 2] . "'>";
        $form_tags .= "<td>" . $tags[$key]["term"] . "</td>";
        $form_tags .= "<td  align='center'><input type='radio' name='tags[{$key}]' value='1' " . ( $tags[$key]["status"] ? "checked" : " '' ") . "></td>";
        $form_tags .= "<td  align='center'><input type='radio' name='tags[{$key}]' value='0' " . ( $tags[$key]["status"] ? " '' " : "checked") . "></td>";
        $form_tags .= "<td  align='center'><input type='radio' name='tags[{$key}]' value='-1'></td>";
        $form_tags .= "</tr>";
    }
    if (  !empty($start) || count($tags) >= $limit ) {
        $count_tag = $tag_handler->getCount($criteria);
    
        include XOOPS_ROOT_PATH . "/class/pagenav.php";
        $nav = new XoopsPageNav($count_tag, $limit, $start, "start", "modid={$modid}&amp;status={$status}");
        $form_tags .= "<tr><td colspan='4' align='right'>" . $nav->renderNav(4) . "</td></tr>";
    }
    $form_tags .= "<tr><td colspan='4' align='center'>";
    $form_tags .= "<input type='hidden' name='status' value='{$status}'> ";
    $form_tags .= "<input type='hidden' name='start' value='{$start}'> ";
    $form_tags .= "<input type='hidden' name='modid' value='{$modid}'> ";
    $form_tags .= "<input type='submit' name='submit' value='" . _SUBMIT . "'> ";
    $form_tags .= "<input type='reset' name='submit' value='" . _CANCEL . "'>";
    $form_tags .= "</td></tr>";
}
$form_tags .= "</table>";
$form_tags .= "</form>";

echo $form_tags;
include "admin_footer.php";
