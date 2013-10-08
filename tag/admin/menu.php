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
 * @version         $Id: menu.php 10505 2012-12-23 03:33:54Z beckmi $
 * @package         tag
 */

if (!defined('XOOPS_ROOT_PATH')) { exit(); }

$module_handler =& xoops_gethandler('module');
$xoopsModule =& XoopsModule::getByDirname('tag');
$moduleInfo =& $module_handler->get($xoopsModule->getVar('mid'));
$pathIcon32 = $moduleInfo->getInfo('icons32');

$adminmenu = array();

$i = 1;
$adminmenu[$i]["title"] = TAG_MI_ADMENU_INDEX;
$adminmenu[$i]["link"]  = "admin/index.php";
$adminmenu[$i]["desc"] = _TAG_ADMIN_HOME_DESC;
$adminmenu[$i]["icon"] = $pathIcon32.'/home.png';
$i++;
$adminmenu[$i]["title"] = TAG_MI_ADMENU_EDIT;
$adminmenu[$i]["link"]  = "admin/admin.tag.php";
$adminmenu[$i]["desc"] = _TAG_ADMIN_ABOUT_DESC;
$adminmenu[$i]["icon"] = $pathIcon32.'/administration.png';
$i++;
$adminmenu[$i]["title"] = TAG_MI_ADMENU_SYNCHRONIZATION;
$adminmenu[$i]["link"]  = "admin/syn.tag.php";
$adminmenu[$i]["desc"] = _TAG_ADMIN_HELP_DESC;
$adminmenu[$i]["icon"] = $pathIcon32.'/synchronized.png';
$i++;
$adminmenu[$i]["title"] = _TAG_ADMIN_ABOUT;
$adminmenu[$i]["link"]  = "admin/about.php";
$adminmenu[$i]["desc"] = _TAG_ADMIN_ABOUT_DESC;
$adminmenu[$i]["icon"] = $pathIcon32.'/about.png';