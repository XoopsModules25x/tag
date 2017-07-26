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

if (!defined('TAG_INI')) {
    define('TAG_INI', 1);
}

//require_once $GLOBALS['xoops']->path("/Frameworks/art/functions.ini.php");
require_once $GLOBALS['xoops']->path('/modules/tag/include/functions.ini.php');

// include customized variables
if (($GLOBALS['xoopsModule'] instanceof XoopsModule) && ('tag' === $GLOBALS['xoopsModule']->getVar('dirname', 'n'))
    && $GLOBALS['xoopsModule']->isactive()) {
    $GLOBALS['xoopsModuleConfig'] = tag_load_config();
}

//load_object();
