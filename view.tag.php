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
 * @copyright       XOOPS Project (https://xoops.org)
 * @license         https://www.fsf.org/copyleft/gpl.html GNU public license
 * @since           1.00
 * @author          Taiwen Jiang <phppp@users.sourceforge.net>
 * */

use Xmf\Request;
use XoopsModules\Tag\{
    Common,
    Constants,
    Tagbar,
    Utility
};


require_once __DIR__ . '/header.php';

//@todo refactor this code - it "works" but it's not right. Look at previous revs using $args_num to see what it's suppose to do
if (Utility::tag_parse_args($args, $args_string)) {
    $args['tag']   = !empty($args['tag']) ? $args['tag'] : (is_numeric($args_string[0]) ? $args_string[0] : Constants::DEFAULT_ID);
    $args['term']  = !empty($args['term']) ? $args['term'] : (!empty($args_string[0]) ? $args_string[0] : null);
    $args['modid'] = !empty($args['modid']) ? $args['modid'] : Constants::DEFAULT_ID;
    $args['catid'] = !empty($args['catid']) ? $args['catid'] : Constants::DEFAULT_ID;
    $args['start'] = !empty($args['start']) ? $args['start'] : Constants::BEGINNING;
}
/*
$tagid = (int)(empty($_GET['tag']) ? @$args['tag'] : $_GET['tag']);
$tag_term = empty($_GET['term']) ? @$args['term'] : Request::getString('term', '', 'GET');
$modid = (int)(empty($_GET['modid']) ? @$args['modid'] : $_GET['modid']);
$catid = (int)(empty($_GET['catid']) ? @$args['catid'] : $_GET['catid']);
$start = (int)(empty($_GET['start']) ? @$args['start'] : $_GET['start']);
*/
$tagid    = (empty($_GET['tag'])) ? @$args['tag'] : Request::getInt('tag', Constants::DEFAULT_ID, 'GET');
$tag_term = empty($_GET['term']) ? @$args['term'] : Request::getString('term', null, 'GET');
$modid    = (int)(empty($_GET['modid'])) ? @$args['modid'] : Request::getInt('modid', Constants::DEFAULT_ID, 'GET');
$catid    = (int)(empty($_GET['catid'])) ? @$args['catid'] : Request::getInt('catid', Constants::DEFAULT_ID, 'GET');
$start    = (int)(empty($_GET['start'])) ? @$args['start'] : Request::getInt('start', Constants::BEGINNING, 'GET');

if (empty($modid) && ($GLOBALS['xoopsModule'] instanceof \XoopsModule)
    && 'tag' !== $GLOBALS['xoopsModule']->getVar('dirname', 'n')) {
    $modid = $GLOBALS['xoopsModule']->getVar('mid');
}

/** @var \XoopsModules\Tag\TagHandler $tagHandler */
$tagHandler = $helper->getHandler('Tag');

if (!empty($tagid)) { // have a tag_id, so check to see if it yields a valid Tag object
    if ((!$tag_obj = $tagHandler->get((int)$tagid)) || $tag_obj->isNew()) {
        $helper->redirect('index.php', Constants::REDIRECT_DELAY_MEDIUM, _MD_TAG_INVALID);
    }
    $tag_term = $tag_obj->getVar('tag_term', 'n');
} elseif (!empty($tag_term)) {
    if (!$tags_obj = $tagHandler->getObjects(new \Criteria('tag_term', $myts->addSlashes(trim($tag_term))))) {
        $helper->redirect('index.php', Constants::REDIRECT_DELAY_MEDIUM, _MD_TAG_INVALID);
    }
    $tag_obj = $tags_obj[0];
    $tagid   = $tag_obj->getVar('tag_id');
} else {
    $helper->redirect('index.php', Constants::REDIRECT_DELAY_MEDIUM, _MD_TAG_INVALID);
}
// made it here, so now we have a valid $tagid and $tag_term
$tag_term = mb_convert_case($tag_term, MB_CASE_TITLE, 'UTF-8');

// @todo: where does $tag_desc come from? - looks like it will always be empty
if (!empty($tag_desc)) {
    $page_title = $tag_desc;
} else {
    $module_name = ('tag' === $GLOBALS['xoopsModule']->getVar('dirname', 'n')) ? $GLOBALS['xoopsConfig']['sitename'] : $GLOBALS['xoopsModule']->getVar('name', 'n');
    $module_name = mb_convert_case($module_name, MB_CASE_TITLE, 'UTF-8');
    $page_title  = sprintf(_MD_TAG_TAGVIEW, htmlspecialchars($tag_term, ENT_QUOTES | ENT_HTML5), $module_name);
}
$GLOBALS['xoopsOption']['template_main']   = 'tag_view.tpl';
$GLOBALS['xoopsOption']['xoops_pagetitle'] = strip_tags($page_title);

require_once $GLOBALS['xoops']->path('header.php');
$GLOBALS['xoTheme']->addStylesheet("browse.php?modules/{$moduleDirName}/assets/css/style.css");

$tag_config = Utility::tag_load_config();
Utility::tag_define_url_delimiter();

$limit = empty($tag_config['items_perpage']) ? Constants::DEFAULT_LIMIT : $tag_config['items_perpage'];

$criteria = new \CriteriaCompo(new \Criteria('o.tag_id', $tagid));
$criteria->setSort('time');
$criteria->order = 'DESC'; // set order directly, XOOPS 2.5x does not set order correctly using Criteria::setOrder() method
$criteria->setStart($start);
$criteria->setLimit($limit);
if (!empty($modid)) {
    $criteria->add(new \Criteria('o.tag_modid', $modid));
    if ($catid >= Constants::DEFAULT_ID) {
        $criteria->add(new \Criteria('o.tag_catid', $catid));
    }
}

$items_array       = $tagHandler->getItems($criteria);
$module_item_array = [];
$module_obj_array  = [];
if (0 < count($items_array)) {
    foreach ($items_array as $this_item) {
        $module_item_array[$this_item['modid']][$this_item['catid']][$this_item['itemid']] = [];
    }

    /** @var \XoopsModuleHandler $moduleHandler */
    $moduleHandler    = xoops_getHandler('module');
    $module_obj_array = $moduleHandler->getObjects(new \Criteria('mid', '(' . implode(', ', array_keys($module_item_array)) . ')', 'IN'), true);
    foreach ($module_obj_array as $mid => $module_obj) {
        $dirname = $module_obj->getVar('dirname', 'n');
        //$dirname = $module_obj_array[$mid]->getVar('dirname', 'n');
        if (file_exists($GLOBALS['xoops']->path("modules/{$dirname}/class/plugins/plugin.tag.php"))) {
            require_once $GLOBALS['xoops']->path("modules/{$dirname}/class/plugins/plugin.tag.php");
        } elseif (file_exists($GLOBALS['xoops']->path("modules/{$dirname}/include/plugin.tag.php"))) {
            require_once $GLOBALS['xoops']->path("modules/{$dirname}/include/plugin.tag.php");
        } elseif (file_exists($GLOBALS['xoops']->path("modules/tag/plugin/{$dirname}.php"))) {
            require_once $GLOBALS['xoops']->path("modules/tag/plugin/{$dirname}.php");
        } else {
            continue;
        }

        $func_tag = "{$dirname}_tag_iteminfo";
        if (function_exists($func_tag)) {
            // Return related item infomation: title, content, time, uid, all tags
            $res = $func_tag($module_item_array[$mid]);
        }
    }
}

$items_data = [];
$uids       = [];
//require_once $helper->path('include/tagbar.php');
$tagbar = new Tagbar();
foreach ($items_array as $key => $myItem) {
    /**
     * Get item fields:
     * title
     * content
     * time
     * uid
     * tags
     */
    if (!$item = @$module_item_array[$myItem['modid']][$myItem['catid']][$myItem['itemid']]) {
        continue;
    }
    $item['module']  = $module_obj_array[$myItem['modid']]->getVar('name');
    $item['dirname'] = $module_obj_array[$myItem['modid']]->getVar('dirname', 'n');
    $time            = empty($item['time']) ? $myItem['time'] : $item['time'];
    $item['time']    = formatTimestamp($time, 's');
    $item['tags']    = $tagbar->getTagbar($item['tags']);
    $items_data[]    = $item;
    // @todo: fix this to use xoops user id, if present otherwise to 1st admin
    $uids[$item['uid']] = 1;
}
unset($item);
xoops_load('XoopsUserUtility');
$users = \XoopsUserUtility::getUnameFromIds(array_keys($uids));

foreach ($items_data as $key => $item) {
    $items_data[$key]['uname'] = $users[$item['uid']];
}

if (!empty($start) || count($items_data) >= $limit) {
    $count_item = $tagHandler->getItemCount($tagid, $modid, $catid); // Tag, modid, catid
    require_once $GLOBALS['xoops']->path('/class/pagenav.php');
    $nav     = new \XoopsPageNav($count_item, $limit, $start, 'start', "tag={$tagid}&amp;catid={$catid}");
    $pagenav = $nav->renderNav(4);
} else {
    $pagenav = '';
}

//add-ons to tag externally (e.g. Google, Flickr)
$tag_addon = [];
if (!empty($GLOBALS['_MD_TAG_ADDONS'])) {
    $tag_addon['title'] = _MD_TAG_TAG_ON;
    foreach ($GLOBALS['_MD_TAG_ADDONS'] as $key => $_tag) {
        $_term                 = (empty($_tag['function']) || !function_exists($_tag['function'])) ? $tag_term : $_tag['function']($tag_term);
        $tag_addon['addons'][] = "<a href='" . sprintf($_tag['link'], urlencode($_term)) . "' target='{$key}' title='{$_tag['title']}'>{$_tag['title']}</a>";
    }
}

// Breadcrumb
$breadcrumb = new Common\Breadcrumb();
$breadcrumb->addLink($helper->getModule()->getVar('name'), $helper->url());
$breadcrumb->addLink(_MD_TAG_TAGS, $helper->url('list.tag.php'));
//$breadcrumb->addLink(htmlspecialchars($tag_term, ENT_QUOTES | ENT_HTML5), $helper->url('view.tag.php' . URL_DELIMITER . urlencode($tag_term)));
$breadcrumb->addLink(htmlspecialchars($tag_term, ENT_QUOTES | ENT_HTML5));

$GLOBALS['xoopsTpl']->assign(
    [
        'module_name'            => $GLOBALS['xoopsModule']->getVar('name'),
        'tag_id'                 => $tagid,
        'tag_term'               => urlencode($tag_term),
        'tag_title'              => htmlspecialchars($tag_term, ENT_QUOTES | ENT_HTML5),
        'tag_page_title'         => $page_title,
        'tag_breadcrumb'         => $breadcrumb->render(),
        // Loading module meta data, NOT THE RIGHT WAY DOING IT
        'xoops_pagetitle'        => $GLOBALS['xoopsOption']['xoops_pagetitle'],
        //'xoops_module_header'    => $GLOBALS['xoopsOption']['xoops_module_header'],
        'xoops_meta_description' => $GLOBALS['xoopsOption']['xoops_pagetitle'],
    ]
);
//@todo do these need to be assign by ref?
$xoopsTpl->assign_by_ref('tag_addon', $tag_addon);
$xoopsTpl->assign_by_ref('tag_articles', $items_data);
$xoopsTpl->assign_by_ref('pagenav', $pagenav);

require_once __DIR__ . '/footer.php';
