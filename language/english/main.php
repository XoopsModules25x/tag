<?php
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
 * @author          Taiwen Jiang (phppp or D.J.) <php_pp@hotmail.com>
 * @since           1.00
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

define('_MD_TAG_TAGS', 'Tags');
define('_MD_TAG_TAG_ON', 'Search Tags On');
define('_MD_TAG_TAGVIEW', 'Items of Tag <strong>%1$s</strong> in %2$s');
define('_MD_TAG_TAGLIST', 'Tag List of <strong>%s</strong>');
define('_MD_TAG_JUMPTO', 'Jump to');
define('_MD_TAG_TAG_DELIMITER', 'Following delimiters are valid for multiple tags');
define('_MD_TAG_INVALID', 'Invalid query');

/**
 * Customize addons:
 * <ul>
 *    <li>key: like "google", nothing but only for "target" in anchor;</li>
 *    <li>title: link title;</li>
 *    <li>link: link prototype, %s for the urlencode'd term;</li>
 *    <li>function: optional, some sites might require_once different charset encoding, you can create your functions or use PHP functions like utf8_encode.
 *                  This is required by non-latin languages for technorati or flickr.
 *    </li>
 * </ul>
 */
$GLOBALS['_MD_TAG_ADDONS'] = [
    'google'   => [
        'title' => 'Google',
        'link'  => 'https://www.google.com/search?q=%s',
    ],
    'techno'   => [
        'title' => 'Technorati',
        'link'  => 'https://technorati.com/search/index.php?q=%s&context=search',
    ],
    'flickr'   => [
        'title'    => 'Flickr',
        'link'     => 'https://www.flickr.com/photos/tags/%s/',
        'function' => 'utf8_encode',
    ],
    'unsplash' => [
        'title' => 'Unsplash',
        'link'  => 'https://unsplash.com/s/photos/%s/',
    ],
    'pixabay'  => [
        'title'    => 'Pixabay',
        'link'     => 'https://pixabay.com/images/search/%s/',
        'function' => 'urlencode',
    ],
];
