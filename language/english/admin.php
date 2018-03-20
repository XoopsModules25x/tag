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
 * @copyright      {@link http://sourceforge.net/projects/xoops/ The XOOPS Project}
 * @license        {@link http://www.fsf.org/copyleft/gpl.html GNU public license}
 * @author         Taiwen Jiang (phppp or D.J.) <php_pp@hotmail.com>
 * @since          1.00
 */

defined('XOOPS_ROOT_PATH') || die('Restricted access');
define('_AM_TAG_TERM', 'Tag');

define('_AM_TAG_STATS', 'Statistic Infomation');
define('_AM_TAG_COUNT_TAG', 'Tag count: %s');
define('_AM_TAG_COUNT_ITEM', 'Item count: %s');
define('_AM_TAG_COUNT_MODULE', 'Module:');
define('_AM_TAG_COUNT_MODULE_TITLE', 'Item Count / Tag Count');

define('_AM_TAG_EDIT', 'Tag Admin');
define('_AM_TAG_SYNCHRONIZATION', 'Synchronize');

define('_AM_TAG_ACTIVE', 'Active');
define('_AM_TAG_INACTIVE', 'Inactive');
define('_AM_TAG_GLOBAL', 'Global');
define('_AM_TAG_ALL', 'All modules');
define('_AM_TAG_NUM', 'Number for each time');
define('_AM_TAG_IN_PROCESS', 'Data synchronization is in process, please wait for a while ...');
define('_AM_TAG_FINISHED', 'Data synchronization is finished.');

//2.31
// index.php
/*
define('_MI_TAG_ADMIN_INDEX', "Index");
define('_MI_TAG_ADMIN_HOME', "Home");
define('_MI_AM_TAG_ADMIN_HOME_DESC', "Go back to Administration module");
define('_MI_TAG_ADMIN_ABOUT', "About");
define('_MI_TAG_ADMIN_HELP_DESC', "About this module");
define('_MI_TAG_HELP_DESC', "Module help");
//define('_MI_TAG_ADMIN_HELP', "Help");
*/
//2.32
define('_AM_TAG_INDEX_TPL_DESC', 'Index page of tag module');
define('_AM_TAG_INDEX_TPL_LIST_DESC', 'List of tags');
define('_AM_TAG_INDEX_TPL_VIEW_DESC', 'Links of a tag');
define('_AM_TAG_INDEX_TPL_BAR_DESC', 'Tag list in an item');
define('_AM_TAG_INDEX_ADMINTPL_ABOUT_DESC', '');
define('_AM_TAG_INDEX_ADMINTPL_HELP_DESC', '');

// Text for Admin footer
define('_AM_TAG_MAINTAINED_BY', 'XOOPS Tag is maintained by the');
define('_AM_TAG_MAINTAINED_TITLE', 'Visit XOOPS Community');
define('_AM_TAG_MAINTAINED_TEXT', 'XOOPS Community');

// About.php
define('_AM_TAG_ABOUT_RELEASEDATE', 'Released: ');
define('_AM_TAG_ABOUT_UPDATEDATE', 'Updated: ');
define('_AM_TAG_ABOUT_AUTHOR', 'Author: ');
define('_AM_TAG_ABOUT_CREDITS', 'Credits: ');
define('_AM_TAG_ABOUT_LICENSE', 'License: ');
define('_AM_TAG_ABOUT_MODULE_STATUS', 'Status: ');
define('_AM_TAG_ABOUT_WEBSITE', 'Website: ');
define('_AM_TAG_ABOUT_AUTHOR_NAME', 'Author name: ');
define('_AM_TAG_ABOUT_CHANGELOG', 'Change Log');
define('_AM_TAG_ABOUT_MODULE_INFO', 'Module Infos');
define('_AM_TAG_ABOUT_AUTHOR_INFO', 'Author Infos');
define('_AM_TAG_ABOUT_DESCRIPTION', 'Description: ');

// text in admin footer
define('_AM_TAG_ADMIN_FOOTER', "<div class='right smallsmall italic pad5'><b>" . $GLOBALS['xoopsModule']->getVar('name') . "</b> is maintained by the <a class='tooltip' rel='external' href='https://xoops.org/' title='Visit XOOPS Community'>XOOPS Community</a></div>");

// Text for Admin footer
define('_AM_TAG_FOOTER', "<div class='center smallsmall italic pad5'>Tag Module is maintained by the <a class='tooltip' rel='external' href='https://xoops.org/' title='Visit XOOPS Community'>XOOPS Community</a></div>");

//2.32
define('_AM_TAG_DB_UPDATED', 'Database Updated Successfully');

//2.33
define('_AM_TAG_UPGRADEFAILED0', "Update failed - couldn't rename field '%s'");
define('_AM_TAG_UPGRADEFAILED1', "Update failed - couldn't add new fields");
define('_AM_TAG_UPGRADEFAILED2', "Update failed - couldn't rename table '%s'");
define('_AM_TAG_ERROR_COLUMN', 'Could not create column in database : %s');
define('_AM_TAG_ERROR_BAD_XOOPS', 'This module requires XOOPS %s+ (%s installed)');
define('_AM_TAG_ERROR_BAD_PHP', 'This module requires PHP version %s+ (%s installed)');
define('_AM_TAG_ERROR_TAG_REMOVAL', 'Could not remove tags from Tag Module');
