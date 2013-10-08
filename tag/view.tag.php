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
 * @copyright       The XOOPS project http://sourceforge.net/projects/xoops/
 * @license         http://www.fsf.org/copyleft/gpl.html GNU public license
 * @since           1.0.0
 * @author          Taiwen Jiang <phppp@users.sourceforge.net>
 * @version         $Id: view.tag.php 12070 2013-09-20 23:09:39Z beckmi $
 * @package         tag
 */
include dirname(__FILE__) . "/header.php";

if (!is_object($GLOBALS["xoopsModule"]) || "tag" != $GLOBALS["xoopsModule"]->getVar("dirname", "n")) {
    xoops_loadLanguage("main", "tag");
}

if (tag_parse_args($args_num, $args, $args_str)) {
    $args["tag"]        = !empty($args["tag"]) ? $args["tag"] : @$args_num[0];
    $args["term"]       = !empty($args["term"]) ? $args["term"] : @$args_str[0];
    $args["modid"]      = !empty($args["modid"]) ? $args["modid"] : @$args_num[1];
    $args["catid"]      = !empty($args["catid"]) ? $args["catid"] : @$args_num[2];
    $args["start"]      = !empty($args["start"]) ? $args["start"] : @$args_num[3];
}

$tag_id     = intval( empty($_GET["tag"]) ? @$args["tag"] : $_GET["tag"] );
$tag_term   = empty($_GET["term"]) ? @$args["term"] : $_GET["term"];
$modid      = intval( empty($_GET["modid"]) ? @$args["modid"] : $_GET["modid"] );
$catid      = intval( empty($_GET["catid"]) ? @$args["catid"] : $_GET["catid"] );
$start      = intval( empty($_GET["start"]) ? @$args["start"] : $_GET["start"] );

if (empty($modid) && is_object($GLOBALS["xoopsModule"]) && "tag" != $GLOBALS["xoopsModule"]->getVar("dirname", "n")) {
    $modid = $GLOBALS["xoopsModule"]->getVar("mid");
}

if (empty($tag_id) && empty($tag_term) ) {
    redirect_header(XOOPS_URL . "/modules/" . $GLOBALS["xoopsModule"]->getVar("dirname") . "/index.php", 2, TAG_MD_INVALID);
    exit();
}
$tag_handler =& xoops_getmodulehandler("tag", "tag");
if (!empty($tag_id)) {
    if (!$tag_obj =& $tag_handler->get($tag_id)) {
        redirect_header(XOOPS_URL . "/modules/" . $GLOBALS["xoopsModule"]->getVar("dirname") . "/index.php", 2, TAG_MD_INVALID);
        exit();
    }
    $tag_term = $tag_obj->getVar("tag_term", "n");
} else {
    if (!$tags_obj = $tag_handler->getObjects(new Criteria("tag_term", $myts->addSlashes(trim($tag_term))))) {
        redirect_header(XOOPS_URL . "/modules/" . $GLOBALS["xoopsModule"]->getVar("dirname") . "/index.php", 2, TAG_MD_INVALID);
        exit();
    }
    $tag_obj =& $tags_obj[0];
    $tag_id = $tag_obj->getVar("tag_id");
}


if (!empty($tag_desc)) {
    $page_title = $tag_desc;
} else {
    $module_name = ("tag" == $xoopsModule->getVar("dirname", "n")) ? $xoopsConfig["sitename"] : $xoopsModule->getVar("name", "n");
    $page_title = sprintf(TAG_MD_TAGVIEW, htmlspecialchars($tag_term), $module_name);
}
$xoopsOption["template_main"] = "tag_view.html";
$xoopsOption["xoops_pagetitle"] = strip_tags($page_title);
include XOOPS_ROOT_PATH . "/header.php";

$tag_config = tag_load_config();
tag_define_url_delimiter();

$limit  = empty($tag_config["items_perpage"]) ? 10 : $tag_config["items_perpage"];
$sort   = "time";
$order  = "DESC";

$criteria = new CriteriaCompo(new Criteria("o.tag_id", $tag_id));
$criteria->setSort($sort);
$criteria->setOrder($order);
$criteria->setStart($start);
$criteria->setLimit($limit);
if (!empty($modid)) {
    $criteria->add( new Criteria("o.tag_modid", $modid) );
    if ($catid >= 0) {
        $criteria->add( new Criteria("o.tag_catid", $catid) );
    }
}
$items = $tag_handler->getItems($criteria); // Tag, imist, start, sort, order, modid, catid

$items_module = array();
$modules_obj = array();
if (!empty($items)) {
    foreach (array_keys($items) as $key) {
        $items_module[$items[$key]["modid"]][$items[$key]["catid"]][$items[$key]["itemid"]] = array();
    }
    $module_handler =& xoops_gethandler('module');
    $modules_obj = $module_handler->getObjects(new Criteria("mid", "(" . implode(", ", array_keys($items_module)) . ")", "IN"), true);
    foreach (array_keys($modules_obj) as $mid) {
        $dirname = $modules_obj[$mid]->getVar("dirname", "n");
        if (!@include_once XOOPS_ROOT_PATH . "/modules/{$dirname}/include/plugin.tag.php") {
            if (!@include_once XOOPS_ROOT_PATH . "/modules/tag/plugin/{$dirname}.php") {
                continue;
            }
        }
        $func_tag = "{$dirname}_tag_iteminfo";
        if (!function_exists($func_tag)) {
            continue;
        }
        // Return related item infomation: title, content, time, uid, all tags
        $res = $func_tag($items_module[$mid]);
    }
}

$items_data = array();
$uids = array();
include_once XOOPS_ROOT_PATH . "/modules/tag/include/tagbar.php";
foreach (array_keys($items) as $key) {
    /**
     * Get item fileds:
     * title
     * content
     * time
     * uid
     * tags
     */
    if (!$item = @$items_module[$items[$key]["modid"]][$items[$key]["catid"]][$items[$key]["itemid"]]) continue;
    $item["module"]     = $modules_obj[$items[$key]["modid"]]->getVar("name");
    $item["dirname"]    = $modules_obj[$items[$key]["modid"]]->getVar("dirname", "n");
    $time = empty($item["time"]) ? $items[$key]["time"] : $item["time"];
    $item["time"]       = formatTimestamp($time, "s");
    $item["tags"]       = @tagBar($item["tags"]);
    $items_data[]       = $item;
    $uids[$item["uid"]] = 1;
}
xoops_load('XoopsUserUtility');
$users = XoopsUserUtility::getUnameFromIds(array_keys($uids));

foreach (array_keys($items_data) as $key) {
    $items_data[$key]["uname"] = $users[$items_data[$key]["uid"]];
}

if ( !empty($start) || count($items_data) >= $limit) {
    $count_item = $tag_handler->getItemCount($tag_id, $modid, $catid); // Tag, modid, catid

    include_once XOOPS_ROOT_PATH . "/class/pagenav.php";
    $nav = new XoopsPageNav($count_item, $limit, $start, "start", "tag={$tag_id}&amp;catid={$catid}");
    $pagenav = $nav->renderNav(4);
} else {
    $pagenav = "";
}

$tag_addon = array();
if (!empty($GLOBALS["TAG_MD_ADDONS"])) {
    $tag_addon["title"] = TAG_MD_TAG_ON;
    foreach ($GLOBALS["TAG_MD_ADDONS"] as $key => $_tag) {
        $_term = (empty($_tag["function"]) || !function_exists($_tag["function"])) ? $tag_term : $_tag["function"]($tag_term);
        $tag_addon["addons"][] = "<a href=\"" . sprintf($_tag["link"], urlencode($_term)) . "\" target=\"{$key}\" title=\"{$_tag['title']}\">{$_tag['title']}</a>";
    }
}

$xoopsTpl->assign("module_name", $GLOBALS["xoopsModule"]->getVar("name"));
$xoopsTpl->assign("tag_id", $tag_id);
$xoopsTpl->assign("tag_term", urlencode($tag_term));
$xoopsTpl->assign("tag_title", htmlspecialchars($tag_term));
$xoopsTpl->assign("tag_page_title", $page_title);
$xoopsTpl->assign_by_ref("tag_addon", $tag_addon);
$xoopsTpl->assign_by_ref("tag_articles", $items_data);
$xoopsTpl->assign_by_ref("pagenav", $pagenav);

// Loading module meta data, NOT THE RIGHT WAY DOING IT
$xoopsTpl -> assign("xoops_pagetitle", $xoopsOption["xoops_pagetitle"]);
$xoopsTpl -> assign("xoops_module_header", $xoopsOption["xoops_module_header"]);
$xoopsTpl -> assign("xoops_meta_description", $xoopsOption["xoops_pagetitle"]);

include_once dirname(__FILE__) . "/footer.php";
?>