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

use XoopsModules\Tag\Utility;
use XoopsModules\Tag\Common;

require_once __DIR__ . '/header.php';

$page_title = sprintf(_MD_TAG_TAGLIST, $GLOBALS['xoopsConfig']['sitename']);
$GLOBALS['xoopsOption']['template_main'] = 'tag_index.tpl';
$GLOBALS['xoopsOption']['xoops_pagetitle'] = strip_tags($page_title);

require_once $GLOBALS['xoops']->path('header.php');
$GLOBALS['xoTheme']->addStylesheet('browse.php?modules/' . $moduleDirName . '/assets/css/style.css');

/**
 * @var \XoopsModules\Tag\Helper $helper
 * @var \XoopsModules\Tag\TagHandler $tagHandler
 */
$tagHandler = $helper->getHandler('Tag');
$tag_config = Utility::tag_load_config();
Utility::tag_define_url_delimiter();

$limit = empty($tag_config['limit_cloud_list']) ? 100 : $tag_config['limit_cloud_list'];

$criteria = new \CriteriaCompo();
$criteria->setSort('count');
$criteria->order = 'DESC'; // patch for XOOPS <= 2.5.10, does not set order correctly using setOrder() method

/** @todo determine if the following call should use $limit as first param to reduce # of returned tags */
$tags_array = $tagHandler->getByLimit($limit, 0, $criteria, null, false);
$tags_term_array = [];

// set min and max tag count
$count_array = array_column($tags_array, 'count', 'id');
$count_min = count($count_array) > 0 ? min($count_array) : 0;
$count_min = $count_min > 0 ? $count_min : 0;
$count_max = count($count_array) > 0 ? max($count_array) : 0;
$count_max = $count_max > 0 ? $count_max : 0;

$term_array = array_column($tags_array, 'term', 'id');
$tags_term_array  = array_map('mb_strtolower', $term_array);
array_multisort($tags_term_array, SORT_ASC, $tags_array);
$count_interval = $count_max - $count_min;
$level_limit    = 5;

$font_max = $tag_config['font_max'];
$font_min = $tag_config['font_min'];
$font_ratio = $count_interval ? ($font_max - $font_min) / $count_interval : 1;

$tags_data_array = [];
foreach ($tags_array as $tag) {
    /*
     * Font-size = ((tag.count - count.min) * (font.max - font.min) / (count.max - count.min) ) * 100%
     */
    $tags_data_array[] = [
        'id'    => $tag['id'],
        'font'  => empty($count_interval) ? 100 : floor(($tag['count'] - $count_min) * $font_ratio) + $font_min,
        'level' => empty($count_max) ? 0 : floor(($tag['count'] - $count_min) * $level_limit / $count_max),
        'term'  => urlencode($tag['term']),
        'title' => htmlspecialchars($tag['term'], ENT_QUOTES | ENT_HTML5),
        'count' => $tag['count'],
    ];
}

unset($tags_array, $tag, $count_array, $term_array, $tags_term_array);

// Breadcrumb
$breadcrumb = new Common\Breadcrumb();
$breadcrumb->addLink($helper->getModule()->getVar('name'));

$GLOBALS['xoopsTpl']->assign([
                                 'lang_jumpto' => _MD_TAG_JUMPTO,
                                 'pagenav' => '<a href="' . $helper->url('list.tag.php') . '">' . _MORE . "</a>\n",
                                 'tag_page_title' => $page_title,
                                 'tag_breadcrumb' => $breadcrumb->render(),
                                 // Loading module meta data, NOT THE RIGHT WAY DOING IT
                                 'xoops_pagetitle' => $GLOBALS['xoopsOption']['xoops_pagetitle'],
                                 //'xoops_module_header' => $GLOBALS['xoopsOption']['xoops_module_header'],
                                 'xoops_meta_description' => $GLOBALS['xoopsOption']['xoops_pagetitle'],
]);
//@todo figure out why $tags_data_array is using assign_by_ref
$GLOBALS['xoopsTpl']->assign_by_ref('tags', $tags_data_array);

require_once __DIR__ . '/footer.php';
