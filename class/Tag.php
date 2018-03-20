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
 * @since           1.00
 */

defined('XOOPS_ROOT_PATH') || die('Restricted access');

/**
 * Class Tag
 */
class Tag extends \XoopsObject
{
    /**
     * Tag constructor.
     */
    public function __construct()
    {
        $this->initVar('tag_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('tag_term', XOBJ_DTYPE_TXTBOX, '', true);
        $this->initVar('tag_status', XOBJ_DTYPE_INT, 0);
        $this->initVar('tag_count', XOBJ_DTYPE_INT, 0);
    }
}
