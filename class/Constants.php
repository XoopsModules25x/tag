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
 * Tag module
 *
 * Class to define Random Quote module constant values. These constants are
 * used to make the code easier to read and to keep values in central
 * location if they need to be changed.  These should not normally need
 * to be modified. If they are to be modified it is recommended to change
 * the value(s) before module installation. Additionally the module may not
 * work correctly if trying to upgrade if these values have been changed.
 *
 * @package      XoopsModules\Tag
 * @copyright    {@link http://sourceforge.net/projects/xoops/ The XOOPS Project}
 * @license      {@link https://www.gnu.org/licenses/gpl-2.0.html GNU Public License}
 * @author       ZySpec <zyspec@yahoo.com>
 * @since        2.33
 **/

/**
 * interface Constants
 */
interface Constants
{
    /**#@+
     * Constant definition
     */
    /**
     *  indicates a tag is active
     */
    public const STATUS_ACTIVE = 0;
    /**
     *  indicates a tag is inactive
     */
    public const STATUS_INACTIVE = 1;
    /**
     *  indicates a tag is to be deleted
     */
    public const STATUS_DELETE = -1;
    /**
     *  indicates inclusion of all tags in select
     */
    public const STATUS_ALL = -1;
    /**
     *  indicates default for ID for tags, modules, etc.
     */
    public const DEFAULT_ID = 0;
    /**
     *  indicates default display limit to show
     */
    public const DEFAULT_LIMIT = 10;
    /**
     *  indicates unlimitted number
     */
    public const UNLIMITED = 0;
    /**
     *  indicates starting point for searches, etc.
     */
    public const BEGINNING = 0;
    /**
     * no delay XOOPS redirect delay (in seconds)
     */
    public const REDIRECT_DELAY_NONE = 0;
    /**
     * short XOOPS redirect delay (in seconds)
     */
    public const REDIRECT_DELAY_SHORT = 1;
    /**
     * medium XOOPS redirect delay (in seconds)
     */
    public const REDIRECT_DELAY_MEDIUM = 3;
    /**
     * long XOOPS redirect delay (in seconds)
     */
    public const REDIRECT_DELAY_LONG = 7;
    /**
     * confirm not ok to take action
     */
    public const CONFIRM_NOT_OK = 0;
    /**
     * confirm ok to take action
     */
    public const CONFIRM_OK = 1;
    /**#@-*/
}
