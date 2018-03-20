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
 * @package        tag
 * @copyright      {@link http://sourceforge.net/projects/xoops/ The XOOPS Project}
 * @license        {@link http://www.fsf.org/copyleft/gpl.html GNU public license}
 * @author         Taiwen Jiang <phppp@users.sourceforge.net>
 * @since          1.00
 */

defined('XOOPS_ROOT_PATH') || die('Restricted access');

include __DIR__ . '/preloads/autoloader.php';

$moduleDirName = basename(__DIR__);

// ------------------- Informations ------------------- //
$modversion = [
    'version'             => 2.34,
    'module_status'       => 'RC-1',
    'release_date'        => '2017/07/01',
    'name'                => _MI_TAG_NAME,
    'description'         => _MI_TAG_DESC,
    'official'            => 0,
    //1 indicates official XOOPS module supported by XOOPS Dev Team, 0 means 3rd party supported
    'author'              => 'Taiwen Jiang <phppp@users.sourceforge.net>',
    'author_website_url'  => 'https://xoops.org',
    'author_website_name' => 'XOOPS',
    'credits'             => 'XOOPS Development Team, Trabis, Mamba',
    'license'             => 'GPL 2.0 or later',
    'license_url'         => 'www.gnu.org/licenses/gpl-2.0.html/',
    'help'                => 'page=help',
    // ------------------- Folders & Files -------------------
    'release_info'        => 'Changelog',
    'release_file'        => XOOPS_URL . "/modules/$moduleDirName/docs/changelog.txt",
    //
    'manual'              => 'link to manual file',
    'manual_file'         => XOOPS_URL . "/modules/$moduleDirName/docs/install.txt",
    // images
    'image'               => 'assets/images/logoModule.png',
    'iconsmall'           => 'assets/images/iconsmall.png',
    'iconbig'             => 'assets/images/iconbig.png',
    'dirname'             => $moduleDirName,
    //Frameworks
    //    'dirmoduleadmin'      => 'Frameworks/moduleclasses/moduleadmin',
    //    'sysicons16'          => 'Frameworks/moduleclasses/icons/16',
    //    'sysicons32'          => 'Frameworks/moduleclasses/icons/32',
    // Local path icons
    'modicons16'          => 'assets/images/icons/16',
    'modicons32'          => 'assets/images/icons/32',
    //About
    'demo_site_url'       => 'https://xoops.org',
    'demo_site_name'      => 'XOOPS Demo Site',
    'support_url'         => 'https://xoops.org/modules/newbb/viewforum.php?forum=28/',
    'support_name'        => 'Support Forum',
    'module_website_url'  => 'www.xoops.org',
    'module_website_name' => 'XOOPS Project',
    // ------------------- Min Requirements -------------
    'min_php'             => '5.5',
    'min_xoops'           => '2.5.9',
    'min_admin'           => '1.2',
    'min_db'              => ['mysql' => '5.5'],
    // ------------------- Admin Menu -------------------
    'system_menu'         => 1,
    'hasAdmin'            => 1,
    'adminindex'          => 'admin/index.php',
    'adminmenu'           => 'admin/menu.php',
    // ------------------- Main Menu ---------------------
    'hasMain'             => 1,
    'sub'                 => [
        [
            'name' => _MI_TAG_VIEW_SEARCH,
            'url'  => 'index.php'
        ],
    ],

    // ------------------- Install/Update -------------------
    'onInstall'           => 'include/oninstall.php',
    'onUpdate'            => 'include/onupdate.php',
    'onUninstall'         => 'include/onuninstall.php',
    // -------------------  PayPal ---------------------------
    'paypal'              => [
        'business'      => 'foundation@xoops.org',
        'item_name'     => 'Donation : ' . _MI_TAG_NAME,
        'amount'        => 0,
        'currency_code' => 'USD'
    ],
    // ------------------- Search ---------------------------
    'hasSearch'           => 1,
    'search'              => [
        'file' => 'include/search.inc.php',
        'func' => 'tag_search'
    ],
    // ------------------- Comments -------------------------
    'hasComments'         => 0,

    // ------------------- Notification ----------------------
    'hasNotification'     => 0,

    // ------------------- Mysql -----------------------------
    'sqlfile'             => ['mysql' => 'sql/mysql.sql'],
    // ------------------- Tables ----------------------------
    'tables'              => [
        $moduleDirName . '_' . 'tag',
        $moduleDirName . '_' . 'link',
        $moduleDirName . '_' . 'stats',
    ],
];

// Use smarty
$modversion['use_smarty'] = 1;

// ------------------- Templates ------------------- //
$modversion['templates'] = [
    ['file' => 'tag_index.tpl', 'description' => '_MI_TAG_INDEX_TPL_DESC'],
    ['file' => 'tag_list.tpl', 'description' => _MI_TAG_INDEX_TPL_LIST_DESC],
    ['file' => 'tag_view.tpl', 'description' => _MI_TAG_INDEX_TPL_VIEW_DESC],
    ['file' => 'tag_bar.tpl', 'description' => _MI_TAG_INDEX_TPL_BAR_DESC],
    ['file' => "admin/{$moduleDirName}_admin_about.tpl", 'description' => _MI_TAG_INDEX_ADMINTPL_ABOUT_DESC],
    ['file' => "admin/{$moduleDirName}_admin_help.tpl", 'description' => _MI_TAG_INDEX_ADMINTPL_HELP_DESC]
];

// Blocks
$modversion['blocks'] = [
    /*
     * $options:
     *                    $options[0] - number of tags to display
     *                    $options[1] - time duration, in days, 0 for all the time
     *                    $options[2] - max font size (px or %)
     *                    $options[3] - min font size (px or %)
     */
    [
        'file'        => 'block.php',
        'name'        => _MI_TAG_BLOCK_CLOUD,
        'description' => _MI_TAG_BLOCK_CLOUD_DESC,
        'show_func'   => 'tag_block_cloud_show',
        'edit_func'   => 'tag_block_cloud_edit',
        'options'     => '100|0|150|80',
        'template'    => 'tag_block_cloud.tpl'
    ],
    /*
     * $options:
     *                    $options[0] - number of tags to display
     *                    $options[1] - time duration, in days, 0 for all the time
     *                    $options[2] - sort: a - alphabet; c - count; t - time
     */
    [
        'file'        => 'block.php',
        'name'        => _MI_TAG_BLOCK_TOP,
        'description' => _MI_TAG_BLOCK_TOP_DESC,
        'show_func'   => 'tag_block_top_show',
        'edit_func'   => 'tag_block_top_edit',
        'options'     => '50|30|a',
        'template'    => 'tag_block_top.tpl'
    ],

    /*
     * $options for cumulus:
     *                     $options[0] - number of tags to display
     *                     $options[1] - time duration
     *                     $options[2] - max font size (px or %)
     *                     $options[3] - min font size (px or %)
     *                     $options[4] - cumulus_flash_width
     *                     $options[5] - cumulus_flash_height
     *                     $options[6] - cumulus_flash_background
     *                     $options[7] - cumulus_flash_transparency
     *                     $options[8] - cumulus_flash_min_font_color
     *                     $options[9] - cumulus_flash_max_font_color
     *                    $options[10] - cumulus_flash_hicolor
     *                    $options[11] - cumulus_flash_speed
     */
    [
        'file'        => 'block.php',
        'name'        => _MI_TAG_BLOCK_CUMULUS,
        'description' => _MI_TAG_BLOCK_CUMULUS_DESC,
        'show_func'   => 'tag_block_cumulus_show',
        'edit_func'   => 'tag_block_cumulus_edit',
        'options'     => '100|0|24|12|160|140|#ffffff|0|#000000|#003300|#00ff00|100',
        'template'    => 'tag_block_cumulus.tpl'
    ]
];

// Configs
$modversion['config'] = [
    [
        'name'        => 'do_urw',
        'title'       => '_MI_TAG_DOURLREWRITE',
        'description' => '_MI_TAG_DOURLREWRITE_DESC',
        'formtype'    => 'yesno',
        'valuetype'   => 'int',
        'default'     => in_array(PHP_SAPI, ['apache', 'apache2handler'])
    ],

    [
        'name'        => 'items_perpage',
        'title'       => '_MI_TAG_ITEMSPERPAGE',
        'description' => '_MI_TAG_ITEMSPERPAGE_DESC',
        'formtype'    => 'textbox',
        'valuetype'   => 'int',
        'default'     => 10
    ],

    [
        'name'        => 'limit_tag_list',
        'title'       => '_MI_TAG_LIMITPERLIST',
        'description' => '_MI_TAG_LIMITPERLIST_DESC',
        'formtype'    => 'textbox',
        'valuetype'   => 'int',
        'default'     => 10
    ],

    [
        'name'        => 'limit_cloud_list',
        'title'       => '_MI_TAG_LIMITPERCLOUD',
        'description' => '_MI_TAG_LIMITPERCLOUD_DESC',
        'formtype'    => 'textbox',
        'valuetype'   => 'int',
        'default'     => 100
    ]
];
