<?php declare(strict_types=1);

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
 * @copyright       {@link https://sourceforge.net/projects/xoops/ The XOOPS Project}
 * @license         {@link https://www.fsf.org/copyleft/gpl.html GNU public license}
 * @author          Taiwen Jiang <phppp@users.sourceforge.net>
 * @since           2.34
 */

/**
 * Class Stats
 */
class Stats extends \XoopsObject
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->initVar('ts_id', \XOBJ_DTYPE_INT, null, false);
        $this->initVar('tag_id', \XOBJ_DTYPE_INT, 0);
        $this->initVar('tag_modid', \XOBJ_DTYPE_INT, 0);
        $this->initVar('tag_catid', \XOBJ_DTYPE_INT, 0);
        $this->initVar('tag_count', \XOBJ_DTYPE_INT, 0);
    }
}
