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
 * @author          Taiwen Jiang <phppp@users.sourceforge.net>
 * @since           1.00
 */

use XoopsModules\Tag;
use XoopsModules\Tag\Constants;

defined('XOOPS_ROOT_PATH') || die('Restricted access');

include $GLOBALS['xoops']->path('/modules/tag/include/vars.php');
//require_once $GLOBALS['xoops']->path('/modules/tag/include/functions.php');

/** @var Tag\Helper $helper */
$helper = Tag\Helper::getInstance();

$helper->loadLanguage('blocks');
xoops_load('constants', 'tag');

/**#@+
 * Function to display tag cloud
 *
 * Developer guide:
 * <ul>
 *    <li>Build your tag_block_cloud_show function, for example newbb_block_tag_cloud_show;</li>
 *    <li>Call the tag_block_cloud_show in your defined block function:<br>
 *        <code>
 *            function newbb_block_tag_cloud_show($options) {
 *                $catid        = $options[4];    // Not used by newbb, Only for demonstration
 *                if (!@require_once $GLOBALS['xoops']->path("/modules/tag/blocks/block.php")) {
 *                    return null;
 *                }
 *                $block_content = tag_block_cloud_show($options, "newbb", $catid);
 *                return $block_content;
 *            }
 *        </code>
 *    </li>
 *    <li>Build your tag_block_cloud_edit function, for example newbb_block_tag_cloud_edit;</li>
 *    <li>Call the tag_block_cloud_edit in your defined block function:<br>
 *        <code>
 *            function newbb_block_tag_cloud_edit($options) {
 *                if (!@require_once $GLOBALS['xoops']->path("/modules/tag/blocks/block.php")) {
 *                    return null;
 *                }
 *                $form = tag_block_cloud_edit($options);
 *                $form .= $CODE_FOR_GET_CATID;    // Not used by newbb, Only for demonstration
 *                return $form;
 *            }
 *        </code>
 *    </li>
 *    <li>Create your tag_block_cloud template, for example newbb_block_tag_cloud.tpl;</li>
 *    <li>Include tag_block_cloud template in your created block template:<br>
 *        <code>
 *            <{include file="db:tag_block_cloud.tpl"}>
 *        </code>
 *    </li>
 * </ul>
 *
 * {@link Tag}
 *
 * @param    array $options :
 *                          $options[0] - number of tags to display
 *                          $options[1] - time duration, in days, 0 for all the time
 *                          $options[2] - max font size (px or %)
 *                          $options[3] - min font size (px or %)
 * @param string   $dirname
 * @param int      $catid
 * @return array
 */
function tag_block_cloud_show($options, $dirname = '', $catid = 0)
{
    global $xoTheme;
    /** @var xos_opal_Theme $xoTheme */
    $xoTheme->addStylesheet($GLOBALS['xoops']->url('www/modules/tag/assets/css/style.css'));

    if (empty($dirname)) {
        $modid = 0;
    } elseif (isset($GLOBALS['xoopsModule']) && ($GLOBALS['xoopsModule'] instanceof XoopsModule)
              && ($GLOBALS['xoopsModule']->getVar('dirname') == $dirname)) {
        $modid = $GLOBALS['xoopsModule']->getVar('mid');
    } else {
        /** @var XoopsModuleHandler $moduleHandler */
        $moduleHandler = xoops_getHandler('module');
        $module        = $moduleHandler->getByDirname($dirname);
        $modid         = $module->getVar('mid');
    }

    $block = [];
    /** @var Tag\TagHandler $tagHandler */
    $tagHandler = Tag\Helper::getInstance()->getHandler('Tag'); // xoops_getModuleHandler('tag', 'tag');
    tag_define_url_delimiter();

    $criteria = new \CriteriaCompo();
    $criteria->setSort('count');
    $criteria->setOrder('DESC');
    $criteria->setLimit($options[0]);
    $criteria->add(new \Criteria('o.tag_status', Constants::STATUS_ACTIVE));
    if (!empty($modid)) {
        $criteria->add(new \Criteria('l.tag_modid', $modid));
        if ($catid >= Constants::DEFAULT_ID) {
            $criteria->add(new \Criteria('l.tag_catid', $catid));
        }
    }
    if (!$tags =& $tagHandler->getByLimit(0, 0, $criteria, null, empty($options[1]))) {
        return $block;
    }

    $count_max = 0;
    $count_min = 0;
    $tags_term = [];
    foreach (array_keys($tags) as $key) {
        $count_max   = max($count_max, $tags[$key]['count']); // set counter to the max tag count
        $count_min   = min(0, $count_min, $tags[$key]['count']); //set counter to the minimum for tag count
        $tags_term[] = mb_strtolower($tags[$key]['term']);
    }
    if (!empty($tags_term)) {
        array_multisort($tags_term, SORT_ASC, $tags);
    }
    $count_interval = $count_max - $count_min;
    $level_limit    = 5;

    $font_max   = $options[2];
    $font_min   = $options[3];
    $font_ratio = $count_interval ? ($font_max - $font_min) / $count_interval : 1;

    $tags_data = [];
    foreach (array_keys($tags) as $key) {
        $tags_data[] = [
            'id'    => $tags[$key]['id'],
            'font'  => $count_interval ? floor(($tags[$key]['count'] - $count_min) * $font_ratio + $font_min) : 100,
            'level' => empty($count_max) ? 0 : floor(($tags[$key]['count'] - $count_min) * $level_limit / $count_max),
            'term'  => urlencode($tags[$key]['term']),
            'title' => htmlspecialchars($tags[$key]['term']),
            'count' => $tags[$key]['count']
        ];
    }
    unset($tags, $tags_term);

    $block['tags']        = $tags_data;
    $block['tag_dirname'] = 'tag';
    if (!empty($modid)) {
        /** @var XoopsModuleHandler $moduleHandler */
        $moduleHandler = xoops_getHandler('module');
        if ($module_obj = $moduleHandler->get($modid)) {
            $block['tag_dirname'] = $module_obj->getVar('dirname');
        }
    }

    return $block;
}

/**
 * @param $options
 * @return string
 */
function tag_block_cloud_edit($options)
{
    $form = _MB_TAG_ITEMS . ":&nbsp;&nbsp;<input type='number' name='options[0]' value='{$options[0]}' min='0' ><br>\n";
    $form .= _MB_TAG_TIME_DURATION . ":&nbsp;&nbsp;<input type='number' name='options[1]' value='{$options[1]}' min='0' ><br>\n";
    $form .= _MB_TAG_FONTSIZE_MAX . ":&nbsp;&nbsp;<input type='number' name='options[2]' value='{$options[2]}' min='0' ><br>\n";
    $form .= _MB_TAG_FONTSIZE_MIN . ":&nbsp;&nbsp;<input type='number' name='options[3]' value='{$options[3]}' min='0' ><br>\n";

    return $form;
}

/**#@+
 * Function to display top tag list
 *
 * Developer guide:
 * <ul>
 *    <li>Build your tag_block_top_show function, for example newbb_block_tag_top_show;</li>
 *    <li>Call the tag_block_top_show in your defined block function:<br>
 *        <code>
 *            function newbb_block_tag_top_show($options) {
 *                $catid        = $options[3];    // Not used by newbb, Only for demonstration
 *                if (!@require_once $GLOBALS['xoops']->path("/modules/tag/blocks/block.php")) {
 *                    return null;
 *                }
 *                $block_content = tag_block_top_show($options, "newbb", $catid);
 *                return $block_content;
 *            }
 *        </code>
 *    </li>
 *    <li>Build your tag_block_top_edit function, for example newbb_block_tag_top_edit;</li>
 *    <li>Call the tag_block_top_edit in your defined block function:<br>
 *        <code>
 *            function newbb_block_tag_top_edit($options) {
 *                if (!@require_once $GLOBALS['xoops']->path("/modules/tag/blocks/block.php")) {
 *                    return null;
 *                }
 *                $form = tag_block_cloud_edit($options);
 *                $form .= $CODE_FOR_GET_CATID;    // Not used by newbb, Only for demonstration
 *                return $form;
 *            }
 *        </code>
 *    </li>
 *    <li>Create your tag_block_top template, for example newbb_block_tag_top.tpl;</li>
 *    <li>Include tag_block_top template in your created block template:<br>
 *        <code>
 *            <{include file="db:tag_block_top.tpl"}>
 *        </code>
 *    </li>
 * </ul>
 *
 * {@link Tag}
 *
 * @param    array $options :
 *                          $options[0] - number of tags to display
 *                          $options[1] - time duration, in days, 0 for all the time
 *                          $options[2] - sort: a - alphabet; c - count; t - time
 * @param string   $dirname
 * @param int      $catid
 * @return array
 */
function tag_block_top_show($options, $dirname = '', $catid = 0)
{
    if (empty($dirname)) {
        $modid = 0;
    } elseif (isset($GLOBALS['xoopsModule']) && ($GLOBALS['xoopsModule'] instanceof XoopsModule)
              && $GLOBALS['xoopsModule']->getVar('dirname') == $dirname) {
        $modid = $GLOBALS['xoopsModule']->getVar('mid');
    } else {
        /** @var XoopsModuleHandler $moduleHandler */
        $moduleHandler = xoops_getHandler('module');
        $module        = $moduleHandler->getByDirname($dirname);
        $modid         = $module->getVar('mid');
    }

    $block = [];

    /** @var Tag\TagHandler $tagHandler */
    $tagHandler = \XoopsModules\Tag\Helper::getInstance()->getHandler('Tag'); // xoops_getModuleHandler('tag', 'tag');
    tag_define_url_delimiter();

    $criteria = new \CriteriaCompo();
    $sort     = '';
    if (isset($options[2])) {
        $sort = (('a' === $options[2]) || ('alphabet' === $options[2])) ? 'count' : $options[2];
    }
    //    $criteria->setSort("count");
    $criteria->setSort($sort);
    $criteria->setOrder('DESC');
    $criteria->setLimit((int)$options[0]);
    $criteria->add(new \Criteria('o.tag_status', Constants::STATUS_ACTIVE));
    if (!empty($options[1])) {
        $criteria->add(new \Criteria('l.tag_time', time() - (float)$options[1] * 24 * 3600, '>'));
    }
    if (!empty($modid)) {
        $criteria->add(new \Criteria('l.tag_modid', $modid));
        $catid = (int)$catid;
        if ($catid >= 0) {
            $criteria->add(new \Criteria('l.tag_catid', $catid));
        }
    }
    if (!$tags =& $tagHandler->getByLimit(0, 0, $criteria, null, empty($options[1]))) {
        return $block;
    }

    $count_max = 0;
    $count_min = 0;
    $tags_sort = [];
    foreach (array_keys($tags) as $key) {
        $count_max = max($count_max, $tags[$key]['count']); // set counter to the max tag count
        $count_min = min(0, $count_min, $tags[$key]['count']); //set counter to the minimum for tag count
        if (('a' === $options[2]) || ('alphabet' === $options[2])) {
            $tags_sort[] = mb_strtolower($tags[$key]['term']);
        }
    }
    $count_interval = $count_max - $count_min;

    /*
    $font_max = $options[1];
    $font_min = $options[2];
    $font_ratio = ($count_interval) ? ($font_max - $font_min) / $count_interval : 1;
    */
    if (!empty($tags_sort)) {
        array_multisort($tags_sort, SORT_ASC, $tags);
    }

    $tags_data = [];
    foreach (array_keys($tags) as $key) {
        $tags_data[] = [
            'id'    => $tags[$key]['id'],
            'term'  => $tags[$key]['term'],
            'count' => $tags[$key]['count'],
            //                          "level" => ($tags[$key]["count"] - $count_min) * $font_ratio + $font_min,
        ];
    }
    unset($tags, $tags_term);

    $block['tags']        = $tags_data;
    $block['tag_dirname'] = 'tag';
    if (!empty($modid)) {
        /** @var XoopsModuleHandler $moduleHandler */
        $moduleHandler = xoops_getHandler('module');
        if ($module_obj = $moduleHandler->get($modid)) {
            $block['tag_dirname'] = $module_obj->getVar('dirname');
        }
    }

    return $block;
}

/**
 * @param $options
 * @return string
 */
function tag_block_top_edit($options)
{
    $form = _MB_TAG_ITEMS . ":&nbsp;&nbsp;<input type='number' name='options[0]' value='{$options[0]}' min='0' ><br>\n";
    $form .= _MB_TAG_TIME_DURATION . ":&nbsp;&nbsp;<input type='number' name='options[1]' value='{$options[1]}' min='0' ><br>\n";
    $form .= _MB_TAG_SORT . ":&nbsp;&nbsp;<select name='options[2]'>\n";
    $form .= "<option value='a'";
    if ('a' === $options[2]) {
        $form .= ' selected ';
    }
    $form .= '>' . _MB_TAG_ALPHABET . "</option>\n";
    $form .= "<option value='c'";
    if ('c' === $options[2]) {
        $form .= ' selected ';
    }
    $form .= '>' . _MB_TAG_COUNT . "</option>\n";
    $form .= "<option value='t'";
    if ('t' === $options[2]) {
        $form .= ' selected ';
    }
    $form .= '>' . _MB_TAG_TIME . "</option>\n";
    $form .= "</select><br>\n";

    return $form;
}

/**
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
/**
 *
 * Prepare output for Cumulus block display
 *
 * @param              array    {
 * @param  null|string $dirname null for all modules, $dirname for specific module
 * @param  int         $catid   category id (only used if $dirname is set)
 * @return array
 */
function tag_block_cumulus_show(array $options, $dirname = '', $catid = 0)
{
    if (null === $dirname) {
        $modid = 0;
    } elseif (isset($GLOBALS['xoopsModule']) && ($GLOBALS['xoopsModule'] instanceof XoopsModule)
              && ($GLOBALS['xoopsModule']->getVar('dirname') == $dirname)) {
        $modid = $GLOBALS['xoopsModule']->getVar('mid');
    } else {
        //        /** @var XoopsModuleHandler $moduleHandler */
        //        $moduleHandler = xoops_getHandler('module');
        //        $module        = $moduleHandler->getByDirname($dirname);
        //        $modid         = $module->getVar('mid');

        /** @var Tag\Helper $helper */
        $helper = Tag\Helper::getInstance();
        $module = $helper->getModule();
//        $modid  = $module->getVar('mid');
        $modid  = $helper->getModule()->getVar('mid');
    }

    $block      = [];
    $tagHandler = \XoopsModules\Tag\Helper::getInstance()->getHandler('Tag'); // xoops_getModuleHandler('tag', 'tag');
    tag_define_url_delimiter();

    $criteria = new \CriteriaCompo();
    $criteria->setSort('count');
    $criteria->setOrder('DESC');
    $criteria->setLimit($options[0]);
    $criteria->add(new \Criteria('o.tag_status', Constants::STATUS_ACTIVE));
    if (!empty($modid)) {
        $criteria->add(new \Criteria('l.tag_modid', $modid));
        if ($catid >= 0) {
            $criteria->add(new \Criteria('l.tag_catid', $catid));
        }
    }
    if (!$tags = $tagHandler->getByLimit(0, 0, $criteria, null, empty($options[1]))) {
        return $block;
    }

    $count_max = 0;
    $count_min = 0;
    $tags_term = [];
    foreach (array_keys($tags) as $key) {
        $count_max   = max(0, $tags[$key]['count'], $count_max);
        $count_min   = min(0, $tags[$key]['count'], $count_min);
        $tags_term[] = mb_strtolower($tags[$key]['term']);
    }
    if (!empty($tags_term)) {
        array_multisort($tags_term, SORT_ASC, $tags);
    }
    $count_interval = $count_max - $count_min;
    $level_limit    = 5;

    $font_max   = $options[2];
    $font_min   = $options[3];
    $font_ratio = $count_interval ? ($font_max - $font_min) / $count_interval : 1;

    $tags_data = [];
    foreach (array_keys($tags) as $key) {
        $tags_data[] = [
            'id'    => $tags[$key]['id'],
            'font'  => $count_interval ? floor(($tags[$key]['count'] - $count_min) * $font_ratio + $font_min) : 12,
            'level' => empty($count_max) ? 0 : floor(($tags[$key]['count'] - $count_min) * $level_limit / $count_max),
            'term'  => urlencode($tags[$key]['term']),
            'title' => htmlspecialchars($tags[$key]['term']),
            'count' => $tags[$key]['count']
        ];
    }
    unset($tags, $tags_term);
    $block['tags'] = $tags_data;

    $block['tag_dirname'] = 'tag';
    if (!empty($modid)) {
        /** @var XoopsModuleHandler $moduleHandler */
        $moduleHandler = xoops_getHandler('module');
        if ($module_obj = $moduleHandler->get($modid)) {
            $block['tag_dirname'] = $module_obj->getVar('dirname');
        }
    }
    $flash_params = [
        'flash_url'  => $GLOBALS['xoops']->url('www/modules/tag/assets/cumulus.swf'),
        'width'      => (int)$options[4],
        'height'     => (int)$options[5],
        'background' => preg_replace_callback('/(#)/i', function ($m) {
            return '';
        }, $options[6]),
        'color'      => '0x' . preg_replace_callback('/(#)/i', function ($m) {
            return '';
        }, $options[8]),
        'hicolor'    => '0x' . preg_replace_callback('/(#)/i', function ($m) {
            return '';
        }, $options[9]),
        'tcolor'     => '0x' . preg_replace_callback('/(#)/i', function ($m) {
            return '';
        }, $options[8]),
        'tcolor2'    => '0x' . preg_replace_callback('/(#)/i', function ($m) {
            return '';
        }, $options[10]),
        'speed'      => (int)$options[11]
    ];

    $output    = '<tags>';
    $xoops_url = $GLOBALS['xoops']->url('www');
    foreach ($tags_data as $term) {
        // assign font size
        $output .= <<<EOT
<a href='{$xoops_url}/modules/tag/view.tag.php?{$term['term']}' style='{$term['font']}'>{$term['title']}</a>
EOT;
    }
    $output                               .= '</tags>';
    $flash_params['tags_formatted_flash'] = urlencode($output);
    if (1 == $options[7]) {
        $flash_params['transparency'] = 'widget_so.addParam("wmode", "transparent");';
    }
    $block['flash_params'] = $flash_params;

    return $block;
}

/**
 *
 * Block function to render Cumulus Preferences form
 *
 * @param array $options module config block options
 *
 * @return string html render of form
 */
function tag_block_cumulus_edit($options)
{
    require_once $GLOBALS['xoops']->path('/class/xoopsformloader.php');
//    xoops_load('blockform', 'tag');
//    xoops_load('formvalidatedinput', 'tag');

    $form = new Tag\BlockForm('', '', '');
    $form->addElement(new Tag\FormValidatedInput(_MB_TAG_ITEMS, 'options[0]', 25, 25, $options[0], 'number'));
    $form->addElement(new Tag\FormValidatedInput(_MB_TAG_TIME_DURATION, 'options[1]', 25, 25, $options[1], 'number'));
    $form->addElement(new Tag\FormValidatedInput(_MB_TAG_FONTSIZE_MAX, 'options[2]', 25, 25, $options[2], 'number'));
    $form->addElement(new Tag\FormValidatedInput(_MB_TAG_FONTSIZE_MIN, 'options[3]', 25, 25, $options[3], 'number'));
    $form->addElement(new Tag\FormValidatedInput(_MB_TAG_FLASH_WIDTH, 'options[4]', 25, 25, $options[4], 'number'));
    $form->addElement(new Tag\FormValidatedInput(_MB_TAG_FLASH_HEIGHT, 'options[5]', 25, 25, $options[5], 'number'));
    $form->addElement(new \XoopsFormColorPicker(_MB_TAG_FLASH_BACKGROUND, 'options[6]', $options[6]));
    $form_cumulus_flash_transparency = new \XoopsFormSelect(_MB_TAG_FLASH_TRANSPARENCY, 'options[7]', $options[7]);
    $form_cumulus_flash_transparency->addOption(0, _NONE);
    $form_cumulus_flash_transparency->addOption(1, _MB_TAG_FLASH_TRANSPARENT);
    $form->addElement($form_cumulus_flash_transparency);
    $form->addElement(new \XoopsFormColorPicker(_MB_TAG_FLASH_MINFONTCOLOR, 'options[8]', $options[8]));
    $form->addElement(new \XoopsFormColorPicker(_MB_TAG_FLASH_MAXFONTCOLOR, 'options[9]', $options[9]));
    $form->addElement(new \XoopsFormColorPicker(_MB_TAG_FLASH_HILIGHTFONTCOLOR, 'options[10]', $options[10]));
    $form->addElement(new Tag\FormValidatedInput(_MB_TAG_FLASH_SPEED, 'options[11]', 25, 25, $options[11], 'number'));

    return $form->render();
}
