<?php namespace XoopsModules\Tag;

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
 * @subpackage      class
 * @copyright       {@link http://sourceforge.net/projects/xoops/ The XOOPS Project}
 * @license         {@link http://www.fsf.org/copyleft/gpl.html GNU public license}
 * @author          Taiwen Jiang <phppp@users.sourceforge.net>
 * @since           1.00
 */

defined('XOOPS_ROOT_PATH') || die('Restricted access');

/**
 * Class TagHandler
 */
class TagHandler extends \XoopsPersistableObjectHandler
{
    public $table_link;
    public $table_stats;

    /**
     * Constructor
     *
     * @param \XoopsDatabase $db reference to the {@link XoopsDatabase}
     *                           object
     */
    public function __construct(\XoopsDatabase $db)
    {
        parent::__construct($db, 'tag_tag', Tag::class, 'tag_id', 'tag_term');
        $this->table_link  = $this->db->prefix('tag_link');
        $this->table_stats = $this->db->prefix('tag_stats');
    }

    /**
     * Get tags linked to an item
     *
     * @access public
     * @param  int $itemid item ID
     * @param  int $modid  module ID, optional
     * @param  int $catid  id of corresponding category, optional
     * @return array associative array of tags (id, term)
     */
    public function getByItem($itemid, $modid = 0, $catid = 0)
    {
        $ret = [];

        $itemid = (int)$itemid;
        $modid  = (empty($modid) && is_object($GLOBALS['xoopsModule'])
                   && 'tag' !== $GLOBALS['xoopsModule']->getVar('dirname')) ? $GLOBALS['xoopsModule']->getVar('mid') : (int)$modid;
        if (empty($itemid) || empty($modid)) {
            return $ret;
        }

        $sql = 'SELECT o.tag_id, o.tag_term'
               . " FROM {$this->table_link} AS l "
               . " LEFT JOIN {$this->table} AS o ON o.{$this->keyName} = l.{$this->keyName} "
               . " WHERE  l.tag_itemid = {$itemid} AND l.tag_modid = {$modid}"
               . (empty($catid) ? '' : (' AND l.tag_catid=' . (int)$catid))
               . ' ORDER BY o.tag_count DESC';
        if (false === ($result = $this->db->query($sql))) {
            return $ret;
        }
        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $ret[$myrow[$this->keyName]] = $myrow['tag_term'];
        }

        return $ret;
    }

    /**
     * Update tags linked to an item
     *
     * @access   public
     * @param  array|string $tags   array of $tags or a single tag
     * @param  int        $itemid item ID
     * @param  int|string $modid  module ID or module dirname, optional
     * @param  int        $catid  id of corresponding category, optional
     * @return bool
     */
    public function updateByItem($tags, $itemid, $modid = '', $catid = 0)
    {
        $catid  = (int)$catid;
        $itemid = (int)$itemid;

        if (!empty($modid) && !is_numeric($modid)) {
            if (($GLOBALS['xoopsModule'] instanceof XoopsModule)
                && ($modid == $GLOBALS['xoopsModule']->getVar('dirname'))) {
                $modid = $GLOBALS['xoopsModule']->getVar('mid');
            } else {
                /** @var XoopsModuleHandler $moduleHandler */
                $moduleHandler = xoops_getHandler('module');
                $modid         = ($module_obj = $moduleHandler->getByDirname($modid)) ? $module_obj->getVar('mid') : 0;
            }
        } elseif ($GLOBALS['xoopsModule'] instanceof XoopsModule) {
            $modid = $GLOBALS['xoopsModule']->getVar('mid');
        }

        if (empty($itemid) || empty($modid)) {
            return false;
        }

        if (empty($tags)) {
            $tags = [];
        } elseif (!is_array($tags)) {
            require_once $GLOBALS['xoops']->path('/modules/tag/include/functions.php');
            $tags = tag_parse_tag(addslashes(stripslashes($tags)));
        }

        $tags_existing = $this->getByItem($itemid, $modid, $catid);
        $tags_delete   = array_diff(array_values($tags_existing), $tags);
        $tags_add      = array_diff($tags, array_values($tags_existing));
        $tags_update   = [];

        if (!empty($tags_delete)) {
            $tags_delete = array_map([$this->db, 'quoteString'], $tags_delete);
            if ($tags_id =& $this->getIds(new \Criteria('tag_term', '(' . implode(', ', $tags_delete) . ')', 'IN'))) {
                $sql = "DELETE FROM {$this->table_link}" . ' WHERE ' . "     {$this->keyName} IN (" . implode(', ', $tags_id) . ')' . "     AND tag_modid = {$modid} AND tag_catid = {$catid} AND tag_itemid = {$itemid}";
                if (false === ($result = $this->db->queryF($sql))) {
                    //@todo: decide if we should do something here on failure
                }
                $sql = 'DELETE FROM ' . $this->table . ' WHERE ' . '    tag_count < 2 AND ' . "     {$this->keyName} IN (" . implode(', ', $tags_id) . ')';
                if (false === ($result = $this->db->queryF($sql))) {
                    //xoops_error($this->db->error());
                }

                $sql = 'UPDATE ' . $this->table . ' SET tag_count = tag_count - 1' . ' WHERE ' . "     {$this->keyName} IN (" . implode(', ', $tags_id) . ')';
                if (false === ($result = $this->db->queryF($sql))) {
                    //xoops_error($this->db->error());
                }
                $tags_update = $tags_id;
            }
        }

        if (!empty($tags_add)) {
            $tag_link  = [];
            $tag_count = [];
            foreach ($tags_add as $tag) {
                if ($tags_id =& $this->getIds(new \Criteria('tag_term', $tag))) {
                    $tag_id      = $tags_id[0];
                    $tag_count[] = $tag_id;
                } else {
                    $tag_obj = $this->create();
                    $tag_obj->setVar('tag_term', $tag);
                    $tag_obj->setVar('tag_count', 1);
                    $this->insert($tag_obj);
                    $tag_id = $tag_obj->getVar('tag_id');
                    unset($tag_obj);
                }
                $tag_link[]    = "({$tag_id}, {$itemid}, {$catid}, {$modid}, " . time() . ')';
                $tags_update[] = $tag_id;
            }
            $sql = "INSERT INTO {$this->table_link}" . ' (tag_id, tag_itemid, tag_catid, tag_modid, tag_time) ' . ' VALUES ' . implode(', ', $tag_link);
            if (false === ($result = $this->db->queryF($sql))) {
                //xoops_error($this->db->error());
            }
            if (!empty($tag_count)) {
                $sql = 'UPDATE ' . $this->table . ' SET tag_count = tag_count+1' . ' WHERE ' . "     {$this->keyName} IN (" . implode(', ', $tag_count) . ')';
                if (false === ($result = $this->db->queryF($sql))) {
                    //xoops_error($this->db->error());
                }
            }
        }
        if (is_array($tags_update)) {
            foreach ($tags_update as $tag_id) {
                $this->update_stats($tag_id, $modid, $catid);
            }
        }

        return true;
    }

    /**
     *
     * Update count stats sor tag
     *
     * @access public
     * @param  int $tag_id
     * @param  int $modid
     * @param  int $catid
     * @return bool
     */
    public function update_stats($tag_id, $modid = 0, $catid = 0)
    {
        $tag_id = (int)$tag_id;
        if (empty($tag_id)) {
            return true;
        }

        $modid = (int)$modid;
        $catid = empty($modid) ? -1 : (int)$catid;
        $count = 0;
        $sql   = 'SELECT COUNT(*) ' . " FROM {$this->table_link}" . " WHERE tag_id = {$tag_id}" . (empty($modid) ? '' : " AND tag_modid = {$modid}") . (($catid < 0) ? '' : " AND tag_catid = {$catid}");

        if ($result = $this->db->query($sql)) {
            list($count) = $this->db->fetchRow($result);
        }
        if (empty($modid)) {
            $tag_obj = $this->get($tag_id);
            if (empty($count)) {
                $this->delete($tag_obj);
            } else {
                $tag_obj->setVar('tag_count', $count);
                $this->insert($tag_obj, true);
            }
        } else {
            if (empty($count)) {
                $sql = "DELETE FROM {$this->table_stats}" . ' WHERE ' . " {$this->keyName} = {$tag_id}" . " AND tag_modid = {$modid}" . " AND tag_catid = {$catid}";

                if (false === $result = $this->db->queryF($sql)) {
                    //xoops_error($this->db->error());
                }
            } else {
                $ts_id = null;
                $sql   = 'SELECT ts_id, tag_count ' . " FROM {$this->table_stats}" . " WHERE {$this->keyName} = {$tag_id}" . " AND tag_modid = {$modid}" . " AND tag_catid = {$catid}";
                if ($result = $this->db->query($sql)) {
                    list($ts_id, $tag_count) = $this->db->fetchRow($result);
                }
                $sql = '';
                if ($ts_id && $tag_count != $count) {
                    $sql = "UPDATE {$this->table_stats}" . " SET tag_count = {$count}" . ' WHERE ' . "     ts_id = {$ts_id}";
                } elseif (!$ts_id) {
                    $sql = "INSERT INTO {$this->table_stats}" . ' (tag_id, tag_modid, tag_catid, tag_count)' . " VALUES ({$tag_id}, {$modid}, {$catid}, {$count})";
                }

                if (!empty($sql) && false === ($result = $this->db->queryF($sql))) {
                    //xoops_error($this->db->error());
                }
            }
        }

        return true;
    }

    /**
     * Get tags with item count
     *
     * @access         public
     * @param int             $limit
     * @param int             $start
     * @param null|\CriteriaElement $criteria  {@link Criteria}
     * @param null            $fields
     * @param boolean         $fromStats fetch from tag-stats table
     * @return array associative array of tags (id, term, count)
     */
    public function &getByLimit(
        $limit = 0,
        $start = 0,
        \CriteriaElement $criteria = null,
        $fields = null,
        $fromStats = true
    )//&getByLimit($criteria = null, $fromStats = true)
    {
        $ret = [];
        if ($fromStats) {
            $sql = "SELECT DISTINCT(o.{$this->keyName}), o.tag_term, o.tag_status, SUM(l.tag_count) AS count, l.tag_modid" . " FROM {$this->table} AS o LEFT JOIN {$this->table_stats} AS l ON l.{$this->keyName} = o.{$this->keyName}";
        } else {
            $sql = "SELECT DISTINCT(o.{$this->keyName}), o.tag_term, o.tag_status, COUNT(l.tl_id) AS count, l.tag_modid" . " FROM {$this->table} AS o LEFT JOIN {$this->table_link} AS l ON l.{$this->keyName} = o.{$this->keyName}";
        }

        $limit = null;
        $start = null;
        $sort  = '';
        $order = '';
        if (null !== $criteria && is_subclass_of($criteria, 'CriteriaCompo')) {
            $sql   .= ' ' . $criteria->renderWhere();
            $sort  = $criteria->getSort();
            $order = $criteria->getOrder();
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        $sql .= " GROUP BY o.{$this->keyName}";

        $order = mb_strtoupper($order);
        $sort  = mb_strtolower($sort);
        switch ($sort) {
            case 'a':
            case 'alphabet':
                $order = ('DESC' !== $order) ? 'ASC' : 'DESC';
                $sql   .= " ORDER BY o.tag_term {$order}";
                break;
            case 'id':
            case 'time':
                $order = ('ASC' !== $order) ? 'DESC' : 'ASC';
                $sql   .= " ORDER BY o.{$this->keyName} {$order}";
                break;
            case 'c':
            case 'count':
            default:
                $order = ('ASC' !== $order) ? 'DESC' : 'ASC';
                $sql   .= " ORDER BY count {$order}";
                break;
        }

        if (false === ($result = $this->db->query($sql, $limit, $start))) {
            //xoops_error($this->db->error());
            $ret = null;
        } else {
            while (false !== ($myrow = $this->db->fetchArray($result))) {
                $ret[$myrow[$this->keyName]] = [
                    'id'     => $myrow[$this->keyName],
                    'term'   => htmlspecialchars($myrow['tag_term']),
                    'status' => $myrow['tag_status'],
                    'modid'  => $myrow['tag_modid'],
                    'count'  => (int)$myrow['count']
                ];
            }
        }

        return $ret;
    }

    /**
     * Get count of tags
     *
     * @access public
     * @param null|\CriteriaElement $criteria {@link Criteria)
     *
     * @return integer count
     */
    public function getCount(\CriteriaElement $criteria = null)
    {
        /*
        $catid    = (int)($catid);
        $modid    = (int)($modid);
        */
        $sql = "SELECT COUNT(DISTINCT o.{$this->keyName})" . "    FROM {$this->table} AS o LEFT JOIN {$this->table_link} AS l ON l.{$this->keyName} = o.{$this->keyName}";
        if ((null !== $criteria) && is_subclass_of($criteria, 'CriteriaElement')) {
            $sql .= ' ' . $criteria->renderWhere();
        }
        /*
        $sql_where    = "    WHERE 1 = 1";
        if (!empty($modid)) {
            $sql_where    .= " AND l.tag_modid = {$modid}";
        }
        if (empty($catid) || $catid > 0) {
            $sql_where    .= " AND l.tag_catid = {$catid}";
        }

        $sql =     $sql_select . " " . $sql_from . " " . $sql_where;
        */
        if (false === ($result = $this->db->query($sql))) {
            //xoops_error($this->db->error());
            $ret = 0;
        } else {
            list($ret) = $this->db->fetchRow($result);
        }

        return $ret;
    }

    /**
     * Get items linked with a tag
     *
     * @param \CriteriaElement $criteria {@link Criteria}
     *
     * @return array associative array of items (id, modid, catid)
     */
    public function getItems(\CriteriaElement $criteria = null)
    {
        $ret = [];
        $sql = '    SELECT o.tl_id, o.tag_itemid, o.tag_modid, o.tag_catid, o.tag_time';
        $sql .= "    FROM {$this->table_link} AS o LEFT JOIN {$this->table} AS l ON l.{$this->keyName} = o.{$this->keyName}";

        $limit = null;
        $start = null;
        $sort  = '';
        $order = '';
        if ((null !== $criteria) && is_subclass_of($criteria, 'CriteriaElement')) {
            $sql   .= ' ' . $criteria->renderWhere();
            $sort  = $criteria->getSort();
            $order = $criteria->getOrder();
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }

        $order = mb_strtoupper($order);
        $sort  = mb_strtolower($sort);
        switch ($sort) {
            case 'i':
            case 'item':
                $order = ('DESC' !== $order) ? 'ASC' : 'DESC';
                $sql   .= "    ORDER BY o.tag_itemid {$order}, o.tl_id DESC";
                break;
            case 'm':
            case 'module':
                $order = ('DESC' !== $order) ? 'ASC' : 'DESC';
                $sql   .= "    ORDER BY o.tag_modid {$order}, o.tl_id DESC";
                break;
            case 't':
            case 'time':
            default:
                $order = ('ASC' !== $order) ? 'DESC' : 'ASC';
                $sql   .= "    ORDER BY o.tl_id {$order}";
                break;
        }

        if (false === ($result = $this->db->query($sql, $limit, $start))) {
            //xoops_error($this->db->error());
            $ret = [];
        } else {
            while (false !== ($myrow = $this->db->fetchArray($result))) {
                $ret[$myrow['tl_id']] = [
                    'itemid' => $myrow['tag_itemid'],
                    'modid'  => $myrow['tag_modid'],
                    'catid'  => $myrow['tag_catid'],
                    'time'   => $myrow['tag_time']
                ];
            }
        }

        return $ret;
    }

    /**
     * Get count of items linked with a tag
     *
     * @access public
     * @param  int $tag_id
     * @param  int $modid id of corresponding module, optional: 0 for all; >1 for a specific module
     * @param  int $catid id of corresponding category, optional
     * @return integer count
     */
    public function getItemCount($tag_id, $modid = 0, $catid = 0)
    {
        if (!$tag_id = (int)$tag_id) {
            $ret = 0;
        } else {
            $catid = (int)$catid;
            $modid = (int)$modid;

            $sql_select = '    SELECT COUNT(DISTINCT o.tl_id)';
            $sql_from   = "    FROM {$this->table_link} AS o LEFT JOIN {$this->table} AS l ON l.{$this->keyName} = o.{$this->keyName}";
            $sql_where  = "    WHERE o.tag_id = {$tag_id}";
            if (!empty($modid)) {
                $sql_where .= " AND o.tag_modid = {$modid}";
            }
            if (empty($catid) || $catid > 0) {
                $sql_where .= " AND o.tag_catid = {$catid}";
            }

            $sql = $sql_select . ' ' . $sql_from . ' ' . $sql_where;
            if (false === ($result = $this->db->query($sql))) {
                //xoops_error($this->db->error());
                $ret = 0;
            } else {
                list($ret) = $this->db->fetchRow($result);
            }
        }

        return $ret;
    }

    /**
     * delete an object as well as links relying on it
     *
     * @access public
     * @param \XoopsObject $object $object {@link Tag}
     * @param  bool        $force  flag to force the query execution despite security settings
     * @return bool
     */
    public function delete(\XoopsObject $object, $force = true)
    {
        /* {@internal - this isn't needed if we type hint Tag}
        if (!is_object($object) || !$object->getVar($this->keyName)) {
            return false;
        }
        */
        $queryFunc = empty($force) ? 'query' : 'queryF';

        /*
         * Remove item-tag links
         */
        $sql = 'DELETE' . " FROM {$this->table_link}" . " WHERE  {$this->keyName} = " . $object->getVar($this->keyName);
        /*
                if (false ===  ($result = $this->db->{$queryFunc}($sql))) {
                   // xoops_error($this->db->error());
                }
        */
        /*
         * Remove stats-tag links
         */
        $sql = 'DELETE' . " FROM {$this->table_stats}" . " WHERE  {$this->keyName} = " . $object->getVar($this->keyName);

        /*
                if (false === ($result = $this->db->{$queryFunc}($sql))) {
                   // xoops_error($this->db->error());
                }
        */

        return parent::delete($object, $force);
    }

    /**
     * clean orphan links from database
     *
     * @access public
     * @param string $table_link
     * @param string $field_link
     * @param string $field_object
     * @return bool true on success
     */
    public function cleanOrphan($table_link = '', $field_link = '', $field_object = '')
    {
        require_once $GLOBALS['xoops']->path('/modules/tag/functions.recon.php');

        //mod_loadFunctions("recon");
        return tag_cleanOrphan();
    }

    /**
     * get item Ids {@see XoopsPersistableObjectHandler}
     * Overloads default method to provide type hint since
     * this is a public function called by plugins
     *
     * @access public
     * @param \CriteriaElement $ids
     * @return array|bool      object IDs or false on failure
     */
    public function &getIds(\CriteriaElement $ids = null)
    {
        return parent::getIds($ids);
    }
}
