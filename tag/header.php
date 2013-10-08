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
 * @version         $Id: header.php 10505 2012-12-23 03:33:54Z beckmi $
 * @package         tag
 */

include_once '../../mainfile.php';
include dirname(__FILE__) . "/include/vars.php";
include_once dirname(__FILE__) . "/include/functions.php";

$xoopsOption["xoops_module_header"] = '<link rel="stylesheet" type="text/css" href="' . XOOPS_URL . '/modules/tag/css/style.css" />';
$myts =& MyTextSanitizer::getInstance();
?>