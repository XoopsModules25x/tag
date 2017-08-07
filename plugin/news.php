<?php
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
// Author: Hervé Thouzard                                     //
// URL: http://www.herve-thouzard.com                                        //
// ------------------------------------------------------------------------- //

function news_tag_iteminfo(&$items)
{
	if(empty($items) || !is_array($items)){
		return false;
	}

	$items_id = array();
	foreach(array_keys($items) as $cat_id){
		foreach(array_keys($items[$cat_id]) as $item_id){
			$items_id[] = intval($item_id);
		}
	}
	require_once XOOPS_ROOT_PATH.'/modules/news/class/class.newsstory.php';
	$tempNews = new NewsStory();
	$items_obj = $tempNews->getStoriesByIds($items_id);

	foreach(array_keys($items) as $cat_id){
		foreach(array_keys($items[$cat_id]) as $item_id) {
			if(isset($items_obj[$item_id])) {
				$item_obj =& $items_obj[$item_id];
				$items[$cat_id][$item_id] = array(
					'title'		=> $item_obj->title(),
					'uid'		=> $item_obj->uid(),
					'link'		=> "article.php?storyid={$item_id}",
					'time'		=> $item_obj->published(),
					'tags'		=> '',		// tag_parse_tag($item_obj->getVar("item_tags", "n")), // optional
					'content'	=> '',
					);
				}
			}
	}
	unset($items_obj);
}

function news_tag_synchronization($mid)
{
	global $xoopsDB;
	$item_handler_keyName = 'storyid';
	$item_handler_table = $xoopsDB->prefix('stories');
	$link_handler =& xoops_getmodulehandler("link", "tag");
	$where = "($item_handler_table.published > 0 AND $item_handler_table.published <= ".time().") AND ($item_handler_table.expired = 0 OR $item_handler_table.expired > ".time().')';

	/* clear tag-item links */
	if($link_handler->mysql_major_version() >= 4):
    $sql =	"	DELETE FROM {$link_handler->table}".
    		"	WHERE ".
    		"		tag_modid = {$mid}".
    		"		AND ".
    		"		( tag_itemid NOT IN ".
    		"			( SELECT DISTINCT {$item_handler_keyName} ".
    		"				FROM {$item_handler_table} ".
    		"				WHERE $where".
    		"			) ".
    		"		)";
    else:
    $sql = 	"	DELETE {$link_handler->table} FROM {$link_handler->table}".
    		"	LEFT JOIN {$item_handler_table} AS aa ON {$link_handler->table}.tag_itemid = aa.{$item_handler_keyName} ".
    		"	WHERE ".
    		"		tag_modid = {$mid}".
    		"		AND ".
    		"		( aa.{$item_handler_keyName} IS NULL".
    		"			OR $where".
    		"		)";
	endif;
    if (!$result = $link_handler->db->queryF($sql)) {
        //xoops_error($link_handler->db->error());
  	}
}
?>