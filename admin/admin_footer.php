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
 * @author          Mamba {@link http://www.xoops.org}
 * @since           2.31
 * @version         $Id: admin_footer.php 12898 2014-12-08 22:05:21Z zyspec $
 */
/*
echo "<div class='adminfooter'>\n"
    ."  <div class='txtcenter'>\n"
    ."    <a href='http://www.xoops.org' rel='external'><img src='{$pathIcon32}/xoopsmicrobutton.gif' alt='XOOPS' title='XOOPS'></a>\n"
    ."  </div>\n"
    ."  " . _AM_TAG_FOOTER . "\n"
    ."</div>";
    */
echo "<div class='adminfooter'>\n"
   . "  <div class='center'>\n"
   . "    <a href='" . $GLOBALS['xoopsModule']->getInfo('author_website_url') . "' target='_blank'><img src='{$pathIcon32}/xoopsmicrobutton.gif' alt='" . $GLOBALS['xoopsModule']->getInfo('author_website_name') . "' title='" . $GLOBALS['xoopsModule']->getInfo('author_website_name') . "' /></a>\n"
   . "  </div>\n"
   . "  <div class='center smallsmall italic pad5'>\n"
   . "    " . _AM_TAG_MAINTAINED_BY
   . " <a class='tooltip' rel='external' href='http://" . $GLOBALS['xoopsModule']->getInfo('module_website_url') . "' "
   . "title='" . _AM_TAG_MAINTAINED_TITLE . "'>" . _AM_TAG_MAINTAINED_TEXT . "</a>\n"
   . "  </div>\n"
   . "</div>";

xoops_cp_footer();
