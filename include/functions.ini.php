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
 * @package         XoopsModules\Tag
 * @copyright       {@link http://sourceforge.net/projects/xoops/ The XOOPS Project}
 * @license         {@link http://www.fsf.org/copyleft/gpl.html GNU public license}
 * @author          Taiwen Jiang <phppp@users.sourceforge.net>
 * @since           1.00
 */

/*

The functions loaded on initializtion
*/

//defined('XOOPS_ROOT_PATH') || exit('Restricted access');
defined('TAG_INI') || die();
(!defined('TAG_FUNCTIONS_INI')) || die();

define('TAG_FUNCTIONS_INI', 1);

/**
 * @deprecated - use {@see Utility::tag_load_config()} method instead
 * @return array|mixed|null
 */
function tag_load_config()
{
    $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
    trigger_error(__FUNCTION__ . " is deprecated, called from {$trace[0]['file']} line {$trace[0]['line']}");
    $GLOBALS['xoopsLogger']->addDeprecated(
        'Tag Module: ' . __FUNCTION__ . " function is deprecated since Tag 2.3.4, please use 'Tag\Utility::" . __FUNCTION__ . ' method instead.'
        . " Called from {$trace[0]['file']}line {$trace[0]['line']}");

    static $moduleConfig;

    if (null === $moduleConfig) {
        if (isset($GLOBALS['xoopsModule']) && ($GLOBALS['xoopsModule'] instanceof \XoopsModule) && ('tag' === $GLOBALS['xoopsModule']->getVar('dirname', 'n'))) {
            $moduleConfig = empty($GLOBALS['xoopsModuleConfig']) ? [] : $GLOBALS['xoopsModuleConfig'];
        } else {
            /** @var \XoopsModuleHandler $moduleHandler */
            $moduleHandler = xoops_getHandler('module');
            $module = $moduleHandler->getByDirname('tag');

            /** @var \XoopsConfigHandler $configHandler */
            $configHandler = xoops_getHandler('config');
            $criteria = new \Criteria('conf_modid', $module->getVar('mid'));
            $configs = $configHandler->getConfigs($criteria);
            /** @var XoopsConfigItem $obj */
            foreach ($configs as $obj) {
                $moduleConfig[$obj->getVar('conf_name')] = $obj->getConfValueForOutput();
            }
            unset($configs);
        }
        if (file_exists($GLOBALS['xoops']->path('/modules/tag/include/plugin.php'))) {
            $customConfig = require $GLOBALS['xoops']->path('/modules/tag/include/plugin.php');
            $moduleConfig = array_merge($moduleConfig, $customConfig);
        }
    }

    return $moduleConfig;
}

/**
 * @deprecated - use {@see Utility::tag_define_url_delimiter()} method instead
 * return void
 */
function tag_define_url_delimiter()
{
    $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
    trigger_error(__FUNCTION__ . " is deprecated, called from {$trace[0]['file']} line {$trace[0]['line']}");
    $GLOBALS['xoopsLogger']->addDeprecated(
        'Tag Module: ' . __FUNCTION__ . " function is deprecated since Tag 2.3.4, please use 'Tag\Utility::" . __FUNCTION__ . ' method instead.'
        . " Called from {$trace[0]['file']}line {$trace[0]['line']}");

    if (defined('URL_DELIMITER')) {
        if (!in_array(URL_DELIMITER, ['?', '/'])) {
            exit('Security Violation');
        }
    } else {
        $moduleConfig = tag_load_config();
        if (empty($GLOBALS['moduleConfig']['do_urw'])) {
            define('URL_DELIMITER', '?');
        } else {
            define('URL_DELIMITER', '/');
        }
    }
}

/**
 * @deprecated - use {@see Utility::tag_get_delimiter()} method instead
 * @return array|mixed
 */
function tag_get_delimiter()
{
    $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
    trigger_error(__FUNCTION__ . " is deprecated, called from {$trace[0]['file']} line {$trace[0]['line']}");
    $GLOBALS['xoopsLogger']->addDeprecated(
        'Tag Module: ' . __FUNCTION__ . " function is deprecated since Tag 2.3.4, please use 'Tag\Utility::" . __FUNCTION__ . ' method instead.'
        . " Called from {$trace[0]['file']}line {$trace[0]['line']}");

    xoops_loadLanguage('config', 'tag');
    $retVal = [',', ' ', '|', ';'];

    if (!empty($GLOBALS['tag_delimiter'])) {
        $retVal = $GLOBALS['tag_delimiter'];
    } else {
        $moduleConfig = tag_load_config();
        if (!empty($GLOBALS['moduleConfig']['tag_delimiter'])) {
            $retVal = $GLOBALS['moduleConfig']['tag_delimiter'];
        }
    }

    return $retVal;
}
