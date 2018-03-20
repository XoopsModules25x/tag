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
 * @copyright       XOOPS Project (https://xoops.org)
 * @license         http://www.fsf.org/copyleft/gpl.html GNU public license
 * @since           1.00
 * @author          Taiwen Jiang <phppp@users.sourceforge.net>
 */

use XoopsModules\Tag;

defined('XOOPS_ROOT_PATH') || die('Restricted access');

defined('TAG_FUNCTIONS_INI') || require_once __DIR__ . '/functions.ini.php';
define('TAG_FUNCTIONS_RECON_LOADED', true);

if (!defined('TAG_FUNCTIONS_RECON')):
    define('TAG_FUNCTIONS_RECON', 1);

    /**
     * @return bool
     */
    function tag_synchronization()
    {
        /** @var \XoopsModuleHandler $moduleHandler */
        $moduleHandler = xoops_getHandler('module');
        $criteria      = new \CriteriaCompo(new \Criteria('isactive', 1));
        $criteria->add(new \Criteria('dirname', "('system', 'tag')", 'NOT IN'));
        $modules_obj = $moduleHandler->getObjects($criteria, true);

        /** @var Tag\LinkHandler $linkHandler */
        $linkHandler = \XoopsModules\Tag\Helper::getInstance()->getHandler('Link'); //@var \XoopsModules\Tag\Handler $tagHandler
        $linkHandler->deleteAll(new \Criteria('tag_modid', '(' . implode(', ', array_keys($modules_obj)) . ')', 'NOT IN'), true);

        foreach (array_keys($modules_obj) as $mid) {
            $dirname = $modules_obj[$mid]->getVar('dirname');
            if (!@require_once $GLOBALS['xoops']->path("/modules/{$dirname}/class/plugins/plugin.tag.php")) {
                if (!@require_once $GLOBALS['xoops']->path("/modules/{$dirname}/include/plugin.tag.php")) {
                    if (!@require_once $GLOBALS['xoops']->path("/modules/tag/plugin/{$dirname}.php")) {
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
        /** @var \XoopsModules\Tag\Handler $tagHandler */
        $tagHandler = \XoopsModules\Tag\Helper::getInstance()->getHandler('Tag'); // xoops_getModuleHandler('tag', 'tag');

        $success = true;
        /* clear item-tag links */
        $sql     = "DELETE FROM {$tagHandler->table_link}" . " WHERE ({$tagHandler->keyName} NOT IN ( SELECT DISTINCT {$tagHandler->keyName} FROM {$tagHandler->table}) )";
        $s1      = $tagHandler->db->queryF($sql) ? true : false;
        $success = $success && $s1;

        /* remove empty stats-tag links */
        $sql     = "DELETE FROM {$tagHandler->table_stats} WHERE tag_count = 0";
        $s1      = $tagHandler->db->queryF($sql) ? true : false;
        $success = $success && $s1;

        /* clear stats-tag links */
        $sql     = "DELETE FROM {$tagHandler->table_stats}" . " WHERE ({$tagHandler->keyName} NOT IN ( SELECT DISTINCT {$tagHandler->keyName} FROM {$tagHandler->table}) )";
        $s1      = $tagHandler->db->queryF($sql) ? true : false;
        $success = $success && $s1;

        $sql     = "    DELETE FROM {$tagHandler->table_stats}"
                   . "    WHERE NOT EXISTS ( SELECT * FROM {$tagHandler->table_link} "
                   . "                       WHERE  {$tagHandler->table_link}.tag_modid={$tagHandler->table_stats}.tag_modid"
                   . "                       AND  {$tagHandler->table_link}.tag_catid={$tagHandler->table_stats}.tag_catid"
                   . '                     )';
        $s1      = $tagHandler->db->queryF($sql) ? true : false;
        $success = $success && $s1;

        /* clear empty tags */
        $sql     = "DELETE FROM {$tagHandler->table}" . " WHERE ({$tagHandler->keyName} NOT IN ( SELECT DISTINCT {$tagHandler->keyName} FROM {$tagHandler->table_link}) )";
        $s1      = $tagHandler->db->queryF($sql) ? true : false;
        $success = $success && $s1;

        return $success;
    }

endif;
