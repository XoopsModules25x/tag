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
 * @package         tag
 * @copyright       {@link http://sourceforge.net/projects/xoops/ The XOOPS Project}
 * @license         {@link http://www.fsf.org/copyleft/gpl.html GNU public license}
 * @author          Taiwen Jiang <phppp@users.sourceforge.net>
 * @since           1.00
 */

use XoopsModules\Tag;

defined('XOOPS_ROOT_PATH') || die('Restricted access');

if (!defined('TAG_FUNCTIONS')):
    define('TAG_FUNCTIONS', 1);

    include $GLOBALS['xoops']->path('modules/tag/include/vars.php');

    /**
     * @return bool|null
     */
    function tag_getTagHandler()
    {
        static $tagHandler;

        if (isset($tagHandler)) {
            return $tagHandler;
        }

        $tagHandler = null;
        if (!($GLOBALS['xoopsModule'] instanceof XoopsModule)
            || ('tag' !== $GLOBALS['xoopsModule']->getVar('dirname'))) {
            /** @var \XoopsModuleHandler $moduleHandler */
            $moduleHandler = xoops_getHandler('module');
            $module        = $moduleHandler->getByDirname('tag');
            if (!$module || !$module->isactive()) {
                return $tagHandler;
            }
        }
        $tagHandler = @xoops_getModuleHandler('tag', 'tag', true);

        return $tagHandler;
    }

    /**
     * Function to parse arguments for a page according to $_SERVER['REQUEST_URI']
     *
     * @var array $args_numeric array of numeric variable values
     * @var array $args         array of indexed variables: name and value
     * @var array $args_string  array of string variable values
     *
     * @return bool    true on args parsed
     */

    /* known issues:
     * - "/" in a string
     * - "&" in a string
    */
    function tag_parse_args(&$args_numeric, &$args, &$args_string)
    {
        $args_abb     = [
            'c' => 'catid',
            'm' => 'modid',
            's' => 'start',
            't' => 'tag'
        ];
        $args         = [];
        $args_numeric = [];
        $args_string  = [];
        if (preg_match("/[^\?]*\.php[\/|\?]([^\?]*)/i", $_SERVER['REQUEST_URI'], $matches)) {
            $vars = preg_split("/[\/|&]/", $matches[1]);
            $vars = array_map('trim', $vars);
            if (count($vars) > 0) {
                foreach ($vars as $var) {
                    if (is_numeric($var)) {
                        //$args_numeric[] = $var;
                        $args_string[] = $var;
                    } elseif (false === strpos($var, '=')) {
                        if (is_numeric(substr($var, 1))) {
                            $args[$args_abb[mb_strtolower($var{0})]] = (int)substr($var, 1);
                        } else {
                            $args_string[] = urldecode($var);
                        }
                    } else {
                        parse_str($var, $args);
                    }
                }
            }
        }

        return (0 == count($args) + count($args_numeric) + count($args_string)) ? null : true;
    }

    /**
     * Function to parse tags(keywords) upon defined delimiters
     *
     * @param string $text_tag text to be parsed
     *
     * @return array containing parsed tags
     */
    function tag_parse_tag($text_tag)
    {
        $tags = [];
        if (empty($text_tag)) {
            return $tags;
        }

        $delimiters = tag_get_delimiter();
        $tags_raw   = explode(',', str_replace($delimiters, ',', $text_tag));
        $tags       = array_filter(array_map('trim', $tags_raw));

        return $tags;
    }

endif;
