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
 * @package         XoopsModules/Tag
 * @copyright       {@link http://sourceforge.net/projects/xoops/ The XOOPS Project}
 * @license         {@link http://www.fsf.org/copyleft/gpl.html GNU public license}
 * @author          Taiwen Jiang <phppp@users.sourceforge.net>
 * @since           1.00
 */

use Xmf\Request;
use XoopsModules\Tag;
use XoopsModules\Tag\Constants;

require_once __DIR__ . '/admin_header.php';
require_once $GLOBALS['xoops']->path('/class/xoopsformloader.php');
require_once $helper->path('include/vars.php');

xoops_cp_header();

/** @var \Xmf\Module\Admin $adminObject */
$adminObject->displayNavigation(basename(__FILE__));

$modid = Request::getInt('modid', Constants::DEFAULT_ID);
$start = Request::getInt('start', Constants::BEGINNING);
$limit = Request::getInt('limit', Constants::DEFAULT_LIMIT);

$sql           = 'SELECT tag_modid, COUNT(DISTINCT tag_id) AS count_tag' . ' FROM ' . $GLOBALS['xoopsDB']->prefix('tag_link') . ' GROUP BY tag_modid';
$counts_module = [];
$module_list   = [];
$result        = $GLOBALS['xoopsDB']->query($sql);
if ($result) {
    while (false !== ($myrow = $GLOBALS['xoopsDB']->fetchArray($result))) {
        $counts_module[$myrow['tag_modid']] = $myrow['count_tag'];
    }
    if (!empty($counts_module)) {
        /** @var \XoopsModuleHandler $moduleHandler */
        $moduleHandler = xoops_getHandler('module');
        $module_list   = $moduleHandler->getList(new \Criteria('mid', '(' . implode(', ', array_keys($counts_module)) . ')', 'IN'));
    }
}

$opform     = new \XoopsSimpleForm('', 'moduleform', xoops_getenv('SCRIPT_NAME'), 'get', true);
$tray       = new \XoopsFormElementTray('');
$mod_select = new \XoopsFormSelect(_SELECT, 'modid', $modid);
$mod_select->addOption(-1, _AM_TAG_GLOBAL);
$mod_select->addOption(0, _AM_TAG_ALL);
foreach ($module_list as $module => $module_name) {
    $mod_select->addOption($module, $module_name . ' (' . $counts_module[$module] . ')');
}
$tray->addElement($mod_select);
$num_select = new \XoopsFormSelect(_AM_TAG_NUM, 'limit', $limit);
$num_select->addOptionArray(
    [
        0   => _ALL,
        10  => 10,
        50  => 50,
        100 => 100,
        500 => 500,
    ]
);
$tray->addElement($num_select);
$tray->addElement(new \XoopsFormButton('', 'submit', _SUBMIT, 'submit'));
$tray->addElement(new \XoopsFormHidden('start', $start));
$opform->addElement($tray);
$opform->display();

if (Request::hasVar('start', 'GET')) {
    /** @var \XoopsModules\Tag\TagHandler $tagHandler */
    $tagHandler = $helper->getHandler('Tag');

    $criteria = new \CriteriaCompo();
    //    $criteria->setStart($start);
    //    $criteria->setLimit($limit);
    if ($modid > Constants::DEFAULT_ID) {
        $criteria->add(new \Criteria('l.tag_modid', $modid));
    }
    $tags = $tagHandler->getByLimit($limit, $start, $criteria, null, false);
    //    $tags = $tagHandler->getByLimit(0, 0, $criteria, null, false);
    if ($tags && is_array($tags)) {
        foreach ($tags as $tag_id => $tag) {
            $tagHandler->update_stats($tag_id, (-1 == $modid) ? Constants::DEFAULT_ID : $tag['modid']);
        }
        $helper->redirect("admin/syn.tag.php?modid={$modid}&amp;start=" . ($start + $limit) . "&amp;limit={$limit}", Constants::REDIRECT_DELAY_MEDIUM, _AM_TAG_IN_PROCESS);
    }
    echo '<h2>' . _AM_TAG_FINISHED . "</h2>\n";
}
require_once __DIR__ . '/admin_footer.php';
