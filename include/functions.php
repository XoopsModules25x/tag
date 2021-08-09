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

use XoopsModules\Tag;
use XoopsModules\Tag\Helper;
use XoopsModules\Tag\Utility;

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

if (!defined('TAG_FUNCTIONS')) {
    define('TAG_FUNCTIONS', 1);

    require_once $GLOBALS['xoops']->path('modules/tag/include/vars.php');

    /**
     * @return XoopsModules\Tag\TagHandler
     * @deprecated - use Tag\Helper::getInstance()->getHandler('Tag') instead
     * @todo       Figure out what, if anything, calls this
     */
    function tag_getTagHandler()
    {
        /** @var \XoopsModuleHandler $moduleHandler */
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
        trigger_error(__FUNCTION__ . " is deprecated, called from {$trace[0]['file']} line {$trace[0]['line']}");
        $GLOBALS['xoopsLogger']->addDeprecated(
            'Tag Module: ' . __FUNCTION__ . " function is deprecated since Tag 2.3.4, please use 'Tag\Helper::getInstance()->getHandler('Tag')' method instead." . " Called from {$trace[0]['file']}line {$trace[0]['line']}"
        );

        if (!class_exists(Helper::class)) {
            return false;
        }

        $tagHandler = Helper::getInstance()->getHandler('Tag');

        /*
        static $tagHandler;

        if (isset($tagHandler)) {
            return $tagHandler;
        }
        $tagHandler = null;
        if (!($GLOBALS['xoopsModule'] instanceof \XoopsModule)
            || ('tag' !== $GLOBALS['xoopsModule']->getVar('dirname'))) {
            // @var \XoopsModuleHandler $moduleHandler

        $moduleHandler = xoops_getHandler('module');
        $module        = $moduleHandler->getByDirname('tag');
        if (!$module || !$module->isactive()) {
            return $tagHandler;
        }
    }
    */

        return $tagHandler;
    }

    /**
     * Function to parse arguments for a page according to $_SERVER['REQUEST_URI']
     *
     * @param mixed $args        array of indexed variables: name and value pass-by-reference
     * @param mixed $args_string array of string variable values pass-by-reference
     * @return bool true on args parsed
     * @deprecated - use {@see Utility::tag_parse_arg()} method instead
     *
     */

    /* known issues:
     * - "/" in a string
     * - "&" in a string
    */
    function tag_parse_args(&$args, &$args_string)
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
        trigger_error(__FUNCTION__ . " is deprecated, called from {$trace[0]['file']} line {$trace[0]['line']}");
        $GLOBALS['xoopsLogger']->addDeprecated(
            'Tag Module: ' . __FUNCTION__ . " function is deprecated since Tag 2.3.4, please use 'Tag\Utility::tag_parse_tag()' method instead." . " Called from {$trace[0]['file']}line {$trace[0]['line']}"
        );

        return Utility::tag_parse_args($args, $args_string);
        /*
        $args_abb     = [
            'c' => 'catid',
            'm' => 'modid',
            's' => 'start',
            't' => 'tag',
        ];
        $args         = [];
        $args_string  = [];
        if (preg_match("/[^\?]*\.php[\/|\?]([^\?]*)/i", $_SERVER['REQUEST_URI'], $matches)) {
            $vars = preg_split("/[\/|&]/", $matches[1]);
            $vars = array_map('trim', $vars);
            foreach ($vars as $var) {
                if (is_numeric($var)) {
                    $args_string[] = $var;
                } elseif (false === mb_strpos($var, '=')) {
                    if (is_numeric(mb_substr($var, 1))) {
                        $args[$args_abb[mb_strtolower($var[0])]] = (int)mb_substr($var, 1);
                    } else {
                        $args_string[] = urldecode($var);
                    }
                } else {
                    parse_str($var, $args);
                }
            }
        }

        return (0 == count($args) + count($args_string)) ? false : true;
        */
    }

    /*
     * Function to parse tags(keywords) upon defined delimiters
     *
     * @param string $text_tag text to be parsed
     *
     * @return array containing parsed tags
     * @deprecated - use {@see Utility::tag_parse_tag()} method instead
     *             {@internal keep this file/function since it is called by 'unknown' plugins }}
     */
    function tag_parse_tag($text_tag)
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
        trigger_error(__FUNCTION__ . " is deprecated, called from {$trace[0]['file']} line {$trace[0]['line']}");
        $GLOBALS['xoopsLogger']->addDeprecated(
            'Tag Module: ' . __FUNCTION__ . " function is deprecated since Tag 2.3.4, please use 'Tag\Utility::tag_parse_tag()' method instead." . " Called from {$trace[0]['file']}line {$trace[0]['line']}"
        );

        return Utility::tag_parse_tag($text_tag);
        /*
        $tags = [];
        if (empty($text_tag)) {
            return $tags;
        }

        $delimiters = tag_get_delimiter();
        $tags_raw   = explode(',', str_replace($delimiters, ',', $text_tag));
        $tags       = array_filter(array_map('trim', $tags_raw));

        return $tags;
        */
    }
}
