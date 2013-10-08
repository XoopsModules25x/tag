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
 * @version         $Id: plugin.php 8164 2011-11-06 22:36:42Z beckmi $
 * @package         tag
 */

// plugin guide:
/* 
 * Add customized configs, variables or functions
 */
$customConfig = array();

/* 
 * Due to the difference of word boundary for different languages, delimiters also depend on languages
 * You need specify all possbile deimiters:
 * IF $GLOBALS["tag_delimiter"] IS SET IN /modules/tag/language/mylanguage/main.php, TAKE IT
 * ELSE IF $customConfig["tag_delimiter"] IS SET BELOW, TAKE IT
 * ELSE TAKE (",", ";", " ", "|")
 *
 * Tips:
 * For English sites, you can set as array(",", ";", " ", "|")
 * For Chinese sites, you can set as array(",", ";", " ", "|", "гм")
 *
 * TODO: there shall be an option for admin to choose a category to store subcategories and articles
 */
$customConfig["tag_delimiter"] = array(",", " ", "|", ";");

$customConfig["limit_tag"]  = 100;
$customConfig["font_max"]   = 150;
$customConfig["font_min"]   = 80;

return $customConfig;
?>