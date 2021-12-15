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
 * Tag stats handler class.
 *
 * {@link XoopsPersistableObjectHandler}
 */
class StatsHandler extends \XoopsPersistableObjectHandler
{
    /**
     * StatsHandler constructor.
     */
    public function __construct(\XoopsDatabase $db = null)
    {
        parent::__construct($db, 'tag_stats', Stats::class, 'ts_id');
    }
}
