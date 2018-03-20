<?php namespace XoopsModules\Tag;

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

use XoopsModules\Tag;

defined('XOOPS_ROOT_PATH') || die('Restricted access');

xoops_load('xoopsformtext');


/**
 * Class TagFormTag
 */
class FormTag extends \XoopsFormText
{
    /**
     * TagFormTag constructor.
     * @param string     $name      "name" attribute
     * @param int        $size      size of input box
     * @param int        $maxlength Maximum length of text
     * @param string|int $value     Initial text or itemid
     * @param int        $catid     category id (applicable if $value is itemid)
     */
    public function __construct($name, $size, $maxlength, $value = null, $catid = 0)
    {
        include $GLOBALS['xoops']->path('/modules/tag/include/vars.php');
        if (!($GLOBALS['xoopsModule'] instanceof XoopsModule) || 'tag' !== $GLOBALS['xoopsModule']->getVar('dirname')) {
            xoops_loadLanguage('main', 'tag');
        }
        $value = empty($value) ? '' : $value;
        // itemid
        if (!empty($value) && is_numeric($value) && ($GLOBALS['xoopsModule'] instanceof XoopsModule)) {
            $modid      = $GLOBALS['xoopsModule']->getVar('mid');
            /** @var \XoopsModules\Tag\Handler $tagHandler */
            $tagHandler = Tag\Helper::getInstance()->getHandler('Tag'); // xoops_getModuleHandler('tag', 'tag');
            if ($tags = $tagHandler->getByItem($value, $modid, $catid)) {
                $value = htmlspecialchars(implode(', ', $tags));
            } else {
                $value = '';
            }
        }
        $caption = _MD_TAG_TAGS;
        parent::__construct($caption, $name, $size, $maxlength, $value);
    }

    /**
     * Prepare HTML for output
     *
     * @return string HTML
     */
    public function render()
    {
        $delimiters = tag_get_delimiter();
        foreach (array_keys($delimiters) as $key) {
            $delimiters[$key] = "<em style='font-weight: bold; color: red; font-style: normal;'>" . htmlspecialchars($delimiters[$key]) . '</em>';
        }
        $render = "<input type='text' name='" . $this->getName() . "' id='" . $this->getName() . "' size='" . $this->getSize() . "' maxlength='" . $this->getMaxlength() . "' value='" . $this->getValue() . "' " . $this->getExtra() . '>';
        $render .= '<br>' . _MD_TAG_TAG_DELIMITER . ': [' . implode('], [', $delimiters) . ']';

        return $render;
    }
}

class_alias(FormTag::class, 'TagFormTag');
