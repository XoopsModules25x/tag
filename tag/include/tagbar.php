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
 * @version         $Id: tagbar.php 10505 2012-12-23 03:33:54Z beckmi $
 * @package         tag
 */
 
if (!defined('XOOPS_ROOT_PATH') || !is_object($GLOBALS["xoopsModule"])) {
    die();
}

/**
 * Display tag list
 *
 * @var        array    $tags    array of tag string
 * OR
 * @var        integer    $itemid
 * @var        integer    $catid
 * @var        integer    $modid
 *
 * @return     array    (subject language, array of linked tags)
 */
function tagBar($tags, $catid = 0, $modid = 0)
{
    static $loaded, $delimiter;
    
    if (empty($tags)) return array();
    
    if (!isset($loaded)):
    include XOOPS_ROOT_PATH . "/modules/tag/include/vars.php";
    include_once XOOPS_ROOT_PATH . "/modules/tag/include/functions.php";
    tag_define_url_delimiter();
    if (!is_object($GLOBALS["xoopsModule"]) || "tag" != $GLOBALS["xoopsModule"]->getVar("dirname")) {
        xoops_loadLanguage("main", "tag");
    }
    $loaded = 1;
    $delimiter = @file_exists(XOOPS_ROOT_PATH . "/modules/tag/images/delimiter.gif") ? "<img src=\"" . XOOPS_URL . "/modules/tag/images/delimiter.gif\" alt=\"\" />" : "<img src=\"" . XOOPS_URL . "/images/pointer.gif\" alt=\"\" />";
    endif;
    
    // itemid
    if (is_numeric($tags)) {
        if (empty($modid) && is_object($GLOBALS["xoopsModule"])) {
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
        $tags_data[] = "<a href=\"" . XOOPS_URL . "/modules/" . $GLOBALS["xoopsModule"]->getVar("dirname") . "/view.tag.php" . URL_DELIMITER . urlencode($tag) . "\" title=\"" . htmlspecialchars($tag) . "\">" . htmlspecialchars($tag) . "</a>";
    }
    return array(
            "title"     => TAG_MD_TAGS, 
            "delimiter" => $delimiter, 
            "tags"      => $tags_data);
}
?>