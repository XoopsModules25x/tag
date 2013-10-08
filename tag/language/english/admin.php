<?php
/**
 * Tag management for XOOPS
 *
 * @copyright	The XOOPS project http://www.xoops.org/
 * @license		http://www.fsf.org/copyleft/gpl.html GNU public license
 * @author		Taiwen Jiang (phppp or D.J.) <php_pp@hotmail.com>
 * @since		1.00
 * @version		$Id: admin.php 11906 2013-08-14 05:54:12Z beckmi $
 * @package		tag
 */

if (!defined('XOOPS_ROOT_PATH')) { exit(); }
define('TAG_AM_TERM','Tag');

define('TAG_AM_STATS','Statistic Infomation');
define('TAG_AM_COUNT_TAG','Tag count: %s');
define('TAG_AM_COUNT_ITEM','Item count: %s');
define('TAG_AM_COUNT_MODULE','Module count: %s');
define('TAG_AM_COUNT_MODULE_TITLE','Tag Count / Module Count');

define('TAG_AM_EDIT','Tag Admin');
define('TAG_AM_SYNCHRONIZATION','Synchronize');

define('TAG_AM_ACTIVE','Active');
define('TAG_AM_INACTIVE','Inactive');
define('TAG_AM_GLOBAL','Global');
define('TAG_AM_ALL','All modules');
define('TAG_AM_NUM','Number for each time');
define('TAG_AM_IN_PROCESS','Data synchronization is in process, please wait for a while ...');
define('TAG_AM_FINISHED','Data synchronization is finished.');

//2.3.1

// About.php
define('_AM_TAG_ABOUT_RELEASEDATE',        'Released: ');
define('_AM_TAG_ABOUT_UPDATEDATE',               'Updated: ');
define('_AM_TAG_ABOUT_AUTHOR',                   'Author: ');
define('_AM_TAG_ABOUT_CREDITS',                  'Credits: ');
define('_AM_TAG_ABOUT_LICENSE',                  'License: ');
define('_AM_TAG_ABOUT_MODULE_STATUS',            'Status: ');
define('_AM_TAG_ABOUT_WEBSITE',                  'Website: ');
define('_AM_TAG_ABOUT_AUTHOR_NAME',              'Author name: ');
define('_AM_TAG_ABOUT_CHANGELOG',                'Change Log');
define('_AM_TAG_ABOUT_MODULE_INFO',              'Module Infos');
define('_AM_TAG_ABOUT_AUTHOR_INFO',              'Author Infos');
define('_AM_TAG_ABOUT_DESCRIPTION',          'Description: ');

// text in admin footer
define('_AM_TAG_ADMIN_FOOTER',                 "<div class='right smallsmall italic pad5'><b>" . $xoopsModule->getVar("name") . "</b> is maintained by the <a class='tooltip' rel='external' href='http://xoops.org/' title='Visit XOOPS Community'>XOOPS Community</a></div>");

//ModuleAdmin
define('_AM_MODULEADMIN_MISSING','Error: The ModuleAdmin class is missing. Please install the ModuleAdmin Class (see /docs/readme.txt)');

//define('_AM_TAG_MISSING','Error: The ModuleAdmin class is missing. Please install the ModuleAdmin Class into /Frameworks (see /docs/readme.txt)');

// Text for Admin footer
define("_AM_TAG_FOOTER","<div class='center smallsmall italic pad5'>Tag Module is maintained by the <a class='tooltip' rel='external' href='http://xoops.org/' title='Visit XOOPS Community'>XOOPS Community</a></div>");

//2.32
define('_AM_TAG_DB_UPDATED', 'Database Updated Successfully');

