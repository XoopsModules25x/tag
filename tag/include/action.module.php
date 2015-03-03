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
 * @version         $Id: action.module.php 12908 2014-12-19 19:59:59Z zyspec $
 * */

defined('XOOPS_ROOT_PATH') || exit('Restricted access');
defined("TAG_INI") || include __DIR__ . "/vars.php";

function xoops_module_install_tag(&$module)
{
    return true;
}

function xoops_module_pre_install_tag(&$module)
{
    //check for minimum XOOPS version
    $currentVer  = substr(XOOPS_VERSION, 6); // get the numeric part of string
    $currArray   = explode('.', $currentVer);
    $requiredVer = "" . $module->getInfo('min_xoops'); //making sure it's a string
    $reqArray    = explode('.', $requiredVer);
    $success     = true;
    foreach ($reqArray as $k=>$v) {
        if (isset($currArray[$k])) {
            if ($currArray[$k] >= $v) {
                continue;
            } else {
                $success = false;
                break;
            }
        } else {
            if ($v > 0) {
                $success = false;
                break;
            }
        }
    }
    if (!$success) {
        $module->setErrors("This module requires XOOPS {$requiredVer}+ ({$currentVer} installed)");

        return false;
    }

    // check for minimum PHP version
    $phpLen    = strlen(PHP_VERSION);
    $extraLen  = strlen(PHP_EXTRA_VERSION);
    $verNum = substr(PHP_VERSION, 0, ($phpLen-$extraLen));
    $reqVer = $module->getInfo('min_php');
    if ($verNum < $reqVer) {
        $module->setErrors( "The module requires PHP {$reqVer}+ ({$verNum} installed)");

        return false;
    }

    /*
    if (!file_exists($GLOBALS['xoops']->path("/Frameworks/art/functions.ini.php"))) {
        $module->setErrors( "The module requires /Frameworks/art/" );

        return false;
    }
    */

    $mod_tables = $module->getInfo("tables");
    foreach ($mod_tables as $table) {
        $GLOBALS["xoopsDB"]->queryF("DROP TABLE IF EXISTS " .  $GLOBALS["xoopsDB"]->prefix($table) . ";");
    }

    return true;
}

function xoops_module_pre_update_tag(&$module)
{
    return true;
}

function xoops_module_pre_uninstall_tag(&$module)
{
    return true;
}

function xoops_module_update_tag(&$module, $prev_version = null)
{
    //load_functions("config");
    //mod_clearConfg($module->getVar("dirname", "n"));

    if ($prev_version <= 150) {
        $GLOBALS['xoopsDB']->queryFromFile($GLOBALS['xoops']->path("/modules/" . $module->getVar("dirname") . "/sql/mysql.150.sql"));
    }

    /* Do some synchronization */
    include_once $GLOBALS['xoops']->path("/modules/" . $module->getVar("dirname") . "/include/functions.recon.php");
    tag_synchronization();

    return true;
}
