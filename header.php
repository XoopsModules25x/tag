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

require_once \dirname(__DIR__, 2) . '/mainfile.php';
require_once __DIR__ . '/include/vars.php';
require_once __DIR__ . '/include/common.php';
//require_once __DIR__ . '/include/functions.php';

/**
 * {@internal defined in ./include/common.php }}
 * @var XoopsModules\Tag\Helper $helper
 */
// Load language files
$helper->loadLanguage('main');

//$GLOBALS['xoopsOption']['xoops_module_header'] = "<link rel='stylesheet' type='text/css' href='" . $helper->url('assets/css/style.css') . "' >";
$myts = \MyTextSanitizer::getInstance();
