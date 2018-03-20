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

use XoopsModules\Tag\Constants;

include __DIR__ . '/header.php';

xoops_loadLanguage('main', 'tag');
/*
if (($GLOBALS["xoopsModule"] instanceof XoopsModule) || ("tag" != $GLOBALS["xoopsModule"]->getVar("dirname"))) {
    xoops_loadLanguage("main", "tag");
}
*/
if (tag_parse_args($args_num, $args, $args_str)) {
    $args['modid'] = !empty($args['modid']) ? $args['modid'] : @$args_num[0];
    $args['catid'] = !empty($args['catid']) ? $args['catid'] : @$args_num[1];
    $args['start'] = !empty($args['start']) ? $args['start'] : @$args_num[2];
}

$modid = (int)(empty($_GET['modid']) ? @$args['modid'] : $_GET['modid']);
$catid = (int)(empty($_GET['catid']) ? @$args['catid'] : $_GET['catid']);
$start = (int)(empty($_GET['start']) ? @$args['start'] : $_GET['start']);
$sort  = '';
$order = '';

if (empty($modid) && ($GLOBALS['xoopsModule'] instanceof XoopsModule)
    && ('tag' !== $GLOBALS['xoopsModule']->getVar('dirname'))) {
    $modid = $GLOBALS['xoopsModule']->getVar('mid');
}

if (!empty($tag_desc)) {
    $page_title = $tag_desc;
} else {
    $module_name = ('tag' === $GLOBALS['xoopsModule']->getVar('dirname')) ? $GLOBALS['xoopsConfig']['sitename'] : $GLOBALS['xoopsModule']->getVar('name');
    $page_title  = sprintf(_MD_TAG_TAGLIST, $module_name);
}
$xoopsOption['template_main']   = 'tag_list.tpl';
$xoopsOption['xoops_pagetitle'] = strip_tags($page_title);
include $GLOBALS['xoops']->path('/header.php');

$mode_display = empty($mode_display) ? @$_GET['mode'] : $mode_display;
switch (strtolower($mode_display)) {
    case 'list':
        $mode_display = 'list';
        $sort         = 'count';
        $order        = 'DESC';
        $limit        = empty($tag_config['limit_tag_list']) ? 10 : (int)$tag_config['limit_tag'];
        break;
    case 'cloud':
    default:
        $mode_display = 'cloud';
        $sort         = 'count';
        $order        = 'DESC';
        $limit        = empty($tag_config['limit_tag_cloud']) ? 100 : (int)$tag_config['limit_tag_cloud'];
        break;
}
/** @var \XoopsModules\Tag\Handler $tagHandler */
$tagHandler = \XoopsModules\Tag\Helper::getInstance()->getHandler('Tag'); // xoops_getModuleHandler('tag', 'tag');
$tag_config = tag_load_config();
tag_define_url_delimiter();

$criteria = new \CriteriaCompo();
$criteria->setSort($sort);
$criteria->setOrder($order);
$criteria->setStart($start);
$criteria->setLimit($limit);
$criteria->add(new \Criteria('o.tag_status', Constants::STATUS_ACTIVE));
if (!empty($modid)) {
    $criteria->add(new \Criteria('l.tag_modid', $modid));
    if ($catid >= 0) {
        $criteria->add(new \Criteria('l.tag_catid', $catid));
    }
}
$tags =& $tagHandler->getByLimit(0, 0, $criteria);

$count_max = 0;
$count_min = 0;
$tags_term = [];
foreach (array_keys($tags) as $key) {
    if ($tags[$key]['count'] > $count_max) {
        $count_max = $tags[$key]['count'];
    }
    if ($tags[$key]['count'] < $count_min) {
        $count_min = $tags[$key]['count'];
    }
    $tags_term[] = strtolower($tags[$key]['term']);
}
array_multisort($tags_term, SORT_ASC, $tags);
$count_interval = $count_max - $count_min;
$level_limit    = 5;

$font_max   = $tag_config['font_max'];
$font_min   = $tag_config['font_min'];
$font_ratio = $count_interval ? ($font_max - $font_min) / $count_interval : 1;

$tags_data = [];
foreach (array_keys($tags) as $key) {
    /*
     * Font-size = ((tag.count - count.min) * (font.max - font.min) / (count.max - count.min) ) * 100%
     */
    $tags_data[] = [
        'id'    => $tags[$key]['id'],
        'font'  => empty($count_interval) ? 100 : floor(($tags[$key]['count'] - $count_min) * $font_ratio) + $font_min,
        'level' => empty($count_max) ? 0 : floor(($tags[$key]['count'] - $count_min) * $level_limit / $count_max),
        'term'  => urlencode($tags[$key]['term']),
        'title' => htmlspecialchars($tags[$key]['term']),
        'count' => $tags[$key]['count']
    ];
}
unset($tags, $tags_term);

if (!empty($start) || count($tags_data) >= $limit) {
    $count_tag = $tagHandler->getCount($criteria); // modid, catid

    if ('list' === mb_strtolower($mode_display)) {
        include $GLOBALS['xoops']->path('/class/pagenav.php');
        $nav     = new \XoopsPageNav($count_tag, $limit, $start, 'start', "catid={$catid}&amp;mode={$mode_display}");
        $pagenav = $nav->renderNav(4);
    } else {
        $pagenav = "<a href='" . xoops_getenv('PHP_SELF') . "?catid={$catid}&amp;mode={$mode_display}\">" . _MORE . '</a>';
    }
} else {
    $pagenav = '';
}

$xoopsTpl->assign('lang_jumpto', _MD_TAG_JUMPTO);
$xoopsTpl->assign('tag_page_title', $page_title);
$xoopsTpl->assign_by_ref('tags', $tags_data);

// Loading module meta data, NOT THE RIGHT WAY DOING IT
$xoopsTpl->assign('xoops_pagetitle', $xoopsOption['xoops_pagetitle']);
$xoopsTpl->assign('xoops_module_header', $xoopsOption['xoops_module_header']);
$xoopsTpl->assign('xoops_meta_description', $xoopsOption['xoops_pagetitle']);

require_once __DIR__ . '/footer.php';
