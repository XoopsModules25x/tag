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
 * @author         Taiwen Jiang (phppp or D.J.) <php_pp@hotmail.com>
 * @since          1.00
 * @version        $Id: admin.php 12898 2014-12-08 22:05:21Z zyspec $
 */

defined('XOOPS_ROOT_PATH') || exit('Restricted access');
define('_AM_TAG_TERM', "Тег");

define('_AM_TAG_STATS', "Статистическая информация");
define('_AM_TAG_COUNT_TAG', "Количество тегов: %s");
define('_AM_TAG_COUNT_ITEM', "Количество элементов: %s");
define('_AM_TAG_COUNT_MODULE', "Модуль:");
define('_AM_TAG_COUNT_MODULE_TITLE', "Количество элементов/Количество тегов");

define('_AM_TAG_EDIT', "Управление тегами");
define('_AM_TAG_SYNCHRONIZATION', "Синхронизировать");

define('_AM_TAG_ACTIVE', "Активный");
define('_AM_TAG_INACTIVE', "Неактивный");
define('_AM_TAG_GLOBAL', "Глобальный");
define('_AM_TAG_ALL', "Все модули");
define('_AM_TAG_NUM', "Номер каждый раз");
define('_AM_TAG_IN_PROCESS', "Синхронизация данных в процессе, пожалуйста, подождите некоторое время ...");
define('_AM_TAG_FINISHED', "Синхронизация данных завершена.");

//2.31
// index.php
/*
define('_MI_TAG_ADMIN_INDEX', "Index");
define('_MI_TAG_ADMIN_HOME', "Home");
define('_MI_AM_TAG_ADMIN_HOME_DESC', "Go back to Administration module");
define('_MI_TAG_ADMIN_ABOUT', "About");
define('_MI_TAG_ADMIN_HELP_DESC', "About this module");
define('_MI_TAG_HELP_DESC', "Module help");
//define('_MI_TAG_ADMIN_HELP', "Help");
*/
//2.32
define('_AM_TAG_INDEX_TPL_DESC', "Главная страница тегов");
define('_AM_TAG_INDEX_TPL_LIST_DESC', "Список тегов");
define('_AM_TAG_INDEX_TPL_VIEW_DESC', "Ссылки тегов");
define('_AM_TAG_INDEX_TPL_BAR_DESC', "Список тегов в элементе");
define('_AM_TAG_INDEX_ADMINTPL_ABOUT_DESC', "");
define('_AM_TAG_INDEX_ADMINTPL_HELP_DESC', "");

// Text for Admin footer
define('_AM_TAG_MAINTAINED_BY', "XOOPS Tег поддерживается");
define('_AM_TAG_MAINTAINED_TITLE', "Посещение XOOPS сообщества");
define('_AM_TAG_MAINTAINED_TEXT', "XOOPS сообщество");

// About.php
define('_AM_TAG_ABOUT_RELEASEDATE', "Релиз: ");
define('_AM_TAG_ABOUT_UPDATEDATE', "Обновленный: ");
define('_AM_TAG_ABOUT_AUTHOR', "Автор: ");
define('_AM_TAG_ABOUT_CREDITS', "Credits: ");
define('_AM_TAG_ABOUT_LICENSE', "License: ");
define('_AM_TAG_ABOUT_MODULE_STATUS', "Статус: ");
define('_AM_TAG_ABOUT_WEBSITE', "Website: ");
define('_AM_TAG_ABOUT_AUTHOR_NAME', "Author name: ");
define('_AM_TAG_ABOUT_CHANGELOG', "Change Log");
define('_AM_TAG_ABOUT_MODULE_INFO', "Module Infos");
define('_AM_TAG_ABOUT_AUTHOR_INFO', "Author Infos");
define('_AM_TAG_ABOUT_DESCRIPTION', "Описание: ");

// text in admin footer
define('_AM_TAG_ADMIN_FOOTER', "<div class='right smallsmall italic pad5'><b>" . $GLOBALS['xoopsModule']->getVar("name") . "</b> поддерживается <a class='tooltip' rel='external' href='http://xoops.org/' title='Visit XOOPS Community'>XOOPS сообществом</a></div>");

//ModuleAdmin
define('_AM_MODULEADMIN_MISSING', "Ошибка: Класс ModuleAdmin отсутствует. Пожалуйста, установите ModuleAdmin Class (see /docs/readme.txt)");

//define('_AM_TAG_MISSING','Error: The ModuleAdmin class is missing. Please install the ModuleAdmin Class into /Frameworks (see /docs/readme.txt)');

// Text for Admin footer
define("_AM_TAG_FOOTER", "<div class='center smallsmall italic pad5'>Модуль тегов поддерживается <a class='tooltip' rel='external' href='http://xoops.org/' title='Visit XOOPS Community'>XOOPS сообществом</a></div>");

//2.32
define('_AM_TAG_DB_UPDATED', "База данных успешно обновлена");
