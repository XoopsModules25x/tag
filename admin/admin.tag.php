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

use Xmf\Request;
use XoopsModules\Tag;
use XoopsModules\Tag\Constants;

require_once __DIR__ . '/admin_header.php';
require_once $GLOBALS['xoops']->path('/class/xoopsformloader.php');

xoops_cp_header();

/** @var XoopsModules\Tag\Helper $helper */
require_once $helper->path('include/vars.php');

/** @var Xmf\Module\Admin $adminObject */
$adminObject->displayNavigation(basename(__FILE__));

$limit  = $GLOBALS['xoopsModuleConfig']['items_perpage'];
$modid  = Request::getInt('modid', Constants::DEFAULT_ID);
$start  = Request::getInt('start', Constants::BEGINNING);
$status = Request::getInt('status', Constants::STATUS_ALL, 'GET');

/**
 * @var XoopsModules\Tag\TagHandler  $tagHandler
 * @var XoopsModules\Tag\LinkHandler $linkHandler
 */
$tagHandler  = Tag\Helper::getInstance()->getHandler('Tag');
$linkHandler = Tag\Helper::getInstance()->getHandler('Link');

$post_tags = Request::getArray('tags', [], 'POST');
if (!empty($post_tags)) {
    $msg_db_updated = '';
    /** {@internal - Test using following code to reduce dB accesses }} */
    /*
    $postTagIdArray = array_keys($post_tags);
    $postTagIdArray = array_map('intval', $postTagIdArray);
    $postTagIdArray = array_unique($postTagIdArray);
    $criteria = new \Criteria('tag_id', '(' . implode(',', $postTagIdArray) . ')', 'IN');
    $tagObjArray = $tagHandler->getAll($criteria);
    foreach ($post_tags as $tagId => $postTagStatus) {
        if ($tagObjArray[$tagId]->isNew() || !$tagObjArray[$tagId]->getVar('tag_id')) {
            continue;
        }
        if ($postTagStatus < Constants::STATUS_ACTIVE) {
            $tagHandler->delete($tagObjArray[$tagId]);
        } elseif ($postTagStatus != $tagObjArray[$tagId]->getVar('tag_status')) {
            $tagObjArray[$tagId]->setVar('tag_status', $postTagStatus);
            $tagHandler->insert($tagObjArray[$tagId]);
            $msg_db_updated = _AM_TAG_DB_UPDATED;
        }
    }
    */
    foreach ($post_tags as $tag => $tag_status) {
        $tag_obj = $tagHandler->get($tag);
        if (!($tag_obj instanceof Tag\Tag) || !$tag_obj->getVar('tag_id')) {
            continue;
        }
        if ($tag_status < Constants::STATUS_ACTIVE) {
            $tagHandler->delete($tag_obj);
        } elseif ($tag_status != $tag_obj->getVar('tag_status')) {
            $tag_obj->setVar('tag_status', $tag_status);
            $tagHandler->insert($tag_obj);
            $msg_db_updated = _AM_TAG_DB_UPDATED;
        }
    }
    $helper->redirect("admin/admin.tag.php?modid={$modid}&amp;start={$start}&amp;status={$status}", Constants::REDIRECT_DELAY_MEDIUM, $msg_db_updated);
}

$counts_module = [];
$module_list   = [];

/** {#internal use direct SQL instead of Tag\TagHandler CRUD operations because XOOPS can't handle COUNT(DISTINCT xx) }} */
$sql    = 'SELECT tag_modid, COUNT(DISTINCT tag_id) AS count_tag' . ' FROM ' . $GLOBALS['xoopsDB']->prefix('tag_link') . ' GROUP BY tag_modid';
$result = $GLOBALS['xoopsDB']->query($sql);

if (false !== $result) {
    while (false !== ($myrow = $GLOBALS['xoopsDB']->fetchArray($result))) {
        $counts_module[$myrow['tag_modid']] = $myrow['count_tag'];
    }
    if (!empty($counts_module)) {
        /** @var \XoopsModuleHandler $moduleHandler */
        $moduleHandler = xoops_getHandler('module');
        $module_list   = $moduleHandler->getList(new \Criteria('mid', '(' . implode(', ', array_keys($counts_module)) . ')', 'IN'));
    }
} else {
    xoops_error($GLOBALS['xoopsDB']->error());
}

$opform = new \XoopsSimpleForm('', 'moduleform', $_SERVER['SCRIPT_NAME'], 'get', true);
//$opform = new \XoopsSimpleForm('', 'moduleform', xoops_getenv('SCRIPT_NAME'), 'post', true);
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
$criteria->order = 'ASC'; // patch for XOOPS <= 2.5.10, does not set order correctly using setOrder() method
$criteria->setStart($start);
$criteria->setLimit($limit);
if ($status >= Constants::STATUS_ACTIVE) {
    $criteria->add(new \Criteria('o.tag_status', $status));
}
if (!empty($modid)) {
    $criteria->add(new \Criteria('l.tag_modid', $modid));
}
$tags = $tagHandler->getByLimit(0, 0, $criteria, null, false);

$form_tags = "<form name='tags' method='post' action='"
             . $_SERVER['SCRIPT_NAME']
             . "'>\n"
             . "<table style='margin: 1px; padding: 4px;' class='outer width100 bnone bspacing1'>\n"
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

        require_once $GLOBALS['xoops']->path('/class/pagenav.php');
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
require_once __DIR__ . '/admin_footer.php';
