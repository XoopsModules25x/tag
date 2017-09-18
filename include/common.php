<?php
/*
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * @copyright    XOOPS Project https://xoops.org/
 * @license      GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package
 * @since
 * @author     XOOPS Development Team
 */

if (!defined('TAG_MODULE_PATH')) {
    define('TAG_DIRNAME', basename(dirname(__DIR__)));
    define('TAG_URL', XOOPS_URL . '/modules/' . TAG_DIRNAME);
    define('TAG_IMAGE_URL', TAG_URL . '/assets/images/');
    define('TAG_ROOT_PATH', XOOPS_ROOT_PATH . '/modules/' . TAG_DIRNAME);
    define('TAG_IMAGE_PATH', TAG_ROOT_PATH . '/assets/images');
    define('TAG_ADMIN_URL', TAG_URL . '/admin/');
    define('TAG_UPLOAD_URL', XOOPS_UPLOAD_URL . '/' . TAG_DIRNAME);
    define('TAG_UPLOAD_PATH', XOOPS_UPLOAD_PATH . '/' . TAG_DIRNAME);
}
xoops_loadLanguage('common', TAG_DIRNAME);

require_once TAG_ROOT_PATH . '/include/functions.php';
//require_once TAG_ROOT_PATH . '/include/constants.php';
//require_once TAG_ROOT_PATH . '/include/seo_functions.php';
//require_once TAG_ROOT_PATH . '/class/metagen.php';
//require_once TAG_ROOT_PATH . '/class/session.php';
//require_once TAG_ROOT_PATH . '/class/xoalbum.php';
//require_once TAG_ROOT_PATH . '/class/request.php';

require_once TAG_ROOT_PATH . '/class/helper.php';
//require_once PUBLISHER_ROOT_PATH . '/class/request.php';

// module information
$mod_copyright = "<a href='https://xoops.org' title='XOOPS Project' target='_blank'>
                     <img src='" . PUBLISHER_AUTHOR_LOGOIMG . "' alt='XOOPS Project'></a>";

xoops_loadLanguage('common', PUBLISHER_DIRNAME);

xoops_load('constants', PUBLISHER_DIRNAME);
xoops_load('utility', PUBLISHER_DIRNAME);

$debug     = false;
$tag = Tag::getInstance($debug);

//This is needed or it will not work in blocks.
global $tagIsAdmin;

// Load only if module is installed
if (is_object($tag->getModule())) {
    // Find if the user is admin of the module
    $tagIsAdmin = TagUtility::userIsAdmin();
}
