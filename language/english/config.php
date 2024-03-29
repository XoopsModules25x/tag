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
 * @copyright       {@link https://sourceforge.net/projects/xoops/ The XOOPS Project}
 * @license         {@link https://www.fsf.org/copyleft/gpl.html GNU public license}
 * @author          Taiwen Jiang (phppp or D.J.) <php_pp@hotmail.com>
 * @since           1.00
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

/*
 * Due to the difference of word boundary for different languages, delimiters also depend on languages
 * You need specify all possbile deimiters here, (",", ";", " ", "|") will be taken if no delimiter is set
 *
 * Tips:
 * For English sites, you can set as array(",", ";", " ", "|")
 * For Chinese sites, set as array(",", ";", " ", "|", "��")
 */
$GLOBALS['tag_delimiter'] = [',', ' ', '|', ';'];
