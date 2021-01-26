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

use Xmf\Request;
use XoopsModules\Tag\Constants;
use XoopsModules\Tag\Helper;
use XoopsModules\Tag\Utility;
use XoopsModules\Tag\Common;

require_once __DIR__ . '/header.php';

/** @var XoopsModules\Tag\Helper $helper */
$helper->loadLanguage('main');

if (Utility::tag_parse_args($args, $args_string)) {
    $args['modid'] = !empty($args['modid']) ? $args['modid'] : Constants::DEFAULT_ID;
    $args['catid'] = !empty($args['catid']) ? $args['catid'] : Constants::DEFAULT_ID;
    $args['start'] = !empty($args['start']) ? $args['start'] : Constants::BEGINNING;
}

/*
$modid = (int)(empty($_GET['modid']) ? @$args['modid'] : $_GET['modid']);
$catid = (int)(empty($_GET['catid']) ? @$args['catid'] : $_GET['catid']);
$start = (int)(empty($_GET['start']) ? @$args['start'] : $_GET['start']);
*/
$modid = \Xmf\Request::getInt('modid', !empty($args['modid']) ? $args['modid'] : Constants::DEFAULT_ID, 'GET');
$catid = \Xmf\Request::getInt('catid', !empty($args['catid']) ? $args['catid'] : Constants::DEFAULT_ID, 'GET');
$start = \Xmf\Request::getInt('start', !empty($args['start']) ? $args['start'] : Constants::BEGINNING, 'GET');

if (empty($modid) && ($GLOBALS['xoopsModule'] instanceof \XoopsModule)
    && ('tag' !== $GLOBALS['xoopsModule']->getVar('dirname'))) {
    $modid = $GLOBALS['xoopsModule']->getVar('mid');
}

$tag_config = Utility::tag_load_config();

//@todo figure out where $tag_desc comes from
if (!empty($tag_desc)) {
    $page_title = $tag_desc;
} else {
    $module_name = ('tag' === $GLOBALS['xoopsModule']->getVar('dirname')) ? $GLOBALS['xoopsConfig']['sitename'] : $GLOBALS['xoopsModule']->getVar('name');
    $page_title  = sprintf(_MD_TAG_TAGLIST, mb_convert_case($module_name, MB_CASE_TITLE, 'UTF-8'));
}

$module_name = $helper->getModule()->getVar('name');
$page_title  = Xmf\FilterInput::clean($page_title, 'string'); // clean unwanted tags, etc.
$breadcrumb  = new Common\Breadcrumb();
$breadcrumb->addLink(mb_convert_case($module_name, MB_CASE_TITLE, 'UTF-8'), $helper->url());
$breadcrumb->addLink($page_title);

$GLOBALS['xoopsOption']['template_main'] = 'tag_list.tpl';
require_once $GLOBALS['xoops']->path('header.php');
$GLOBALS['xoTheme']->addStylesheet('browse.php?modules/' . $moduleDirName . '/assets/css/style.css');
$GLOBALS['xoopsOption']['xoops_pagetitle'] = $page_title;

$mode_display = empty($mode_display) ? \Xmf\Request::getCmd('mode', null, 'GET') : $mode_display;
switch (mb_strtolower($mode_display)) {
    case 'list':
        $mode_display = 'list';
        $limit        = (0 === (int)$tag_config['limit_tag_list']) ? 10 : (int)$tag_config['limit_tag'];
        break;
    case 'cloud':
    default:
        $mode_display = 'cloud';
        $limit        = (0 === (int)$tag_config['limit_cloud_list']) ? 100 : (int)$tag_config['limit_cloud_list'];
        break;
}
/** @var \XoopsModules\Tag\TagHandler $tagHandler */
$tagHandler = Helper::getInstance()->getHandler('Tag'); // xoops_getModuleHandler('tag', 'tag');
Utility::tag_define_url_delimiter();

$criteria = new \CriteriaCompo();
$criteria->setSort('count');
$criteria->order = 'DESC'; // direct set of order because XOOPS 2.5x won't allow anything other than 'ASC' to be set using setOrder() method
$criteria->add(new \Criteria('o.tag_status', Constants::STATUS_ACTIVE));
if (!empty($modid)) {
    $criteria->add(new \Criteria('l.tag_modid', $modid));
    if ($catid >= 0) {
        $criteria->add(new \Criteria('l.tag_catid', $catid));
    }
}
$tags_array      = $tagHandler->getByLimit($limit, $start, $criteria, null, false);
$tags_data_array = $tagHandler->getTagData($tags_array, $tag_config['font_max'], $tag_config['font_min']);

$page_nav = '';
if (!empty($start) || count($tags_data_array) >= $limit) {
    if ('list' === mb_strtolower($mode_display)) {
        require_once $GLOBALS['xoops']->path('class/pagenav.php');
        $count_tag = $tagHandler->getCount($criteria); // modid, catid
        $nav       = new \XoopsPageNav($count_tag, $limit, $start, 'start', "catid={$catid}&amp;mode={$mode_display}");
        $page_nav  = $nav->renderNav(4);
    } else {
        $page_nav = '<a href="' . xoops_getenv('SCRIPT_NAME') . "?catid={$catid}&amp;mode={$mode_display}\">" . _MORE . "</a>\n";
    }
}

$xoopsTpl->assign(
    [
        'lang_jumpto'    => _MD_TAG_JUMPTO,
        'tag_page_title' => $page_title,
        'tag_breadcrumb' => $breadcrumb->render(),
        'pagenav'        => $page_nav,
        // Loading module meta data, NOT THE RIGHT WAY DOING IT
        //'xoops_pagetitle' => $GLOBALS['xoopsOption']['xoops_pagetitle'],
        //'xoops_module_header' => $GLOBALS['xoopsOption']['xoops_module_header'],
        //'xoops_meta_description' => $GLOBALS['xoopsOption']['xoops_pagetitle']
    ]
);
//@todo determine why using assign_by_ref here?
$xoopsTpl->assign_by_ref('tags', $tags_data_array);

require_once __DIR__ . '/footer.php';
