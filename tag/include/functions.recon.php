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
 * @copyright       The XOOPS project http://sourceforge.net/projects/xoops/
 * @license         http://www.fsf.org/copyleft/gpl.html GNU public license
 * @since           1.00
 * @author          Taiwen Jiang <phppp@users.sourceforge.net>
 * @version         $Id: functions.recon.php 12898 2014-12-08 22:05:21Z zyspec $
 * */

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

defined("TAG_FUNCTIONS_INI") || include __DIR__ . "/functions.ini.php";
define("TAG_FUNCTIONS_RECON_LOADED", TRUE);

IF (!defined("TAG_FUNCTIONS_RECON")):
define("TAG_FUNCTIONS_RECON", 1);

function tag_synchronization()
{
    $module_handler =& xoops_gethandler("module");
    $criteria = new CriteriaCompo(new Criteria("isactive", 1));
    $criteria->add(new Criteria("dirname", "('system', 'tag')", "NOT IN"));
    $modules_obj = $module_handler->getObjects($criteria, true);

    $link_handler =& xoops_getmodulehandler("link", "tag");
    $link_handler->deleteAll(new Criteria("tag_modid", "(" . implode(", ", array_keys($modules_obj)) . ")", "NOT IN"), true);

    foreach (array_keys($modules_obj) as $mid) {
        $dirname = $modules_obj[$mid]->getVar("dirname");
        if (!@include_once $GLOBALS['xoops']->path("/modules/{$dirname}/class/plugins/plugin.tag.php")) {
            if (!@include_once $GLOBALS['xoops']->path("/modules/{$dirname}/include/plugin.tag.php")) {
                if (!@include_once $GLOBALS['xoops']->path("/modules/tag/plugin/{$dirname}.php")) {
                    continue;
                }
            }
        }
        $func_tag = "{$dirname}_tag_synchronization";
        if (!function_exists($func_tag)) {
            continue;
        }
        $res = $func_tag($mid);
    }

    return tag_cleanOrphan();
//    return true;
}
/**
 *
 * Cleans orphans from dB table
 *
 * @return bool true successfully deleted all orphans, false otherwise
 */
function tag_cleanOrphan()
{
    $tag_handler =& xoops_getmodulehandler("tag", "tag");

    $success = true;
    /* clear item-tag links */
    $sql =  "DELETE FROM {$tag_handler->table_link}" .
            " WHERE ({$tag_handler->keyName} NOT IN ( SELECT DISTINCT {$tag_handler->keyName} FROM {$tag_handler->table}) )";
    $s1 = ($tag_handler->db->queryF($sql)) ? true : false;
    $success = $success && $s1;

    /* remove empty stats-tag links */
    $sql = "DELETE FROM {$tag_handler->table_stats} WHERE tag_count = 0";
    $s1 = ($tag_handler->db->queryF($sql)) ? true : false;
    $success = $success && $s1;

    /* clear stats-tag links */
    $sql =  "DELETE FROM {$tag_handler->table_stats}" .
            " WHERE ({$tag_handler->keyName} NOT IN ( SELECT DISTINCT {$tag_handler->keyName} FROM {$tag_handler->table}) )";
    $s1 = ($tag_handler->db->queryF($sql)) ? true : false;
    $success = $success && $s1;

    $sql =  "    DELETE FROM {$tag_handler->table_stats}" .
            "    WHERE NOT EXISTS ( SELECT * FROM {$tag_handler->table_link} " .
            "                       WHERE  {$tag_handler->table_link}.tag_modid={$tag_handler->table_stats}.tag_modid" .
            "                       AND  {$tag_handler->table_link}.tag_catid={$tag_handler->table_stats}.tag_catid" .
            "                     )";
    $s1 = ($tag_handler->db->queryF($sql)) ? true : false;
    $success = $success && $s1;

    /* clear empty tags */
    $sql =  "DELETE FROM {$tag_handler->table}" .
            " WHERE ({$tag_handler->keyName} NOT IN ( SELECT DISTINCT {$tag_handler->keyName} FROM {$tag_handler->table_link}) )";
    $s1 = ($tag_handler->db->queryF($sql)) ? true : false;
    $success = $success && $s1;

    return $success;
}

ENDIF;
