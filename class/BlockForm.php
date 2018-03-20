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
 * @subpackage      class
 * @copyright       {@link http://sourceforge.net/projects/xoops/ The XOOPS Project}
 * @license         {@link http://www.fsf.org/copyleft/gpl.html GNU public license}
 * @author          Taiwen Jiang <phppp@users.sourceforge.net>
 * @author          susheng yang <ezskyyoung@gmail.com>
 * @since           2.33
 */

use XoopsModules\Tag;

require_once $GLOBALS['xoops']->path('/class/xoopsformloader.php');

/**
 * Class BlockForm
 */
class BlockForm extends \XoopsForm
{

    /**
     * create HTML to output the form as a table
     *
     * @return string HTML div containing element
     */
    public function render()
    {
        //        $ele_name = $this->getName();
        $ret    = "<div>\n";
        $hidden = '';
        foreach ($this->getElements() as $ele) {
            if (!is_object($ele)) {
                $ret .= $ele;
            } elseif (!$ele->isHidden()) {
                if ('' != $caption = $ele->getCaption()) {
                    $ret .= "<div class='xoops-form-element-caption" . ($ele->isRequired() ? '-required' : '') . "'>\n" . "  <span class='caption-text'>{$caption}</span>\n" . "  <span class='caption-marker'>*</span>\n" . "</div>\n";
                }

                $ret .= "<div style='margin:5px 0 8px 0; '>" . $ele->render() . "</div>\n";
            } else {
                $hidden .= $ele->render();
            }
        }
        $ret .= "</div>\n";
        $ret .= $this->renderValidationJS(true);

        return $ret;
    }
}
