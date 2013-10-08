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
 * @version         $Id: action.module.php 8164 2011-11-06 22:36:42Z beckmi $
 * @package         tag
 */

if (!defined('XOOPS_ROOT_PATH')) { exit(); }
defined("TAG_INI") || include dirname(__FILE__) . "/vars.php";

function xoops_module_install_tag(&$module)
{
    return true;
}

function xoops_module_pre_install_tag(&$module)
{
    if (substr(XOOPS_VERSION, 0, 9) < "XOOPS 2.3") {
        $module->setErrors( "The module only works for XOOPS 2.3+" );
        return false;
    }
    
    /*
    if (!file_exists(XOOPS_ROOT_PATH . "/Frameworks/art/functions.ini.php")) {
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
        $GLOBALS['xoopsDB']->queryFromFile(XOOPS_ROOT_PATH . "/modules/" . $module->getVar("dirname") . "/sql/mysql.150.sql");
    }
    
    /* Do some synchronization */
    include_once XOOPS_ROOT_PATH . "/modules/" . $module->getVar("dirname") . "/include/functions.recon.php";
    //mod_loadFunctions("recon", $module->getVar("dirname"));
    tag_synchronization();
    return true;
}
?>