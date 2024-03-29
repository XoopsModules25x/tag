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
 * @author          Taiwen Jiang <phppp@users.sourceforge.net>
 * @since           1.00
 *
 * @todo            There should be an option for admin to choose a category to store subcategories and articles
 */

// plugin guide:
/*
 * Add customized configs, variables or functions
 */

/*
 * Due to the difference of word boundary for different languages, delimiters also depend on languages
 * You need specify all possbile deimiters here, (",", ";", " ", "|") will be taken if no delimiter is set
 *
 * Tips:
 * For English sites, you can set as array(",", ";", " ", "|")
 * For Chinese sites, you can set as array(",", ";", " ", "|", "��")
 *
 */
$customConfig = [
    'tag_delimiter' => [',', ' ', '|'],
    'limit_tag'     => 100,
    'font_max'      => 150,
    'font_min'      => 80,
];

return $customConfig;
