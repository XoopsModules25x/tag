<?php
/*
 You may not change or alter any portion of this comment or credits of
 supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit
 authors.

 This program is distributed in the hope that it will be useful, but
 WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * Module: Tag
 *
 * @category        Module
 * @package         tag
 * @author          XOOPS Module Development Team
 * @author          Mamba
 * @author          Herve Thouzard
 * @copyright       {@link http://xoops.org 2001-2016 XOOPS Project}
 * @coypright       Herve Thouzard
 * @license         {@link http://www.fsf.org/copyleft/gpl.html GNU public license}
 * @link            http://xoops.org XOOPS
 * @since           2.00
 */

if ((!defined('XOOPS_ROOT_PATH')) || !($GLOBALS['xoopsUser'] instanceof XoopsUser) || !$GLOBALS['xoopsUser']->IsAdmin()) {
    exit('Restricted access' . PHP_EOL);
}

/**
 * @param string $tablename
 *
 * @return bool
 */
function tableExists($tablename)
{
    $result = $GLOBALS['xoopsDB']->queryF("SHOW TABLES LIKE '$tablename'");
    return ($GLOBALS['xoopsDB']->getRowsNum($result) > 0) ? true : false;
}

/**
 *
 * Prepares system prior to attempting to install module
 * @param XoopsModule $module {@link XoopsModule}
 *
 * @return bool true if ready to install, false if not
 */
function xoops_module_pre_update_tag(XoopsModule $module)
{

    if (!class_exists('TagUtilities')) {
        xoops_load('utilities', 'tag');
    }
    //check for minimum XOOPS version
    if (!TagUtilities::checkXoopsVer($module)) {
        return false;
    }

    // check for minimum PHP version
    if (!TagUtilities::checkPHPVer($module)) {
        return false;
    }
    return true;
}

/**
 *
 * Performs tasks required during update of the module
 * @param XoopsModule $module {@link XoopsModule}
 * @param  null       $prev_version
 *
 * @return bool true if update successful, false if not
 */

function xoops_module_update_tag(XoopsModule $module, $prev_version = null)
{
    //load_functions("config");
    //mod_clearConfg($module->getVar("dirname", "n"));

    if ($prev_version <= 150) {
        $GLOBALS['xoopsDB']->queryFromFile($GLOBALS['xoops']->path('/modules/' . $module->getVar('dirname') . '/sql/mysql.150.sql'));
    }

    /* Do some synchronization */
    include_once $GLOBALS['xoops']->path('/modules/' . $module->getVar('dirname') . '/include/functions.recon.php');
    tag_synchronization();

    return true;
}
