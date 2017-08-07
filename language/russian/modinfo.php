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
 * @copyright       {@link http://sourceforge.net/projects/xoops/ The XOOPS Project}
 * @license         {@link http://www.fsf.org/copyleft/gpl.html GNU public license}
 * @author          Taiwen Jiang <phppp@users.sourceforge.net>
 * @since           1.00
 * @version         $Id: modinfo.php 12898 2014-12-08 22:05:21Z zyspec $
 */

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

define('_MI_TAG_NAME', "XOOPS тег");
define('_MI_TAG_DESC', "Управление тегами");

define('_MI_TAG_BLOCK_CLOUD', "Облако тегов");
define('_MI_TAG_BLOCK_CLOUD_DESC', "");
define('_MI_TAG_BLOCK_TOP', "Топ тегов");
define('_MI_TAG_BLOCK_TOP_DESC', "");

define('_MI_TAG_DOURLREWRITE', "Включить URL переадресацию");
define('_MI_TAG_DOURLREWRITE_DESC', "Требуется AcceptPathInfo для Apache2");

define('_MI_TAG_ITEMSPERPAGE', "Кол-во на странице");
define('_MI_TAG_ITEMSPERPAGE_DESC', "");

define('_MI_TAG_ADMENU_INDEX', "Главная");
define('_MI_TAG_ADMENU_EDIT', "Управление тегами");
define('_MI_TAG_ADMENU_EDIT_DESC', "Посмотреть/Редактировать теги");
define('_MI_TAG_ADMENU_SYNCHRONIZATION', "Синхронизировать");

//2.31
define('_MI_TAG_ADMIN_INDEX', "Главная");
define('_MI_TAG_ADMIN_HOME', "Главная");
define('_MI_TAG_ADMIN_HOME_DESC', "Вернитесь в модуль администрирования");
define('_MI_TAG_ADMIN_ABOUT', "About");
define('_MI_TAG_ADMIN_HELP_DESC', "Об этом модуле");
define('_MI_TAG_HELP_DESC', "Помощь");
//define('_MI_TAG_ADMIN_HELP', "Help");

//2.32
define('_MI_TAG_INDEX_TPL_DESC', "Главная страница тегов");
define('_MI_TAG_INDEX_TPL_LIST_DESC', "Список тегов");
define('_MI_TAG_INDEX_TPL_VIEW_DESC', "Ссылки тегов");
define('_MI_TAG_INDEX_TPL_BAR_DESC', "Список тегов в элементе");
define('_MI_TAG_INDEX_ADMINTPL_ABOUT_DESC', "");
define('_MI_TAG_INDEX_ADMINTPL_HELP_DESC', "");

//2.33
define('_MI_TAG_BLOCK_CUMULUS',"Облако тегов");
define('_MI_TAG_BLOCK_CUMULUS_DESC', "Показать термины в движущемся облаке");
define('_MI_TAG_LIMITPERLIST', "Максимальное количество элементов в списке");
define('_MI_TAG_LIMITPERLIST_DESC', "");
define('_MI_TAG_LIMITPERCLOUD', "Максимальное количество элементов в облаке");
define('_MI_TAG_LIMITPERCLOUD_DESC', "");
