<?php declare(strict_types=1);
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
 * @author          Mamba {@link https://xoops.org}
 * @since           2.31
 */
$pathIcon32 = Xmf\Module\Admin::iconUrl('', '32');

echo "<div class='adminfooter'>\n" . "<div class='center'>\n" . "  <a href='https://xoops.org' rel='external' target='_blank'><img src='{$pathIcon32}/xoopsmicrobutton.gif' 'alt='XOOPS' title='XOOPS'></a>\n" . "</div>\n" . _AM_MODULEADMIN_ADMIN_FOOTER . "\n" . "</div>\n";

xoops_cp_footer();
