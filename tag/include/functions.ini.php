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
 * @version         $Id: functions.ini.php 12898 2014-12-08 22:05:21Z zyspec $
 */

/*

The functions loaded on initializtion
*/

defined('XOOPS_ROOT_PATH') || exit('Restricted access');
defined('TAG_INI') || exit();
(!defined("TAG_FUNCTIONS_INI")) || exit();

define("TAG_FUNCTIONS_INI", 1);

function &tag_load_config()
{
    static $moduleConfig;

    if (!isset($moduleConfig)) {
        if (isset($GLOBALS["xoopsModule"])
            && ($GLOBALS["xoopsModule"] instanceof XoopsModule)
            && ('tag' == $GLOBALS["xoopsModule"]->getVar("dirname", "n")))
        {
            if (!empty($GLOBALS["xoopsModuleConfig"])) {
                $moduleConfig = $GLOBALS["xoopsModuleConfig"];
            } else {
                $moduleConfig = null;
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
        if (file_exists($GLOBALS['xoops']->path("/modules/tag/include/plugin.php"))) {
            $customConfig = include $GLOBALS['xoops']->path("/modules/tag/include/plugin.php");
            $moduleConfig = array_merge($moduleConfig, $customConfig);
        }
    }

    return $moduleConfig;
}

function tag_define_url_delimiter()
{
    if (defined("URL_DELIMITER")) {
        if (!in_array(URL_DELIMITER, array("?","/"))){
            exit("Security Violation");
        }
    } else {
        $moduleConfig = tag_load_config();
        if (empty($GLOBALS['moduleConfig']['do_urw'])) {
            define("URL_DELIMITER", "?");
        } else {
            define("URL_DELIMITER", "/");
        }
    }
}

function tag_get_delimiter()
{
    xoops_loadLanguage("config", "tag");
    $retVal = array(",", " ", "|", ";");

    if (!empty($GLOBALS["tag_delimiter"])) {
        $retVal = $GLOBALS["tag_delimiter"];
    } else {
        $moduleConfig = tag_load_config();
        if (!empty($GLOBALS['moduleConfig']['tag_delimiter'])) {
            $retVal = $GLOBALS['moduleConfig']['tag_delimiter'];
        }
    }

    return $retVal;
}
