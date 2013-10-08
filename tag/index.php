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
 * @version         $Id: index.php 10505 2012-12-23 03:33:54Z beckmi $
 * @package         tag
 */
include dirname(__FILE__) . "/header.php";

$limit = empty($tag_config["limit_tag_could"]) ? 100 : $tag_config["limit_tag_could"];

$page_title = sprintf(TAG_MD_TAGLIST, $xoopsConfig["sitename"]);
$xoopsOption["template_main"] = "tag_index.html";
$xoopsOption["xoops_pagetitle"] = strip_tags($page_title);
include XOOPS_ROOT_PATH . "/header.php";

$tag_handler =& xoops_getmodulehandler("tag", "tag");
$tag_config = tag_load_config();
tag_define_url_delimiter();

$criteria = new CriteriaCompo();
$criteria->setSort("count");
$criteria->setOrder("DESC");
$criteria->setLimit(empty($tag_config["limit_tag_could"]) ? 100 : $tag_config["limit_tag"]);
$tags = $tag_handler->getByLimit($criteria);

$count_max = 0;
$count_min = 0;
$tags_term = array();
foreach (array_keys($tags) as $key) {
    if ($tags[$key]["count"] > $count_max) $count_max = $tags[$key]["count"];
    if ($tags[$key]["count"] < $count_min) $count_min = $tags[$key]["count"];
    $tags_term[] = strtolower($tags[$key]["term"]);
}
array_multisort($tags_term, SORT_ASC, $tags);
$count_interval = $count_max - $count_min;
$level_limit = 5;

$font_max = $tag_config["font_max"];
$font_min = $tag_config["font_min"];
$font_ratio = ($count_interval) ? ($font_max - $font_min) / $count_interval : 1;

$tags_data = array();
foreach (array_keys($tags) as $key) {
    $tags_data[] = array(
                    "id"    => $tags[$key]["id"],
                    "font"  => empty($count_interval) ? 100 : floor( ($tags[$key]["count"] - $count_min) * $font_ratio ) + $font_min,
                    "level" => empty($count_max) ? 0 : floor( ($tags[$key]["count"] - $count_min) * $level_limit / $count_max ),
                    "term"      => urlencode($tags[$key]["term"]),
                    "title"    => htmlspecialchars($tags[$key]["term"]),
                    "count" => $tags[$key]["count"],
                    );
}
unset($tags, $tags_term);
$pagenav = "<a href=\"" . XOOPS_URL . "/modules/tag/list.tag.php\">" . _MORE . "</a>";

$xoopsTpl -> assign("lang_jumpto",      TAG_MD_JUMPTO);
$xoopsTpl -> assign("pagenav",          $pagenav);
$xoopsTpl -> assign("tag_page_title",   $page_title);
$xoopsTpl -> assign_by_ref("tags",      $tags_data);

// Loading module meta data, NOT THE RIGHT WAY DOING IT
$xoopsTpl -> assign("xoops_pagetitle", $xoopsOption["xoops_pagetitle"]);
$xoopsTpl -> assign("xoops_module_header", $xoopsOption["xoops_module_header"]);

include_once "footer.php";
?>