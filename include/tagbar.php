<?php declare(strict_types=1);
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
 * @copyright      {@link https://sourceforge.net/projects/xoops/ The XOOPS Project}
 * @license        {@link https://www.fsf.org/copyleft/gpl.html GNU public license}
 * @author         Taiwen Jiang <phppp@users.sourceforge.net>
 * @since          1.00
 */

use XoopsModules\Tag\{
    Tagbar
};

(defined('XOOPS_ROOT_PATH') && ($GLOBALS['xoopsModule'] instanceof \XoopsModule)) || exit('Restricted access');

/**
 * Display tag list
 *
 * @param int|array $tags  array of tag string
 *                         OR
 * @param int       $catid
 * @param int       $modid
 * @return array
 */
function tagBar($tags, $catid = 0, $modid = 0)
{
    $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
    trigger_error(__FUNCTION__ . " is deprecated, called from {$trace[0]['file']} line {$trace[0]['line']}");
    $GLOBALS['xoopsLogger']->addDeprecated(
        'Tag Module: ' . __FUNCTION__ . " function  is deprecated since Tag 2.3.5, please use 'Tag\Tagbar' class instead." . " Called from {$trace[0]['file']}line {$trace[0]['line']}"
    );

    $tagbar = new Tagbar();

    return $tagbar->getTagbar($tags, $catid, $modid);
}
