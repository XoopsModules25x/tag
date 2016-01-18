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
 * @package        tag
 * @copyright      {@link http://sourceforge.net/projects/xoops/ The XOOPS Project}
 * @license        {@link http://www.fsf.org/copyleft/gpl.html GNU public license}
 * @author         Taiwen Jiang <phppp@users.sourceforge.net>
 * @since          1.00
 * @version        $Id: tagbar.php 12898 2014-12-08 22:05:21Z zyspec $
 */

(defined('XOOPS_ROOT_PATH') && ($GLOBALS["xoopsModule"] instanceof XoopsModule)) || exit('Restricted access');

/**
 * Display tag list
 *
 * @param array $tags array of tag string
 * OR
 * @param int     $itemid
 * @param int     $catid
 * @param int     $modid
 *
 * @return     array    (subject language, array of linked tags)
 */
function tagBar($tags, $catid = 0, $modid = 0)
{
    static $loaded, $delimiter;

    if (empty($tags)) {
        return array();
    }

    if (!isset($loaded)){
        include $GLOBALS['xoops']->path("/modules/tag/include/vars.php");
        include_once $GLOBALS['xoops']->path("/modules/tag/include/functions.php");
        tag_define_url_delimiter();
        if (!($GLOBALS["xoopsModule"] instanceof XoopsModule) || ("tag" != $GLOBALS["xoopsModule"]->getVar("dirname"))) {
            xoops_loadLanguage("main", "tag");
        }
        if (file_exists($GLOBALS['xoops']->path("/modules/tag/assets/images/delimiter.gif"))) {
            $delimiter = "<img src='" . $GLOBALS['xoops']->url("www/modules/tag/assets/images/delimiter.gif") . "' alt='' />";
        } else {
            $delimiter = "<img src='" . $GLOBALS['xoops']->url("www/assets/images/pointer.gif") . "' alt='' />";
        }
        $loaded = 1;
    }

    // itemid
    if (is_numeric($tags)) {
        if (empty($modid) && ($GLOBALS["xoopsModule"] instanceof XoopsModule)) {
            $modid = $GLOBALS["xoopsModule"]->getVar("mid");
        }
        $tag_handler =& xoops_getmodulehandler("tag", "tag");
        if (!$tags = $tag_handler->getByItem($tags, $modid, $catid)) {
            return array();
        }

    // if ready, do nothing
    } elseif (is_array($tags)) {

    // parse
    } elseif (!$tags = tag_parse_tag($tags)) {
        return array();
    }
    $tags_data = array();
    foreach ($tags as $tag) {
        $tags_data[] = "<a href='" . $GLOBALS['xoops']->url("www/modules/" . $GLOBALS["xoopsModule"]->getVar("dirname") . "/view.tag.php" . URL_DELIMITER . urlencode($tag)) . "' title='" . htmlspecialchars($tag) . "'>" . htmlspecialchars($tag) . "</a>";
    }

    return array("title" => _MD_TAG_TAGS,
             "delimiter" => $delimiter,
                  "tags" => $tags_data);
}
