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
 * @author          XOOPS Module Development Team {@link http://www.xoops.org}
 * @since           1.00
 * @version         $Id: formtag.php 12898 2014-12-08 22:05:21Z zyspec $
 */

xoops_load('formtag', 'tag');

class XoopsFormTag extends TagFormTag
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
        $GLOBALS['xoopsLogger']->addDeprecated(__CLASS__ . ' is deprecated use TagFormTag instead.');
        parent::__construct($name, $size, $maxlength, $value, $catid);
    }

    /**
     * Constructor {@see XoopsFormTag}
     *
     * @param string     $name      "name" attribute
     * @param int        $size      size of input box
     * @param int        $maxlength Maximum length of text
     * @param string|int $value     Initial text or itemid
     * @param int        $catid     category id (applicable if $value is itemid)
     */
    public function XoopsFormTag($name, $size, $maxlength, $value = null, $catid = 0)
    {
        self::__construct($name, $size, $maxlength, $value, $catid);
    }
}
