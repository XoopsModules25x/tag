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
 * @copyright       XOOPS Project (https://xoops.org)
 * @license         http://www.fsf.org/copyleft/gpl.html GNU public license
 * @since           1.00
 * @author          Taiwen Jiang <phppp@users.sourceforge.net>
 * */

use XoopsModules\Tag;

defined('XOOPS_ROOT_PATH') || die('Restricted access');
defined('TAG_INI') || include __DIR__ . '/vars.php';

/**
 * @param  XoopsModule $module
 * @return bool
 */
function xoops_module_install_tag(\XoopsModule $module)
{
    return true;
}

/**
 * @param  XoopsModule $module
 * @return bool
 */
function xoops_module_pre_install_tag(\XoopsModule $module)
{
    //check for minimum XOOPS version
    $currentVer  = substr(XOOPS_VERSION, 6); // get the numeric part of string
    $currArray   = explode('.', $currentVer);
    $requiredVer = '' . $module->getInfo('min_xoops'); //making sure it's a string
    $reqArray    = explode('.', $requiredVer);

    $success = false;
    if (version_compare($currentVer, $requiredVer) >= 0) {
        $success = true;
    }

    if (false === $success) {
        $module->setErrors("This module requires XOOPS {$requiredVer}+ ({$currentVer} installed)");

        return false;
    }

    // check for minimum PHP version
    $phpLen   = strlen(PHP_VERSION);
    $extraLen = strlen(PHP_EXTRA_VERSION);
    $verNum   = substr(PHP_VERSION, 0, $phpLen - $extraLen);
    $reqVer   =& $module->getInfo('min_php');
    if ($verNum < $reqVer) {
        $module->setErrors("The module requires PHP {$reqVer}+ ({$verNum} installed)");

        return false;
    }

    /*
    if (!file_exists($GLOBALS['xoops']->path("/Frameworks/art/functions.ini.php"))) {
        $module->setErrors( "The module requires /Frameworks/art/" );

        return false;
    }
    */

    $mod_tables =& $module->getInfo('tables');
    foreach ($mod_tables as $table) {
        $GLOBALS['xoopsDB']->queryF('DROP TABLE IF EXISTS ' . $GLOBALS['xoopsDB']->prefix($table) . ';');
    }

    return true;
}

/**
 * @param  XoopsModule $module
 * @return bool
 */
function xoops_module_pre_update_tag(\XoopsModule $module)
{
    /** @var Tag\Utility $utility */
    $moduleDirName = basename(dirname(__DIR__));
    $utility      = new Tag\Utility();

    $xoopsSuccess = $utility::checkVerXoops($module);
    $phpSuccess   = $utility::checkVerPhp($module);
    return $xoopsSuccess && $phpSuccess;
}

/**
 * @param  XoopsModule $module
 * @return bool
 */
function xoops_module_pre_uninstall_tag(\XoopsModule $module)
{
    return true;
}

/**
 * @param  XoopsModule $module
 * @param  null        $prev_version
 * @return bool
 */
function xoops_module_update_tag(\XoopsModule $module, $prev_version = null)
{
    //load_functions("config");
    //mod_clearConfg($module->getVar("dirname", "n"));

    if ($prev_version <= 150) {
        $GLOBALS['xoopsDB']->queryFromFile($GLOBALS['xoops']->path('/modules/' . $module->getVar('dirname') . '/sql/mysql.150.sql'));
    }

    /* Do some synchronization */
    require_once $GLOBALS['xoops']->path('/modules/' . $module->getVar('dirname') . '/include/functions.recon.php');
    tag_synchronization();

    return true;
}
