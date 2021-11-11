<?php

namespace XoopsModules\Tag;

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

use XoopsModules\Tag;
use XoopsModules\Tag\Utility;

\xoops_load('xoopsformtext');

/**
 * Class FormTag
 */
class FormTag extends \XoopsFormText
{
    /**
     * TagFormTag constructor.
     * @param string $name      "name" attribute
     * @param int    $size      size of input box
     * @param int    $maxlength Maximum length of text
     * @param null   $value     Initial text or itemid
     * @param int    $catid     category id (applicable if $value is itemid)
     */
    public function __construct($name, int $size, $maxlength, $value = null, $catid = 0)
    {
        $helper = \XoopsModules\Tag\Helper::getInstance();
        require_once $helper->path('include/vars.php');
        $helper->loadLanguage('main');

        $value = empty($value) ? '' : $value;

        if (!empty($value) && \is_numeric($value) && ($GLOBALS['xoopsModule'] instanceof \XoopsModule)) {
            $modid = $GLOBALS['xoopsModule']->getVar('mid');
            /** @var \XoopsModules\Tag\TagHandler $tagHandler */
            $tagHandler = $helper->getHandler('Tag');
            $tags       = $tagHandler->getByItem($value, $modid, $catid);
            if ($tags) {
                $value = \htmlspecialchars(\implode(', ', $tags), \ENT_QUOTES | \ENT_HTML5);
            }
        }
        $caption = \_MD_TAG_TAGS;
        parent::__construct($caption, $name, $size, $maxlength, $value);
    }

    /**
     * Prepare HTML for output
     *
     * @return string HTML
     */
    public function render()
    {
        $delimiters = Utility::tag_get_delimiter();
        foreach (\array_keys($delimiters) as $key) {
            $delimiters[$key] = "<em style='font-weight: bold; color: #ff0000; font-style: normal;'>" . \htmlspecialchars($delimiters[$key], \ENT_QUOTES | \ENT_HTML5) . '</em>';
        }
        $class  = (false !== $this->getClass()) ? "class='" . $this->getClass() . "' " : '';
        $render = "<input type='text' name='" . $this->getName() . "' id='" . $this->getName() . "' size='" . $this->getSize() . "' maxlength='" . $this->getMaxlength() . "' value='" . $this->getValue() . "' " . $class . $this->getExtra() . '>' . \_MD_TAG_TAG_DELIMITER . ': [' . \implode(
                '], [',
                $delimiters
            ) . ']';

        return $render;
    }
}

\class_alias(FormTag::class, 'TagFormTag');
