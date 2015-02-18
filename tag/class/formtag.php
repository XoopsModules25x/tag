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
 * @copyright       {@link http://sourceforge.net/projects/xoops/ The XOOPS Project}
 * @license         {@link http://www.fsf.org/copyleft/gpl.html GNU public license}
 * @author          Taiwen Jiang <phppp@users.sourceforge.net>
 * @since           1.00
 * @version         $Id: formtag.php 12898 2014-12-08 22:05:21Z zyspec $
 */

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

xoops_load("xoopsformtext");

class TagFormTag extends XoopsFormText
{

    /**
     * Constructor
     *
     * @param string     $name      "name" attribute
     * @param int        $size      size of input box
     * @param int        $maxlength Maximum length of text
     * @param string|int $value     Initial text or itemid
     * @param int        $catid     category id (applicable if $value is itemid)
     */
    public function __construct($name, $size, $maxlength, $value = null, $catid = 0)
    {
        include $GLOBALS['xoops']->path("/modules/tag/include/vars.php");
        if (!($GLOBALS["xoopsModule"] instanceof XoopsModule) || "tag" != $GLOBALS["xoopsModule"]->getVar("dirname")) {
            xoops_loadLanguage("main", "tag");
        }
        $value = empty($value) ? "" : $value;
        // itemid
        if (!empty($value) && is_numeric($value) && ($GLOBALS["xoopsModule"] instanceof XoopsModule) ) {
            $modid = $GLOBALS["xoopsModule"]->getVar("mid");
            $tag_handler =& xoops_getmodulehandler("tag", "tag");
            if ($tags = $tag_handler->getByItem($value, $modid, $catid)) {
                $value = htmlspecialchars(implode(", ", $tags));
            } else {
                $value = "";
            }
        }
        $caption = _MD_TAG_TAGS;
        $this->XoopsFormText($caption, $name, $size, $maxlength, $value);
    }

    /**
     * Constructor
     *
     * @param string $name      "name" attribute
     * @param int    $size      Size
     * @param int    $maxlength Maximum length of text
     * @param mixed  $value     Initial text or itemid
     * @param int    $catid     category id (applicable if $value is itemid)
     */
    public function TagFormTag($name, $size, $maxlength, $value = null, $catid = 0)
    {
        self::__construct($name, $size, $maxlength, $value, $catid);
    }

    /**
     * Prepare HTML for output
     *
     * @return string HTML
     */
    function render()
    {
        $delimiters = tag_get_delimiter();
        foreach (array_keys($delimiters) as $key) {
            $delimiters[$key] = "<em style='font-weight: bold; color: red; font-style: normal;'>" . htmlspecialchars($delimiters[$key]) . "</em>";
        }
        $render  = "<input type='text' name='" . $this->getName() . "' id='" . $this->getName() . "' size='" . $this->getSize() . "' maxlength='" . $this->getMaxlength() . "' value='" . $this->getValue() . "' " . $this->getExtra() . " />";
        $render .= "<br />" . _MD_TAG_TAG_DELIMITER . ": [" . implode("], [", $delimiters) . "]";

        return $render;
    }
}
