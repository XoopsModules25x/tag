<?php

namespace XoopsModules\Tag;

use XoopsModules\Tag;
use XoopsModules\Tag\Common;
use XoopsModules\Tag\Constants;

/**
 * Class Utility
 */
class Utility extends Common\SysUtility
{
    //--------------- Custom module methods -----------------------------
    /**
     * Load Tag module configs
     *
     * @return array|mixed|null
     */
    public static function tag_load_config()
    {
        static $moduleConfig;

        if (null === $moduleConfig) {
            $moduleConfig = [];
            $helper       = \XoopsModules\Tag\Helper::getInstance();
            if ($GLOBALS['xoopsModule'] instanceof \XoopsModule
                && !empty($GLOBALS['xoopsModuleConfig'])
                && ($helper->getDirname() === $GLOBALS['xoopsModule']->getVar('dirname', 'n'))) {
                $moduleConfig = $GLOBALS['xoopsModuleConfig'];
            } else {
                /** @var \XoopsConfigHandler $configHandler */
                $mid           = $helper->getModule()->getVar('mid');
                $configHandler = \xoops_getHandler('config');

                $criteria = new \Criteria('conf_modid', $mid);
                $configs  = $configHandler->getConfigs($criteria);
                /** @var \XoopsConfigItem $obj */
                foreach ($configs as $obj) {
                    $moduleConfig[$obj->getVar('conf_name')] = $obj->getConfValueForOutput();
                }
                unset($configs, $criteria);
            }
            if (\is_file($helper->path('include/plugin.php'))) {
                $customConfig = require $helper->path('include/plugin.php');
                $moduleConfig = \array_merge($moduleConfig, $customConfig);
            }
        }

        return $moduleConfig;
    }

    /**
     * Define URL_DELIMITER Constant
     *
     * return void
     */
    public static function tag_define_url_delimiter()
    {
        if (\defined('URL_DELIMITER')) {
            if (!\in_array(URL_DELIMITER, ['?', '/'], true)) {
                exit('Security Violation');
            }
        } else {
            $moduleConfig = self::tag_load_config();
            if (empty($moduleConfig['do_urw'])) {
                \define('URL_DELIMITER', '?');
            } else {
                \define('URL_DELIMITER', '/');
            }
        }
    }

    /**
     * Get the tag delimiter
     *
     * @return array|mixed
     */
    public static function tag_get_delimiter()
    {
        \XoopsModules\Tag\Helper::getInstance()->loadLanguage('config');
        //xoops_loadLanguage('config', 'tag');
        $retVal = [',', ' ', '|', ';'];

        if (!empty($GLOBALS['tag_delimiter'])) {
            $retVal = $GLOBALS['tag_delimiter'];
        } else {
            $moduleConfig = self::tag_load_config();
            if (!empty($GLOBALS['moduleConfig']['tag_delimiter'])) {
                $retVal = $GLOBALS['moduleConfig']['tag_delimiter'];
            }
        }

        return $retVal;
    }
    /**
     * Function to parse arguments for a page according to $_SERVER['REQUEST_URI']
     *
     *
     * @param mixed $args        array of indexed variables: name and value pass-by-reference
     * @param mixed $args_string array of string variable values pass-by-reference
     * @return bool true on args parsed
     */

    /* known issues:
     * - "/" in a string
     * - "&" in a string
     */
    public static function tag_parse_args(&$args, &$args_string)
    {
        $args_abb    = [
            'c' => 'catid',
            'm' => 'modid',
            's' => 'start',
            't' => 'tag',
        ];
        $args        = [];
        $args_string = [];
        if (\preg_match('/[^\?]*\.php[\/|\?]([^\?]*)/i', $_SERVER['REQUEST_URI'], $matches)) {
            $vars = \preg_split('/[\/|&]/', $matches[1]);
            $vars = \array_map('\trim', $vars);
            foreach ($vars as $var) {
                if (\is_numeric($var)) {
                    $args_string[] = $var;
                } elseif (false === mb_strpos($var, '=')) {
                    if (\is_numeric(mb_substr($var, 1))) {
                        $args[$args_abb[mb_strtolower($var[0])]] = (int)mb_substr($var, 1);
                    } else {
                        $args_string[] = \urldecode($var);
                    }
                } else {
                    \parse_str($var, $args);
                }
            }
        }

        return (0 == \count($args) + \count($args_string)) ? false : true;
    }

    /**
     * Function to parse tags(keywords) upon defined delimiters
     *
     * @param string $text_tag text to be parsed
     *
     * @return array containing parsed tags
     */
    public static function tag_parse_tag($text_tag)
    {
        $tags = [];
        if (!empty($text_tag)) {
            $delimiters = self::tag_get_delimiter();
            $tags_raw   = \explode(',', \str_replace($delimiters, ',', $text_tag));
            $tags       = \array_filter(\array_map('\trim', $tags_raw)); // removes all array elements === false
        }
        return $tags;
    }
}
