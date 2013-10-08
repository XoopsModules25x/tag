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
 * @version         $Id: formtag.php 10505 2012-12-23 03:33:54Z beckmi $
 * @package         tag
 */
 
if (!defined('XOOPS_ROOT_PATH')) {
    die("XOOPS root path not defined");
}

xoops_load("xoopsformtext");

class XoopsFormTag extends XoopsFormText
{

    /**
     * Constructor
     * 
     * @param    string    $name       "name" attribute
     * @param    int        $size        Size
     * @param    int        $maxlength    Maximum length of text
     * @param    mixed    $value      Initial text or itemid
     * @param    int        $catid      category id (applicable if $value is itemid)
     */
    function XoopsFormTag($name, $size, $maxlength, $value = null, $catid = 0)
    {
        include XOOPS_ROOT_PATH . "/modules/tag/include/vars.php";
        if (!is_object($GLOBALS["xoopsModule"]) || "tag" != $GLOBALS["xoopsModule"]->getVar("dirname")) {
            xoops_loadLanguage("main", "tag");
        }
        $value = empty($value) ? "" : $value;
        // itemid
        if ( !empty($value) && is_numeric($value) && is_object($GLOBALS["xoopsModule"]) ) {
            $modid = $GLOBALS["xoopsModule"]->getVar("mid");
            $tag_handler =& xoops_getmodulehandler("tag", "tag");
            if ($tags = $tag_handler->getByItem($value, $modid, $catid)) {
                $value = htmlspecialchars(implode(", ", $tags));
            } else {
                $value = "";
            }
        }
        $caption = TAG_MD_TAGS;
        $this->XoopsFormText($caption, $name, $size, $maxlength, $value);
    }

    /**
     * Prepare HTML for output
     * 
     * @return    string  HTML
     */
    function render()
    {
        $delimiters = tag_get_delimiter();
        foreach (array_keys($delimiters) as $key) {
            $delimiters[$key] = "<em style=\"font-weight: bold; color: red; font-style: normal;\">" . htmlspecialchars($delimiters[$key]) . "</em>";
        }
        $render  = "<input type='text' name='" . $this->getName() . "' id='" . $this->getName() . "' size='" . $this->getSize() . "' maxlength='" . $this->getMaxlength() . "' value='" . $this->getValue() . "' " . $this->getExtra() . " />";
        $render .= "<br />" . TAG_MD_TAG_DELIMITER . ": [" . implode("], [", $delimiters) . "]";
        return $render;
    }
}
?>