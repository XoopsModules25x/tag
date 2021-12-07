<?php declare(strict_types=1);

namespace XoopsModules\Tag;

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
 * @copyright       {@link https://sourceforge.net/projects/xoops/ The XOOPS Project}
 * @license         {@link https://www.fsf.org/copyleft/gpl.html GNU public license}
 * @author          Taiwen Jiang <phppp@users.sourceforge.net>
 * @since           1.00
 */
\defined('XOOPS_ROOT_PATH') || exit('Restricted access');

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
     * @param \XoopsMySQLDatabase|null $db reference to the object {@link XoopsMySQLDatabase}
     */
    public function __construct(?\XoopsMySQLDatabase $db = null)
    {
        parent::__construct($db, 'tag_tag', Tag::class, 'tag_id', 'tag_term');
        $this->table_link  = $this->db->prefix('tag_link');
        $this->table_stats = $this->db->prefix('tag_stats');
    }

    /**
     * Get tags linked to an item
     *
     * @param int $itemid item ID
     * @param int $modid  module ID, optional
     * @param int $catid  id of corresponding category, optional
     * @return array associative array of tags (id, term)
     */
    public function getByItem($itemid, $modid = 0, $catid = 0): array
    {
        $ret = [];

        $itemid = (int)$itemid;
        $modid  = (empty($modid) && \is_object($GLOBALS['xoopsModule'])
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

        $result = $this->db->query($sql);
        if ($result instanceof \mysqli_result) {
            while (false !== ($myrow = $this->db->fetchArray($result))) {
                $ret[$myrow[$this->keyName]] = $myrow['tag_term'];
            }
        }

        return $ret;
    }

    /**
     * Update tags linked to an item
     *
     * @param array|string $tags   array of $tags or a single tag
     * @param int          $itemid item ID
     * @param int|string   $modid  module ID or module dirname, optional
     * @param int          $catid  id of corresponding category, optional
     * @return bool
     */
    public function updateByItem($tags, $itemid, $modid = '', $catid = 0): bool
    {
        $catid  = (int)$catid;
        $itemid = (int)$itemid;

        if (!empty($modid) && !\is_numeric($modid)) {
            if (($GLOBALS['xoopsModule'] instanceof \XoopsModule)
                && ($modid == $GLOBALS['xoopsModule']->getVar('dirname'))) {
                $modid = $GLOBALS['xoopsModule']->getVar('mid');
            } else {
                /** @var \XoopsModuleHandler $moduleHandler */
                $moduleHandler = \xoops_getHandler('module');
                $modid         = ($module_obj = $moduleHandler->getByDirname($modid)) ? $module_obj->getVar('mid') : 0;
            }
        } elseif ($GLOBALS['xoopsModule'] instanceof \XoopsModule) {
            $modid = $GLOBALS['xoopsModule']->getVar('mid');
        }

        if (empty($itemid) || empty($modid)) {
            return false;
        }

        if (empty($tags)) {
            $tags = [];
        } elseif (!\is_array($tags)) {
            //require_once $GLOBALS['xoops']->path('/modules/tag/include/functions.php');
            $tags = Utility::tag_parse_tag(\addslashes(\stripslashes($tags)));
        }

        $tags_existing = $this->getByItem($itemid, $modid, $catid);
        $tags_delete   = \array_diff(\array_values($tags_existing), $tags);
        $tags_add      = \array_diff($tags, \array_values($tags_existing));
        $tags_update   = [];

        if (0 < \count($tags_delete)) {
            $tags_delete = \array_map([$this->db, 'quoteString'], $tags_delete);
            $tags_id     = &$this->getIds(new \Criteria('tag_term', '(' . \implode(', ', $tags_delete) . ')', 'IN'));
            if (!empty($tags_id)) {
                $sql = "DELETE FROM {$this->table_link}" . ' WHERE ' . "     {$this->keyName} IN (" . \implode(', ', $tags_id) . ')' . "     AND tag_modid = {$modid} AND tag_catid = {$catid} AND tag_itemid = {$itemid}";
                if (false === ($result = $this->db->queryF($sql))) {
                    //@todo: decide if we should do something here on failure
                }
                $sql = 'DELETE FROM ' . $this->table . ' WHERE ' . '    tag_count < 2 AND ' . "     {$this->keyName} IN (" . \implode(', ', $tags_id) . ')';
                if (false === ($result = $this->db->queryF($sql))) {
                    \trigger_error($this->db->error());
                }

                $sql = 'UPDATE ' . $this->table . ' SET tag_count = tag_count - 1' . ' WHERE ' . "     {$this->keyName} IN (" . \implode(', ', $tags_id) . ')';
                if (false === ($result = $this->db->queryF($sql))) {
                    \trigger_error($this->db->error());
                }
                $tags_update = $tags_id;
            }
        }

        if (!empty($tags_add)) {
            $tag_link  = [];
            $tag_count = [];
            foreach ($tags_add as $tag) {
                $tags_id = &$this->getIds(new \Criteria('tag_term', $tag));
                if (!empty($tags_id)) {
                    $tag_id      = $tags_id[0];
                    $tag_count[] = $tag_id;
                } else {
                    $tagObj = $this->create();
                    $tagObj->setVars(['tag_term' => $tag, 'tag_count' => 1]);
                    $this->insert($tagObj);
                    $tag_id = $tagObj->getVar('tag_id');
                    unset($tagObj);
                }
                $tag_link[]    = "({$tag_id}, {$itemid}, {$catid}, {$modid}, " . \time() . ')';
                $tags_update[] = $tag_id;
            }
            $sql = "INSERT INTO {$this->table_link}" . ' (tag_id, tag_itemid, tag_catid, tag_modid, tag_time) ' . ' VALUES ' . \implode(', ', $tag_link);
            if (false === ($result = $this->db->queryF($sql))) {
                \trigger_error($this->db->error());
            }
            if (!empty($tag_count)) {
                $sql = 'UPDATE ' . $this->table . ' SET tag_count = tag_count+1' . ' WHERE ' . "     {$this->keyName} IN (" . \implode(', ', $tag_count) . ')';
                if (false === ($result = $this->db->queryF($sql))) {
                    \trigger_error($this->db->error());
                }
            }
        }
        if (\is_array($tags_update)) {
            foreach ($tags_update as $tag_id) {
                $this->update_stats($tag_id, $modid, $catid);
            }
        }

        return true;
    }

    /**
     * Update count stats or tag
     *
     * @param int $tag_id
     * @param int $modid
     * @param int $catid
     * @return bool
     */
    public function update_stats($tag_id, $modid = 0, $catid = 0): bool
    {
        $tag_id = (int)$tag_id;
        if (0 === $tag_id) {
            return true;
        }

        $tag_count = [];
        $modid     = (int)$modid;
        $catid     = (0 === $modid) ? -1 : (int)$catid;

        /** @var \XoopsModules\Tag\LinkHandler $linkHandler */
        $linkHandler = \XoopsModules\Tag\Helper::getInstance()->getHandler('Link');
        $criteria    = new \CriteriaCompo(new \Criteria('tag_id', $tag_id));
        if (0 !== $modid) {
            $criteria->add(new \Criteria('tag_modid', $modid), 'AND');
        }
        if (0 < $catid) {
            $criteria->add(new \Criteria('tag_catid', $catid), 'AND');
        }
        $count = $linkHandler->getCount($criteria);
        /*
        $sql   = 'SELECT COUNT(*) ' . " FROM {$this->table_link}" . " WHERE tag_id = {$tag_id}" . (empty($modid) ? '' : " AND tag_modid = {$modid}") . (($catid < 0) ? '' : " AND tag_catid = {$catid}");

        $result = $this->db->query($sql);
        if ($result) {
            list($count) = $this->db->fetchRow($result);
        }
        */
        if (0 === $modid) {
            $tagObj = $this->get($tag_id);
            if ($tagObj instanceof \XoopsModules\Tag\Tag) {
                if (0 === $count) {
                    $this->delete($tagObj);
                } else {
                    $tagObj->setVar('tag_count', $count);
                    $this->insert($tagObj, true);
                }
            }
        } else {
            $statsHandler = Helper::getInstance()->getHandler('Stats');
            if (empty($count)) {
                $criteria = new \CriteriaCompo(new \Criteria($this->keyName, $tag_id));
                $criteria->add(new \Criteria('tag_modid, $modid'), 'AND');
                $criteria->add(new \Criteria('tag_catid', $catid), 'AND');
                $status = $statsHandler->deleteAll($criteria);
                if (!$status) {
                    //@todo determine what should happen here on failure.
                }
                /*
                $sql = "DELETE FROM {$this->table_stats}" . ' WHERE ' . " {$this->keyName} = {$tag_id}" . " AND tag_modid = {$modid}" . " AND tag_catid = {$catid}";

                if (false === $result = $this->db->queryF($sql)) {
                    \trigger_error($this->db->error());
                }
                */
            } else {
                $ts_id    = null;
                $criteria = new \CriteriaCompo(new \Criteria($this->keyName, $tag_id));
                $criteria->add(new \Criteria('tag_modid', $modid), 'AND');
                $criteria->add(new \Criteria('tag_catid', $catid), 'AND');
                $criteria->setLimit(1);
                $tsCountObjs = $statsHandler->getAll($criteria);
                if (\count($tsCountObjs) > 0) {
                    $tsCountObj = \array_pop($tsCountObjs); // get 1st (only) item
                    $ts_id      = $tsCountObj->getVar('ts_id');
                    $tag_count  = $tsCountObj->getVar('tag_count');
                }
                /*
                $sql   = 'SELECT ts_id, tag_count ' . " FROM {$this->table_stats}" . " WHERE {$this->keyName} = {$tag_id}" . " AND tag_modid = {$modid}" . " AND tag_catid = {$catid}";
                $result = $this->db->query($sql);
                if ($result) {
                    list($ts_id, $tag_count) = $this->db->fetchRow($result);
                }
                */
                $sql = '';
                if ($ts_id) {
                    if ($tag_count != $count) {
                        $tsCountObj->setVar('tag_count', $count);
                        $statsHandler->insert($tsCountObj);
                        //$sql = "UPDATE {$this->table_stats}" . " SET tag_count = {$count}" . ' WHERE ' . "     ts_id = {$ts_id}";
                    }
                } else {
                    $newTsObj = $statsHandler->create();
                    $newTsObj->setVars(
                        [
                            'tag_id'    => $tag_id,
                            'tag_modid' => $modid,
                            'tag_catid' => $catid,
                            'tag_count' => $count,
                        ]
                    );
                    $statsHandler->insert($newTsObj);
                    //$sql = "INSERT INTO {$this->table_stats}" . ' (tag_id, tag_modid, tag_catid, tag_count)' . " VALUES ({$tag_id}, {$modid}, {$catid}, {$count})";
                }
                /*
                if (!empty($sql) && false === ($result = $this->db->queryF($sql))) {
                    \trigger_error($this->db->error());
                }
                */
            }
        }

        return true;
    }

    /**
     * Get tags with item count
     *
     * @param int                                  $limit
     * @param int                                  $start
     * @param null|\CriteriaElement|\CriteriaCompo $criteria  {@link Criteria}
     * @param null                                 $fields
     * @param bool                                 $fromStats fetch from tag-stats table
     * @return array associative array of tags (id, term, status, count)
     */
    public function &getByLimit(
        $limit = Constants::UNLIMITED,
        $start = Constants::BEGINNING,
        \CriteriaElement $criteria = null,
        $fields = null,
        $fromStats = true
    ) {//&getByLimit($criteria = null, $fromStats = true)
        $ret = [];
        if ($fromStats) {
            $sql = "SELECT DISTINCT(o.{$this->keyName}), o.tag_term, o.tag_status, SUM(l.tag_count) AS count , l.tag_modid" . " FROM {$this->table} AS o LEFT JOIN {$this->table_stats} AS l ON l.{$this->keyName} = o.{$this->keyName}";
        } else {
            $sql = "SELECT DISTINCT(o.{$this->keyName}), o.tag_term, o.tag_status, COUNT(l.tl_id) AS count , l.tag_modid" . " FROM {$this->table} AS o LEFT JOIN {$this->table_link} AS l ON l.{$this->keyName} = o.{$this->keyName}";
        }

        $limit = \is_int($limit) && ($limit >= 0) ? $limit : Constants::UNLIMITED;
        $start = \is_int($start) && ($start >= 0) ? $start : Constants::BEGINNING;
        $sort  = '';
        $order = '';
        if (($criteria instanceof \CriteriaCompo) || ($criteria instanceof \Criteria)) {
            $sql   .= ' ' . $criteria->renderWhere();
            $sort  = $criteria->getSort();
            $order = $criteria->getOrder();
            $limit = $limit >= 0 ? $limit : $criteria->getLimit(); // non-zero arg passed to method overrides $criteria setting
            $start = $start >= 0 ? $start : $criteria->getStart(); // non-zero arg passed to method overrides $criteria setting
        }
        $sql .= " GROUP BY o.{$this->keyName}, o.tag_term, o.tag_status, l.tag_modid";

        $order = ('ASC' !== \mb_strtoupper($order)) ? 'DESC' : 'ASC';
        $sort  = \mb_strtolower($sort);
        switch ($sort) {
            case 'a':
            case 'alphabet':
                $sql .= " ORDER BY o.tag_term {$order}";
                break;
            case 'id':
            case 'time':
                $sql .= " ORDER BY o.{$this->keyName} {$order}";
                break;
            case 'c':
            case 'count':
            default:
                $sql .= " ORDER BY count {$order}";
                break;
        }

        if (false !== ($result = $this->db->query($sql, $limit, $start))) {
            while (false !== ($myrow = $this->db->fetchArray($result))) {
                $ret[$myrow[$this->keyName]] = [
                    'id'     => $myrow[$this->keyName],
                    'term'   => \htmlspecialchars($myrow['tag_term'], \ENT_QUOTES | \ENT_HTML5),
                    'status' => $myrow['tag_status'],
                    'modid'  => $myrow['tag_modid'],
                    'count'  => (int)$myrow['count'],
                ];
            }
        } else {
            \trigger_error($this->db->error());
            $ret = null;
        }

        return $ret;
    }

    /**
     * Get count of tags
     *
     * @param null|\CriteriaElement|\CriteriaCompo $criteria {@link Criteria)
     * @return int count
     */
    public function getCount(\CriteriaElement $criteria = null)
    {
        /*
        $catid    = (int)($catid);
        $modid    = (int)($modid);
        */
        $sql = "SELECT COUNT(DISTINCT o.{$this->keyName})" . "    FROM {$this->table} AS o LEFT JOIN {$this->table_link} AS l ON l.{$this->keyName} = o.{$this->keyName}";
        if (($criteria instanceof \CriteriaCompo) || ($criteria instanceof \Criteria)) {
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
        if (false !== ($result = $this->db->query($sql))) {
            [$ret] = $this->db->fetchRow($result);
        } else {
            \trigger_error($this->db->error());
            $ret = 0;
        }

        return $ret;
    }

    /**
     * Get items linked with a tag
     *
     * @param \CriteriaElement|null $criteria {@link Criteria}
     * @return array associative array of items[] => (id, modid, catid, time)
     */
    public function getItems(\CriteriaElement $criteria = null): array
    {
        $ret = [];
        $sql = '    SELECT o.tl_id, o.tag_itemid, o.tag_modid, o.tag_catid, o.tag_time';
        $sql .= "    FROM {$this->table_link} AS o LEFT JOIN {$this->table} AS l ON l.{$this->keyName} = o.{$this->keyName}";

        $limit = Constants::UNLIMITED;
        $start = Constants::BEGINNING;
        $sort  = '';
        $order = '';
        if (($criteria instanceof \CriteriaCompo) || ($criteria instanceof \Criteria)) {
            $sql   .= ' ' . $criteria->renderWhere();
            $sort  = $criteria->getSort();
            $order = $criteria->getOrder();
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }

        $order = ('ASC' !== \mb_strtoupper($order)) ? 'DESC' : 'ASC';
        $sort  = \mb_strtolower($sort);
        switch ($sort) {
            case 'i':
            case 'item':
                $sql .= "    ORDER BY o.tag_itemid {$order}, o.tl_id DESC";
                break;
            case 'm':
            case 'module':
                $sql .= "    ORDER BY o.tag_modid {$order}, o.tl_id DESC";
                break;
            case 't':
            case 'time':
            default:
                $sql .= "    ORDER BY o.tl_id {$order}";
                break;
        }

        if (false !== ($result = $this->db->query($sql, $limit, $start))) {
            while (false !== ($myrow = $this->db->fetchArray($result))) {
                $ret[$myrow['tl_id']] = [
                    'itemid' => $myrow['tag_itemid'],
                    'modid'  => $myrow['tag_modid'],
                    'catid'  => $myrow['tag_catid'],
                    'time'   => $myrow['tag_time'],
                ];
            }
        } else {
            \trigger_error($this->db->error());
            $ret = [];
        }

        return $ret;
    }

    /**
     * Get count of items linked with a tag
     *
     * @param int $tag_id
     * @param int $modid id of corresponding module, optional: 0 for all; >1 for a specific module
     * @param int $catid id of corresponding category, optional
     * @return int count
     */
    public function getItemCount($tag_id, $modid = 0, $catid = 0): int
    {
        if ($tag_id = (int)$tag_id) {
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
            if (false !== ($result = $this->db->query($sql))) {
                [$ret] = $this->db->fetchRow($result);
            } else {
                \trigger_error($this->db->error());
                $ret = 0;
            }
        } else {
            $ret = 0;
        }

        return $ret;
    }

    /**
     * Get detailed data (and font) for a tag
     *
     * @param array $tags_array associative array of tags (id, term, status, count)
     * @param int   $font_max
     * @param int   $font_min
     * @return array tag data values for display
     */
    public function getTagData($tags_array, $font_max = 0, $font_min = 0): array
    {
        //        $tags_data_array = [];
        //        if (\is_array($tags_array) && !empty($tags_array)) {
        //            // set min and max tag count
        //            $count_array = \array_column($tags_array, 'count', 'id');
        //            $count_min   = \count($count_array) > 0 ? \min($count_array) : 0;
        //            $count_min   = max($count_min, 0);
        //            $count_max   = \count($count_array) > 0 ? \max($count_array) : 0;
        //            $count_max   = max($count_max, 0);
        //            if ($count_max > 0) {
        //                $term_array      = \array_column($tags_array, 'term', 'id');
        //                $tags_term_array = \array_map('\mb_strtolower', $term_array);
        //                \array_multisort($tags_term_array, \SORT_ASC, $tags_array);
        //                $count_interval = $count_max - $count_min;
        //                $level_limit    = 5;
        //
        //                $font_ratio = $count_interval ? ($font_max - $font_min) / $count_interval : 1;
        //
        //                foreach ($tags_array as $tag) {
        //                    /*
        //                     * Font-size = ((tag.count - count.min) * (font.max - font.min) / (count.max - count.min) ) * 100%
        //                     */
        //                    $font_sz           = \floor(($tag['count'] - $count_min) * $font_ratio) + $font_min;
        //                    $level_sz          = \floor(($tag['count'] - $count_min) * $level_limit / $count_max);
        //                    $tags_data_array[] = [
        //                        'id'    => $tag['id'],
        //                        'font'  => empty($count_interval) ? 100 : (int)$font_sz,
        //                        'level' => empty($count_max) ? 0 : (int)$level_sz,
        //                        'term'  => \urlencode($tag['term']),
        //                        'title' => \htmlspecialchars($tag['term'], \ENT_QUOTES | \ENT_HTML5),
        //                        'count' => $tag['count'],
        //                    ];
        //                }
        //            }
        //        }

        $count_max = 0;
        $count_min = 0;
        $tags_term = [];
        foreach ($tags_array as $tag) {
            $count_max   = \max($count_max, $tag['count']); // set counter to the max tag count
            $count_min   = \min(0, $count_min, $tag['count']); //set counter to the minimum for tag count
            $tags_term[] = \mb_strtolower($tag['term']);
        }

        if (!empty($tags_term)) {
            \array_multisort($tags_term, \SORT_ASC, $tags_array);
        }
        $count_interval = $count_max - $count_min;
        $level_limit    = 5;

        //            $font_max   = $options[2];
        //            $font_min   = $options[3];
        $font_ratio = $count_interval ? ($font_max - $font_min) / $count_interval : 1;

        $tags_data_array = [];
        foreach ($tags_array as $tag) {
            $tags_data_array[] = [
                'id'    => $tag['id'],
                'font'  => $count_interval ? \floor(($tag['count'] - $count_min) * $font_ratio + $font_min) : 100,
                'level' => empty($count_max) ? 0 : \floor(($tag['count'] - $count_min) * $level_limit / $count_max),
                'term'  => \urlencode($tag['term']),
                'title' => \htmlspecialchars($tag['term'], \ENT_QUOTES | \ENT_HTML5),
                'count' => $tag['count'],
            ];
        }
        //            unset($tags_array, $tag, $tags_term, $tag_count_array);

        return $tags_data_array;
    }

    /**
     * Delete an object as well as links relying on it
     *
     * @param \XoopsObject $object $object {@link Tag}
     * @param bool         $force  flag to force the query execution despite security settings
     * @return bool
     */
    public function delete(\XoopsObject $object, $force = true)
    {
        /* {@internal - this isn't needed if we type hint Tag object }}
        if (!is_object($object) || !$object->getVar($this->keyName)) {
            return false;
        }
        */
        //$queryFunc = empty($force) ? 'query' : 'queryF';

        /*
         * Remove item-tag links
         */
        $helper = \XoopsModules\Tag\Helper::getInstance();

        /** @var \XoopsModules\Tag\LinkHandler $linkHandler */
        $linkHandler = $helper->getHandler('Link');
        $criteria    = new \Criteria($this->keyName, $object->getVar($this->keyName));
        $linkHandler->deleteAll($criteria, $force);
        //$sql = 'DELETE' . " FROM {$this->table_link}" . " WHERE  {$this->keyName} = " . $object->getVar($this->keyName);
        /*
                if (false ===  ($result = $this->db->{$queryFunc}($sql))) {
                   // xoops_error($this->db->error());
                }
        */
        /*
         * Remove stats-tag links
         */
        /** @var \XoopsModules\Tag\StatsHandler $statsHandler */
        $statsHandler = $helper->getHandler('Stats');
        $criteria     = new \Criteria($this->keyName, $object->getVar($this->keyName));
        $statsHandler->deleteAll($criteria, $force);
        //$sql = 'DELETE' . " FROM {$this->table_stats}" . " WHERE  {$this->keyName} = " . $object->getVar($this->keyName);

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
     * @param string $table_link
     * @param string $field_link
     * @param string $field_object
     * @return bool true on success
     */
    public function cleanOrphan($table_link = '', $field_link = '', $field_object = '')
    {
        require_once $GLOBALS['xoops']->path('/modules/tag/functions.recon.php');

        //mod_loadFunctions("recon");
        return \tag_cleanOrphan();
    }
}
