<?php
/**
 * Tag management for XOOPS
 *
 * @copyright	The XOOPS project http://www.xoops.org/
 * @license		http://www.fsf.org/copyleft/gpl.html GNU public license
 * @author		Taiwen Jiang (phppp or D.J.) <php_pp@hotmail.com>
 * @since		1.00
 * @version		$Id: modinfo.php 10505 2012-12-23 03:33:54Z beckmi $
 * @package		tag
 */

if (!defined('XOOPS_ROOT_PATH')) { exit(); }

define('TAG_MI_NAME','XOOPS TAG');
define('TAG_MI_DESC','For site-wide Tag management');

define('TAG_MI_BLOCK_CLOUD','Tag Cloud');
define('TAG_MI_BLOCK_CLOUD_DESC','');
define('TAG_MI_BLOCK_TOP','Top Tag');
define('TAG_MI_BLOCK_TOP_DESC','');

define('TAG_MI_DOURLREWRITE','Enable URL rewrite');
define('TAG_MI_DOURLREWRITE_DESC','AcceptPathInfo On for Apache2 is required');

define('TAG_MI_ITEMSPERPAGE','Items per page');
define('TAG_MI_ITEMSPERPAGE_DESC','');

define('TAG_MI_ADMENU_INDEX','Home');
define('TAG_MI_ADMENU_EDIT','Tag Admin');
define('TAG_MI_ADMENU_SYNCHRONIZATION','Synchronize');

//2.31
// index.php

define('_TAG_ADMIN_INDEX','Index');
define('_TAG_ADMIN_HOME','Home');
define('_TAG_ADMIN_HOME_DESC','Go back to Administration module');
define('_TAG_ADMIN_ABOUT' , 'About');
define('_TAG_ADMIN_ABOUT_DESC' , 'About this module');
define('_TAG_ADMIN_HELP' , 'Help');
define('_TAG_ADMIN_HELP_DESC' , 'Module help');
define('_AM_TAG_ADMIN_HELP' , 'Help');

?>