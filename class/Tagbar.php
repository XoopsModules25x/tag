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
 * @copyright      {@link https://sourceforge.net/projects/xoops/ The XOOPS Project}
 * @license        {@link https://www.fsf.org/copyleft/gpl.html GNU public license}
 * @author         Taiwen Jiang <phppp@users.sourceforge.net>
 * @since          1.00
 */
(\defined('XOOPS_ROOT_PATH') && ($GLOBALS['xoopsModule'] instanceof \XoopsModule)) || exit('Restricted access');

class Tagbar
{
    /**
     * Display tag list
     *
     * @param int|array $tags  array of tag string
     *                         OR
     * @param int       $catid
     * @param int       $modid
     * @return array
     *                         {@internal param int $itemid }}
     */
    public function getTagbar($tags, $catid = 0, $modid = 0): array
    {
        static $loaded, $delimiter;

        if (empty($tags)) {
            return [];
        }

        $helper = Helper::getInstance();

        if (null === $loaded) {
            require_once $helper->path('include/vars.php');
            //require_once $helper->path('include/functions.php');
            Utility::tag_define_url_delimiter();
            $helper->loadLanguage('main'); // load Main lang file
            /*
            if (!($GLOBALS['xoopsModule'] instanceof \XoopsModule)
                || ('tag' !== $GLOBALS['xoopsModule']->getVar('dirname'))) {
                $helper->loadLanguage('main');
            }
            */
            if (\file_exists($helper->path('assets/images/delimiter.gif'))) {
                $delimiter = "<img src='" . $helper->url('assets/images/delimiter.gif') . "' alt=''>";
            } else {
                $delimiter = "<img src='" . $GLOBALS['xoops']->url('www/images/pointer.gif') . "' alt=''>";
            }
            $loaded = 1;
        }

        // itemid
        if (\is_numeric($tags)) {
            if (empty($modid) && ($GLOBALS['xoopsModule'] instanceof \XoopsModule)) {
                $modid = $GLOBALS['xoopsModule']->getVar('mid');
            }
            /** @var \XoopsModules\Tag\TagHandler $tagHandler */
            $tagHandler = $helper->getHandler('Tag');
            if (!$tags = $tagHandler->getByItem($tags, $modid, $catid)) {
                return [];
            }
            // if ready, do nothing
        } elseif (\is_array($tags)) {
            // parse
        } elseif (!$tags = Utility::tag_parse_tag($tags)) {
            return [];
        }
        $tags_data = [];
        foreach ($tags as $tag) {
            $tags_data[] = "<a href='" . $helper->url('view.tag.php' . URL_DELIMITER . \urlencode($tag)) . "' title='" . \htmlspecialchars($tag, \ENT_QUOTES | \ENT_HTML5) . "'>" . \htmlspecialchars($tag, \ENT_QUOTES | \ENT_HTML5) . '</a>';
        }

        return [
            'title'     => \_MD_TAG_TAGS,
            'delimiter' => $delimiter,
            'tags'      => $tags_data,
        ];
    }
}
