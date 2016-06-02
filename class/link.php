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
 * @package         tag
 * @subpackage      class
 * @copyright       {@link http://sourceforge.net/projects/xoops/ The XOOPS Project}
 * @license         {@link http://www.fsf.org/copyleft/gpl.html GNU public license}
 * @author          Taiwen Jiang <phppp@users.sourceforge.net>
 * @since           1.00
 */

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

/**
 * Class TagLink
 */
class TagLink extends XoopsObject
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->initVar('tl_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('tag_id', XOBJ_DTYPE_INT, 0);
        $this->initVar('tag_modid', XOBJ_DTYPE_INT, 0);
        $this->initVar('tag_catid', XOBJ_DTYPE_INT, 0);
        $this->initVar('tag_itemid', XOBJ_DTYPE_INT, 0);
        $this->initVar('tag_time', XOBJ_DTYPE_INT, 0);
    }
}

/**
 * Tag link handler class.
 * @package     tag
 *
 * @author      Taiwen Jiang <phppp@users.sourceforge.net>
 * @copyright   copyright &copy; The XOOPS Project
 *
 * {@link XoopsPersistableObjectHandler}
 *
 */
class TagLinkHandler extends XoopsPersistableObjectHandler
{
    public $table_stats;

    /**
     * TagLinkHandler constructor.
     * @param XoopsDatabase $db
     */
    public function __construct(XoopsDatabase $db)
    {
        parent::__construct($db, 'tag_link', 'TagLink', 'tl_id', 'tag_itemid');
        $this->table_stats = $this->db->prefix('tag_stats');
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
        return parent::cleanOrphan($this->db->prefix('tag_tag'), 'tag_id');
    }
}
