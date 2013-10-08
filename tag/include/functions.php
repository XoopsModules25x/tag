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
 * @version         $Id: functions.php 10505 2012-12-23 03:33:54Z beckmi $
 * @package         tag
 */

if (!defined('XOOPS_ROOT_PATH')) { exit(); }

if (!defined("TAG_FUNCTIONS")):
define("TAG_FUNCTIONS",1);

include XOOPS_ROOT_PATH . "/modules/tag/include/vars.php";

function &tag_getTagHandler()
{
    static $tag_handler;
    if (isset($tag_handler)) return $tag_handler;
    
    $tag_handler = null;
    if (!is_object($GLOBALS["xoopsModule"]) || "tag" != $GLOBALS["xoopsModule"]->getVar("dirname")) {
        $module_handler =& xoops_gethandler('module');
        $module =& $module_handler->getByDirname("tag");
        if (!$module || !$module->getVar("isactive")) {
            return $tag_handler;
        }
    }
    $tag_handler = @xoops_getmodulehandler("tag", "tag", true);
    return $tag_handler;
}

/**
 * Function to parse arguments for a page according to $_SERVER['REQUEST_URI']
 * 
 * @var array $args_numeric    array of numeric variable values
 * @var array $args            array of indexed variables: name and value
 * @var array $args_string    array of string variable values
 *
 * @return bool    true on args parsed
 */

/* known issues:
 * - "/" in a string 
 * - "&" in a string 
*/
function tag_parse_args(&$args_numeric, &$args, &$args_string)
{
    $args_abb = array(
                        "c"    => "catid", 
                        "m"    => "modid", 
                        "s"    => "start", 
                        "t"    => "tag", 
                        );
    $args = array();
    $args_numeric = array();
    $args_string = array();
    if (preg_match("/[^\?]*\.php[\/|\?]([^\?]*)/i", $_SERVER['REQUEST_URI'], $matches)) {
        $vars = preg_split("/[\/|&]/", $matches[1]);
        $vars = array_map("trim", $vars);
        if (count($vars) > 0) {
            foreach ($vars as $var) {
                if (is_numeric($var)) {
                    //$args_numeric[] = $var;
                    $args_string[] = $var;
                } elseif (false === strpos($var, "=")) {
                    if (is_numeric(substr($var, 1))) {
                        $args[$args_abb[strtolower($var{0})]] = intval(substr($var, 1));
                    } else {
                        $args_string[] = urldecode($var);
                    }
                } else {
                    parse_str($var, $args);
                }
            }
        }
    }
    return (count($args) + count($args_numeric) + count($args_string) == 0) ? null : true;
}

/**
 * Function to parse tags(keywords) upon defined delimiters
 * 
 * @var        string    $text_tag    text to be parsed
 *
 * @return    array    tags
 */
function tag_parse_tag($text_tag)
{
    $tags = array();
    if (empty($text_tag)) return $tags;
    
    $delimiters = tag_get_delimiter();
    $tags_raw = explode(",", str_replace($delimiters, ",", $text_tag));
    $tags = array_filter(array_map("trim", $tags_raw));
    
    return $tags;
}

endif;