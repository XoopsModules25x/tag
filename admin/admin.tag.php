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

use Xmf\Request;
use XoopsModules\Tag;
use XoopsModules\Tag\Constants;

require_once __DIR__ . '/admin_header.php';
require_once $GLOBALS['xoops']->path('/class/xoopsformloader.php');

$adminObject = \Xmf\Module\Admin::getInstance();

xoops_cp_header();

include $GLOBALS['xoops']->path('/modules/tag/include/vars.php');
$adminObject->displayNavigation(basename(__FILE__));

$limit  = $GLOBALS['xoopsModuleConfig']['items_perpage'];
$modid  = Request::getInt('modid', Constants::DEFAULT_ID);
$start  = Request::getInt('start', Constants::BEGINNING);
$status = Request::getInt('status', Constants::STATUS_ALL, 'GET');
///** @var \XoopsModules\Tag\Handler $tagHandler */
//$tagHandler  = xoops_getModuleHandler('tag', $moduleDirName);
//$linkHandler = xoops_getModuleHandler('link', $moduleDirName);

/** @var Tag\TagHandler $tagHandler */
/** @var Tag\LinkHandler $linkHandler */
$tagHandler = Tag\Helper::getInstance()->getHandler('Tag');
$linkHandler = Tag\Helper::getInstance()->getHandler('Link');

$postTags = Request::getArray('tags', [], 'POST');
if (!empty($postTags)) {
    $msgDBUpdated = '';
    foreach ($postTags as $tag => $tag_status) {
        $tag_obj = $tagHandler->get($tag);
        if (!($tag_obj instanceof Tag) || !$tag_obj->getVar('tag_id')) {
            continue;
        }
        if ($tag_status < Constants::STATUS_ACTIVE) {
            $tagHandler->delete($tag_obj);
        } elseif ($tag_status != $tag_obj->getVar('tag_status')) {
            $tag_obj->setVar('tag_status', $tag_status);
            $tagHandler->insert($tag_obj);
            $msgDBUpdated = _AM_TAG_DB_UPDATED;
        }
    }
    redirect_header("admin.tag.php?modid={$modid}&amp;start={$start}&amp;status={$status}", Constants::REDIRECT_DELAY_MEDIUM, $msgDBUpdated);
}

$sql           = 'SELECT tag_modid, COUNT(DISTINCT tag_id) AS count_tag';
$sql           .= ' FROM ' . $GLOBALS['xoopsDB']->prefix('tag_link');
$sql           .= ' GROUP BY tag_modid';
$counts_module = [];
$module_list   = [];
$result        = $GLOBALS['xoopsDB']->query($sql);
if (false === $result) {
    xoops_error($GLOBALS['xoopsDB']->error());
} else {
    while (false !== ($myrow = $GLOBALS['xoopsDB']->fetchArray($result))) {
        $counts_module[$myrow['tag_modid']] = $myrow['count_tag'];
    }
    if (!empty($counts_module)) {
        /** @var XoopsModuleHandler $moduleHandler */
        $moduleHandler = xoops_getHandler('module');
        $module_list   = $moduleHandler->getList(new \Criteria('mid', '(' . implode(', ', array_keys($counts_module)) . ')', 'IN'));
    }
}

$opform     = new \XoopsSimpleForm('', 'moduleform', xoops_getenv('PHP_SELF'), 'get', true);
$tray       = new \XoopsFormElementTray('');
$mod_select = new \XoopsFormSelect(_SELECT, 'modid', $modid);
$mod_select->addOption(0, _ALL);
foreach ($module_list as $module => $module_name) {
    $mod_select->addOption($module, $module_name . ' (' . $counts_module[$module] . ')');
}
$tray->addElement($mod_select);
$status_select = new \XoopsFormRadio('', 'status', $status);
$status_select->addOption(Constants::STATUS_ALL, _ALL);
$status_select->addOption(Constants::STATUS_ACTIVE, _AM_TAG_ACTIVE);
$status_select->addOption(Constants::STATUS_INACTIVE, _AM_TAG_INACTIVE);
$tray->addElement($status_select);
$tray->addElement(new \XoopsFormButton('', 'submit', _SUBMIT, 'submit'));
$opform->addElement($tray);
$opform->display();

$criteria = new \CriteriaCompo();
$criteria->setSort('a');
$criteria->setOrder('ASC');
$criteria->setStart($start);
$criteria->setLimit($limit);
if ($status >= Constants::STATUS_ACTIVE) {
    $criteria->add(new \Criteria('o.tag_status', $status));
}
if (!empty($modid)) {
    $criteria->add(new \Criteria('l.tag_modid', $modid));
}
$tags =& $tagHandler->getByLimit(0, 0, $criteria, null, false);

$form_tags = "<form name='tags' method='post' action='"
             . xoops_getenv('PHP_SELF')
             . "'>\n"
             . "<table style='border-width: 0px; margin: 1px; padding: 4px;' cellspacing='1' class='outer width100'>\n"
             . "  <thead>\n"
             . "  <tr class='txtcenter'>\n"
             . "    <th class='bg3'>"
             . _AM_TAG_TERM
             . "</th>\n"
             . "    <th class='bg3 width10'>"
             . _AM_TAG_INACTIVE
             . "</th>\n"
             . "    <th class='bg3 width10'>"
             . _AM_TAG_ACTIVE
             . "</th>\n"
             . "    <th class='bg3 width10'>"
             . _DELETE
             . "</th>\n"
             . "  </tr>\n"
             . "  </thead>\n"
             . "  <tbody>\n";
if (empty($tags)) {
    $form_tags .= "  <tr><td colspan='4'>" . _NONE . "</td></tr>\n";
} else {
    $class_tr = 'odd';
    $i        = 0;
    foreach (array_keys($tags) as $key) {
        $form_tags .= "  <tr class='{$class_tr}'>\n"
                      . '    <td>'
                      . $tags[$key]['term']
                      . "</td>\n"
                      . "    <td  class='txtcenter'><input type='radio' name='tags[{$key}]' value='"
                      . Constants::STATUS_INACTIVE
                      . "'"
                      . ($tags[$key]['status'] ? ' checked' : " '' ")
                      . "></td>\n"
                      . "    <td  class='txtcenter'><input type='radio' name='tags[{$key}]' value='"
                      . Constants::STATUS_ACTIVE
                      . "'"
                      . ($tags[$key]['status'] ? " '' " : ' checked')
                      . "></td>\n"
                      . "    <td  class='txtcenter'><input type='radio' name='tags[{$key}]' value='"
                      . Constants::STATUS_DELETE
                      . "'></td>\n"
                      . "  </tr>\n";
        $class_tr  = ('even' === $class_tr) ? 'odd' : 'even';
    }
    if (!empty($start) || (count($tags) >= $limit)) {
        $count_tag = $tagHandler->getCount($criteria);

        include $GLOBALS['xoops']->path('/class/pagenav.php');
        $nav       = new \XoopsPageNav($count_tag, $limit, $start, 'start', "modid={$modid}&amp;status={$status}");
        $form_tags .= "  <tr><td colspan='4' class='txtright'>" . $nav->renderNav(4) . "</td></tr>\n";
    }
    $form_tags .= "  </tbody>\n"
                  . "  <tfoot>\n"
                  . "  <tr>\n"
                  . "    <td class='txtcenter' colspan='4'>\n"
                  . "      <input type='hidden' name='status' value='{$status}'> \n"
                  . "      <input type='hidden' name='start' value='{$start}'> \n"
                  . "      <input type='hidden' name='modid' value='{$modid}'> \n"
                  . "      <input type='submit' name='submit' value='"
                  . _SUBMIT
                  . "'> \n"
                  . "      <input type='reset' name='submit' value='"
                  . _CANCEL
                  . "'>\n"
                  . "    </td>\n"
                  . "  </tr>\n"
                  . "  </tfoot>\n";
}
$form_tags .= "  </tbody>\n" . "</table>\n" . "</form>\n";

echo $form_tags;
include __DIR__ . '/admin_footer.php';
