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
 * @copyright       XOOPS Project (https://xoops.org)
 * @license         http://www.fsf.org/copyleft/gpl.html GNU public license
 * @since           1.00
 * @author          Taiwen Jiang <phppp@users.sourceforge.net>
 * */

use Xmf\Request;
use XoopsModules\Tag\Constants;

include __DIR__ . '/header.php';

//xoops_loadLanguage('main', 'tag');
/*
if (!($GLOBALS["xoopsModule"] instanceof XoopsModule) || "tag" != $GLOBALS["xoopsModule"]->getVar("dirname", "n")) {
    xoops_loadLanguage("main", "tag");
}
*/

if (tag_parse_args($args_num, $args, $args_str)) {
    $args['tag']   = !empty($args['tag']) ? $args['tag'] : @$args_num[0];
    $args['term']  = !empty($args['term']) ? $args['term'] : @$args_str[0];
    $args['modid'] = !empty($args['modid']) ? $args['modid'] : @$args_num[1];
    $args['catid'] = !empty($args['catid']) ? $args['catid'] : @$args_num[2];
    $args['start'] = !empty($args['start']) ? $args['start'] : @$args_num[3];
}

$tag_id   = (int)(empty($_GET['tag']) ? @$args['tag'] : $_GET['tag']);
$tag_term = empty($_GET['term']) ? @$args['term'] : Request::getString('term', '', 'GET');
$modid    = (int)(empty($_GET['modid']) ? @$args['modid'] : $_GET['modid']);
$catid    = (int)(empty($_GET['catid']) ? @$args['catid'] : $_GET['catid']);
$start    = (int)(empty($_GET['start']) ? @$args['start'] : $_GET['start']);

if (empty($modid) && ($GLOBALS['xoopsModule'] instanceof XoopsModule)
    && 'tag' !== $GLOBALS['xoopsModule']->getVar('dirname', 'n')) {
    $modid = $GLOBALS['xoopsModule']->getVar('mid');
}

if (empty($tag_id) && empty($tag_term)) {
    redirect_header($GLOBALS['xoops']->url('www/modules/' . $GLOBALS['xoopsModule']->getVar('dirname') . '/index.php'), 2, _MD_TAG_INVALID);
}
/** @var \XoopsModules\Tag\Handler $tagHandler */
$tagHandler = \XoopsModules\Tag\Helper::getInstance()->getHandler('Tag'); // xoops_getModuleHandler('tag', 'tag');
if (!empty($tag_id)) {
    if (!$tag_obj = $tagHandler->get($tag_id)) {
        redirect_header($GLOBALS['xoops']->url('www/modules/' . $GLOBALS['xoopsModule']->getVar('dirname') . '/index.php'), 2, _MD_TAG_INVALID);
    }
    $tag_term = $tag_obj->getVar('tag_term', 'n');
} else {
    if (!$tags_obj =& $tagHandler->getObjects(new \Criteria('tag_term', $myts->addSlashes(trim($tag_term))))) {
        redirect_header($GLOBALS['xoops']->url('www/modules/' . $GLOBALS['xoopsModule']->getVar('dirname') . '/index.php'), 2, _MD_TAG_INVALID);
    }
    $tag_obj = $tags_obj[0];
    $tag_id  = $tag_obj->getVar('tag_id');
}

// @todo: where does $tag_desc come from? - looks like it will always be empty
if (!empty($tag_desc)) {
    $page_title = $tag_desc;
} else {
    $module_name = ('tag' === $GLOBALS['xoopsModule']->getVar('dirname', 'n')) ? $GLOBALS['xoopsConfig']['sitename'] : $GLOBALS['xoopsModule']->getVar('name', 'n');
    $page_title  = sprintf(_MD_TAG_TAGVIEW, htmlspecialchars($tag_term), $module_name);
}
$GLOBALS['xoopsOption']['template_main']   = 'tag_view.tpl';
$GLOBALS['xoopsOption']['xoops_pagetitle'] = strip_tags($page_title);

include $GLOBALS['xoops']->path('/header.php');

$tag_config = tag_load_config();
tag_define_url_delimiter();

$limit = empty($tag_config['items_perpage']) ? Constants::DEFAULT_LIMIT : $tag_config['items_perpage'];

$criteria = new \CriteriaCompo(new \Criteria('o.tag_id', $tag_id));
$criteria->setSort('time');
$criteria->setOrder('DESC');
$criteria->setStart($start);
$criteria->setLimit($limit);
if (!empty($modid)) {
    $criteria->add(new \Criteria('o.tag_modid', $modid));
    if ($catid >= 0) {
        $criteria->add(new \Criteria('o.tag_catid', $catid));
    }
}
$items = $tagHandler->getItems($criteria); // Tag, imist, start, sort, order, modid, catid

$items_module = [];
$modules_obj  = [];
if (!empty($items)) {
    foreach (array_keys($items) as $key) {
        $items_module[$items[$key]['modid']][$items[$key]['catid']][$items[$key]['itemid']] = [];
    }
    /** @var XoopsModuleHandler $moduleHandler */
    $moduleHandler = xoops_getHandler('module');
    $modules_obj   = $moduleHandler->getObjects(new \Criteria('mid', '(' . implode(', ', array_keys($items_module)) . ')', 'IN'), true);
    foreach (array_keys($modules_obj) as $mid) {
        $dirname = $modules_obj[$mid]->getVar('dirname', 'n');
        if (file_exists($GLOBALS['xoops']->path("/modules/{$dirname}/class/plugins/plugin.tag.php"))) {
            require_once $GLOBALS['xoops']->path("/modules/{$dirname}/class/plugins/plugin.tag.php");
        } elseif (file_exists($GLOBALS['xoops']->path("/modules/{$dirname}/include/plugin.tag.php"))) {
            require_once $GLOBALS['xoops']->path("/modules/{$dirname}/include/plugin.tag.php");
        } elseif (file_exists($GLOBALS['xoops']->path("/modules/tag/plugin/{$dirname}.php"))) {
            require_once $GLOBALS['xoops']->path("/modules/tag/plugin/{$dirname}.php");
        } else {
            continue;
        }
        /*
                if (!@require_once $GLOBALS['xoops']->path("/modules/{$dirname}/include/plugin.tag.php")) {
                    if (!@require_once $GLOBALS['xoops']->path("/modules/tag/plugin/{$dirname}.php")) {
                        continue;
                    }
                }
        */
        $func_tag = "{$dirname}_tag_iteminfo";
        if (!function_exists($func_tag)) {
            continue;
        }
        // Return related item infomation: title, content, time, uid, all tags
        $res = $func_tag($items_module[$mid]);
    }
}

$items_data = [];
$uids       = [];
require_once $GLOBALS['xoops']->path('/modules/tag/include/tagbar.php');
foreach (array_keys($items) as $key) {
    /**
     * Get item fileds:
     * title
     * content
     * time
     * uid
     * tags
     */
    if (!$item = @$items_module[$items[$key]['modid']][$items[$key]['catid']][$items[$key]['itemid']]) {
        continue;
    }
    $item['module']  = $modules_obj[$items[$key]['modid']]->getVar('name');
    $item['dirname'] = $modules_obj[$items[$key]['modid']]->getVar('dirname', 'n');
    $time            = empty($item['time']) ? $items[$key]['time'] : $item['time'];
    $item['time']    = formatTimestamp($time, 's');
    $item['tags']    = @tagBar($item['tags']);
    $items_data[]    = $item;
    // @todo: fix this to use xoops user id, if present otherwise to 1st admin
    $uids[$item['uid']] = 1;
}
xoops_load('XoopsUserUtility');
$users = XoopsUserUtility::getUnameFromIds(array_keys($uids));

foreach (array_keys($items_data) as $key) {
    $items_data[$key]['uname'] = $users[$items_data[$key]['uid']];
}

if (!empty($start) || count($items_data) >= $limit) {
    $count_item = $tagHandler->getItemCount($tag_id, $modid, $catid); // Tag, modid, catid

    require_once $GLOBALS['xoops']->path('/class/pagenav.php');
    $nav     = new \XoopsPageNav($count_item, $limit, $start, 'start', "tag={$tag_id}&amp;catid={$catid}");
    $pagenav = $nav->renderNav(4);
} else {
    $pagenav = '';
}

$tag_addon = [];
if (!empty($GLOBALS['_MD_TAG_ADDONS'])) {
    $tag_addon['title'] = _MD_TAG_TAG_ON;
    foreach ($GLOBALS['_MD_TAG_ADDONS'] as $key => $_tag) {
        $_term                 = (empty($_tag['function'])
                                  || !function_exists($_tag['function'])) ? $tag_term : $_tag['function']($tag_term);
        $tag_addon['addons'][] = "<a href='" . sprintf($_tag['link'], urlencode($_term)) . "' target='{$key}' title='{$_tag['title']}'>{$_tag['title']}</a>";
    }
}

$GLOBALS['xoopsTpl']->assign([
                                 'module_name'            => $GLOBALS['xoopsModule']->getVar('name'),
                                 'tag_id'                 => $tag_id,
                                 'tag_term'               => urlencode($tag_term),
                                 'tag_title'              => htmlspecialchars($tag_term),
                                 'tag_page_title'         => $page_title,
                                 // Loading module meta data, NOT THE RIGHT WAY DOING IT
                                 'xoops_pagetitle'        => $GLOBALS['xoopsOption']['xoops_pagetitle'],
                                 'xoops_module_header'    => $GLOBALS['xoopsOption']['xoops_module_header'],
                                 'xoops_meta_description' => $GLOBALS['xoopsOption']['xoops_pagetitle']
                             ]);
$xoopsTpl->assign_by_ref('tag_addon', $tag_addon);
$xoopsTpl->assign_by_ref('tag_articles', $items_data);
$xoopsTpl->assign_by_ref('pagenav', $pagenav);

require_once __DIR__ . '/footer.php';
