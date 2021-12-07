<?php declare(strict_types=1);
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
 * @copyright       {@link https://sourceforge.net/projects/xoops/ The XOOPS Project}
 * @license         {@link https://www.fsf.org/copyleft/gpl.html GNU public license}
 * @author          Taiwen Jiang <phppp@users.sourceforge.net>
 * @since           1.00
 */

use Xmf\Module\Admin;
use XoopsModules\Tag\{
    Helper
};

$moduleDirName      = \basename(\dirname(__DIR__));
$moduleDirNameUpper = \mb_strtoupper($moduleDirName);

$helper = Helper::getInstance();
$helper->loadLanguage('common');
$helper->loadLanguage('modinfo');
$helper->loadLanguage('common');

$pathIcon32    = Admin::menuIconPath('');
$pathModIcon32 = XOOPS_URL . '/modules/' . $moduleDirName . '/assets/images/icons/32/';
if (is_object($helper->getModule()) && false !== $helper->getModule()->getInfo('modicons32')) {
    $pathModIcon32 = $helper->url($helper->getModule()->getInfo('modicons32'));
}

$adminmenu[] = [
    'title' => _MI_TAG_ADMENU_INDEX,
    'link'  => 'admin/index.php',
    'desc'  => _MI_TAG_ADMIN_HOME_DESC,
    'icon'  => $pathIcon32 . '/home.png',
];

$adminmenu[] = [
    'title' => _MI_TAG_ADMENU_EDIT,
    'link'  => 'admin/admin.tag.php',
    'desc'  => _MI_TAG_ADMENU_EDIT_DESC,
    'icon'  => $pathIcon32 . '/administration.png',
];

$adminmenu[] = [
    'title' => _MI_TAG_ADMENU_SYNCHRONIZATION,
    'link'  => 'admin/syn.tag.php',
    'desc'  => _MI_TAG_HELP_DESC,
    'icon'  => $pathIcon32 . '/synchronized.png',
];

// Blocks Admin
$adminmenu[] = [
    'title' => constant('CO_' . $moduleDirNameUpper . '_' . 'BLOCKS'),
    'link' => 'admin/blocksadmin.php',
    'icon' => $pathIcon32 . '/block.png',
];


$adminmenu[] = [
    'title' => _MI_TAG_ADMIN_ABOUT,
    'link'  => 'admin/about.php',
    'desc'  => _MI_TAG_ADMIN_HELP_DESC,
    'icon'  => $pathIcon32 . '/about.png',
];
