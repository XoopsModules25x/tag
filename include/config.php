<?php
    if ($previousVersion <= 150) {
        $GLOBALS['xoopsDB']->queryFromFile($GLOBALS['xoops']->path('/modules/' . $module->getVar('dirname') . '/sql/mysql.150.sql'));
    }

