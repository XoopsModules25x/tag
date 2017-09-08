<?php
    if ($previousVersion <= 150) {
        $GLOBALS['xoopsDB']->queryFromFile($GLOBALS['xoops']->path('/modules/' . $module->getVar('dirname') . '/sql/mysql.150.sql'));
    }

    /* Do some synchronization */
    include_once $GLOBALS['xoops']->path('/modules/' . $module->getVar('dirname') . '/include/functions.recon.php');
    tag_synchronization();

    return true;