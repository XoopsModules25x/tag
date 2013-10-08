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
 * @version         $Id: functions.ini.php 8164 2011-11-06 22:36:42Z beckmi $
 * @package         tag
 */

/*

The functions loaded on initializtion
*/

if (!defined('XOOPS_ROOT_PATH')) { exit(); }
if (!defined('TAG_INI')) { exit(); }

if (!defined("TAG_FUNCTIONS_INI")):
define("TAG_FUNCTIONS_INI", 1);

function &tag_load_config()
{
    global $xoopsModuleConfig;
    static $moduleConfig;
    
    if (isset($moduleConfig)) {
        return $moduleConfig;
    }
    
    if (isset($GLOBALS["xoopsModule"]) && is_object($GLOBALS["xoopsModule"]) && $GLOBALS["xoopsModule"]->getVar("dirname", "n") == "tag") {
        if (!empty($GLOBALS["xoopsModuleConfig"])) {
            $moduleConfig = $GLOBALS["xoopsModuleConfig"];
        } else {
            return null;
        }
    } else {
        $module_handler =& xoops_gethandler('module');
        $module = $module_handler->getByDirname("tag");
    
        $config_handler =& xoops_gethandler('config');
        $criteria = new CriteriaCompo(new Criteria('conf_modid', $module->getVar('mid')));
        $configs = $config_handler->getConfigs($criteria);
        foreach (array_keys($configs) as $i) {
            $moduleConfig[$configs[$i]->getVar('conf_name')] = $configs[$i]->getConfValueForOutput();
        }
        unset($configs);
    }
    if ($customConfig = @include XOOPS_ROOT_PATH . "/modules/tag/include/plugin.php") {
        $moduleConfig = array_merge($moduleConfig, $customConfig);
    }
    
    return $moduleConfig;
}

function tag_define_url_delimiter()
{
    if (defined("URL_DELIMITER")) {
        if (!in_array(URL_DELIMITER, array("?","/"))) die("Exit on security");
    } else {
        $moduleConfig = tag_load_config();
        if (empty($moduleConfig["do_urw"])) {
            define("URL_DELIMITER", "?");
        } else {
            define("URL_DELIMITER", "/");
        }
    }
}

function tag_get_delimiter()
{
    xoops_loadLanguage("config", "tag");
    if (!empty($GLOBALS["tag_delimiter"])) return $GLOBALS["tag_delimiter"];
    $moduleConfig = tag_load_config();
    if (!empty($moduleConfig["tag_delimiter"])) return $moduleConfig["tag_delimiter"];
    return array(",", " ", "|", ";");
}

endif;
?>