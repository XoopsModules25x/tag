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
 * @since           1.00
 * @author          Taiwen Jiang <phppp@users.sourceforge.net>
 * @version         $Id: admin_header.php 12898 2014-12-08 22:05:21Z zyspec $
 * */

$path = dirname(dirname(dirname(__DIR__)));
require_once $path . '/include/cp_header.php';

require_once dirname(__DIR__) . '/include/vars.php';
require_once dirname(__DIR__) . '/include/functions.php';
xoops_load('constants', 'tag');

$thisModuleDir = $GLOBALS['xoopsModule']->getVar('dirname');
// Load language files
xoops_loadLanguage('admin', $thisModuleDir);
xoops_loadLanguage('modinfo', $thisModuleDir);
xoops_loadLanguage('main', $thisModuleDir);

$pathIcon16 = $GLOBALS['xoops']->url('www/' . $GLOBALS['xoopsModule']->getInfo('icons16'));
$pathIcon32 = $GLOBALS['xoops']->url('www/' . $GLOBALS['xoopsModule']->getInfo('icons32'));
$pathModuleAdmin = $GLOBALS['xoops']->path('www/' . $GLOBALS['xoopsModule']->getInfo('dirmoduleadmin'));

if ( file_exists("{$pathModuleAdmin}/moduleadmin/moduleadmin.php")) {
    include_once "{$pathModuleAdmin}/moduleadmin/moduleadmin.php";
} else {
    redirect_header("{$path}/admin.php", TagConstants::REDIRECT_DELAY_LONG, _AM_MODULEADMIN_MISSING, false);
}

include_once $GLOBALS['xoops']->path("/Frameworks/art/functions.admin.php");
