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

include __DIR__ . '/header.php';

$limit = empty($tag_config['limit_tag_could']) ? 100 : $tag_config['limit_tag_could'];

$page_title                     = sprintf(_MD_TAG_TAGLIST, $GLOBALS['xoopsConfig']['sitename']);
$xoopsOption['template_main']   = 'tag_index.tpl';
$xoopsOption['xoops_pagetitle'] = strip_tags($page_title);
include $GLOBALS['xoops']->path('/header.php');
/** @var \XoopsModules\Tag\Handler $tagHandler */
$tagHandler = \XoopsModules\Tag\Helper::getInstance()->getHandler('Tag'); // xoops_getModuleHandler('tag', 'tag');
$tag_config  = tag_load_config();
tag_define_url_delimiter();

$criteria = new \CriteriaCompo();
$criteria->setSort('count');
$criteria->setOrder('DESC');
$criteria->setLimit($limit);
$tags =& $tagHandler->getByLimit(0, 0, $criteria);

$count_max = 0;
$count_min = 0;
$tags_term = [];
foreach (array_keys($tags) as $key) {
    $count_max   = max(0, $tags[$key]['count'], $count_max);
    $count_min   = min(0, $tags[$key]['count'], $count_min);
    $tags_term[] = mb_strtolower($tags[$key]['term']);
}
if (!empty($tags_term)) {
    array_multisort($tags_term, SORT_ASC, $tags);
}
$count_interval = $count_max - $count_min;
$level_limit    = 5;

$font_max   = $tag_config['font_max'];
$font_min   = $tag_config['font_min'];
$font_ratio = $count_interval ? ($font_max - $font_min) / $count_interval : 1;

$tags_data = [];
foreach (array_keys($tags) as $key) {
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
$pagenav = "<a href='" . $GLOBALS['xoops']->url('www/modules/tag/list.tag.php') . "'>" . _MORE . '</a>';

$GLOBALS['xoopsTpl']->assign([
                                 'lang_jumpto'    => _MD_TAG_JUMPTO,
                                 'pagenav'        => $pagenav,
                                 'tag_page_title' => $page_title
                             ]);
$GLOBALS['xoopsTpl']->assign_by_ref('tags', $tags_data);

// Loading module meta data, NOT THE RIGHT WAY DOING IT
$GLOBALS['xoopsTpl']->assign('xoops_pagetitle', $xoopsOption['xoops_pagetitle']);
$GLOBALS['xoopsTpl']->assign('xoops_module_header', $xoopsOption['xoops_module_header']);

require_once __DIR__ . '/footer.php';
