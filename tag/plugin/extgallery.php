<?php
function extgallery_tag_iteminfo(&$items)
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
    
    $item_handler =& xoops_getmodulehandler('publicphoto', 'extgallery');
    $items_obj = $item_handler->getObjects(new Criteria("photo_id", "(" . implode(", ", $items_id) . ")", "IN"), true);

	foreach(array_keys($items) as $cat_id){
		foreach(array_keys($items[$cat_id]) as $item_id) {
			if(isset($items_obj[$item_id])) {
				$item_obj =& $items_obj[$item_id];
				$items[$cat_id][$item_id] = array(
					'title'		=> $item_obj->getVar("photo_title"),
					'uid'		=> $item_obj->getVar("uid"),
					'link'		=> "public-photo.php?photoId={$item_id}#photoNav",
					'time'		=> $item_obj->getVar("photo_date"),
					'tags'		=> '',
					'content'	=> '',
					);
				}
			}
	}
	unset($items_obj);
}

function extgallery_tag_synchronization($mid)
{
    $item_handler =& xoops_getmodulehandler('publicphoto', 'extgallery');
    $link_handler =& xoops_getmodulehandler("link", "tag");
        
    /* clear tag-item links */
    if (version_compare( mysql_get_server_info(), "4.1.0", "ge" )):
    $sql =  "    DELETE FROM {$link_handler->table}" .
            "    WHERE " .
            "        tag_modid = {$mid}" .
            "        AND " .
            "        ( tag_itemid NOT IN " .
            "            ( SELECT DISTINCT {$item_handler->keyName} " .
            "                FROM {$item_handler->table} " .
            "                WHERE {$item_handler->table}.status > 0" .
            "            ) " .
            "        )";
    else:
    $sql =  "    DELETE {$link_handler->table} FROM {$link_handler->table}" .
            "    LEFT JOIN {$item_handler->table} AS aa ON {$link_handler->table}.tag_itemid = aa.{$item_handler->keyName} " .
            "    WHERE " .
            "        tag_modid = {$mid}" .
            "        AND " .
            "        ( aa.{$item_handler->keyName} IS NULL" .
            "            OR aa.status < 1" .
            "        )";
    endif;
    if (!$result = $link_handler->db->queryF($sql)) {
        //xoops_error($link_handler->db->error());
    }
}
?>