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
 * @copyright      {@link https://sourceforge.net/projects/xoops/ The XOOPS Project}
 * @license        {@link https://www.fsf.org/copyleft/gpl.html GNU public license}
 * @author         Taiwen Jiang <phppp@users.sourceforge.net>
 * @since          1.00
 */

use Xmf\Module\Admin;
use XoopsModules\Tag\{
    Helper,
    LinkHandler,
    TagHandler,
    Utility
};

/** @var Helper $helper */
/** @var TagHandler $tagHandler */
/** @var LinkHandler $linkHandler */
/** @var Admin $adminObject */

require_once __DIR__ . '/admin_header.php';
require_once $helper->path('include/vars.php');

xoops_cp_header();

$adminObject = Admin::getInstance();

$tagHandler = $helper->getHandler('Tag');
$count_tag  = $tagHandler->getCount();

$linkHandler = $helper->getHandler('Link');
$count_item  = $linkHandler->getCount();

$sql           = 'SELECT tag_modid, SUM(tag_count) AS count_item, COUNT(DISTINCT tag_id) AS count_tag';
$sql           .= ' FROM ' . $GLOBALS['xoopsDB']->prefix('tag_stats');
$sql           .= ' GROUP BY tag_modid';
$counts_module = [];

$result = $GLOBALS['xoopsDB']->query($sql);
if ($result instanceof \mysqli_result) {
    while (false !== ($myrow = $GLOBALS['xoopsDB']->fetchArray($result))) {
        $counts_module[$myrow['tag_modid']] = [
            'count_item' => $myrow['count_item'],
            'count_tag'  => $myrow['count_tag'],
        ];
    }
    if (!empty($counts_module)) {
        /** @var \XoopsModuleHandler $moduleHandler */
        $moduleHandler = xoops_getHandler('module');
        $module_list   = $moduleHandler->getList(new \Criteria('mid', '(' . implode(', ', array_keys($counts_module)) . ')', 'IN'));
    }
} else {
    \trigger_error($GLOBALS['xoopsDB']->error());
}

$adminObject->addInfoBox(_AM_TAG_STATS);
$adminObject->addInfoBoxLine(sprintf('<infolabel>' . _AM_TAG_COUNT_TAG . '</infolabel>', $count_tag));
$adminObject->addInfoBoxLine(sprintf('<infolabel>' . _AM_TAG_COUNT_ITEM . '</infolabel>', $count_item . '<br><br>'));
$adminObject->addInfoBoxLine('<infolabel>' . _AM_TAG_COUNT_MODULE . '</infolabel>' . '<infotext>' . _AM_TAG_COUNT_MODULE_TITLE . '</infotext>');

foreach ($counts_module as $module => $count) {
    $moduleStat = "<infolabel>{$module_list[$module]}:</infolabel>\n" . "<infotext>{$count['count_tag']} / {$count['count_item']}\n" . "  [<a href='" . $helper->url("admin/admin.tag.php?modid={$module}") . "'>" . _AM_TAG_EDIT . "</a>]\n" . "  [<a href='" . $helper->url(
            "admin/syn.tag.php?modid={$module}"
        ) . "'>" . _AM_TAG_SYNCHRONIZATION . "</a>]\n" . "</infotext> \n";
    $adminObject->addInfoBoxLine($moduleStat);
}

if (empty($counts_module)) {  // there aren't any so just display "none"
    $moduleStat = "<infolabel>%s</infolabel><infotext>0 / 0</infotext> \n";
    $adminObject->addInfoBoxLine(sprintf($moduleStat, _NONE));
}

$adminObject->displayNavigation(basename(__FILE__));
$adminObject->displayIndex();

echo Utility::getServerStats();

require_once __DIR__ . '/admin_footer.php';
