<div id="help-template" class="outer">
    <h1 class="head">Help:
        <a class="ui-corner-all tooltip" href="<{$xoops_url}>/modules/tag/admin/index.php"
           title="Back to the administration of Tag"> Tag <img src="<{xoAdminIcons home.png}>"
                                                                     alt="Back to the Administration of Tag">
        </a></h1>
    <!-- -----Help Content ---------- -->
    <h2 class="odd">Plugin Development</h2>
    <div class="even marg10 boxshadow1">
    <p>Below are instructions to be able to develop a Tag plugin for a XOOPS module.</p>
    </div>

    <h3 class="odd" id="checkbox_element">Tag Background</h3>
    <div class="even marg10 boxshadow1">
    <p>For more information on tags check <a href="https://en.wikipedia.org/wiki/Tag_(metadata)" target="_blank" rel="external">https://en.wikipedia.org/wiki/Tag_(metadata)</a></p>
    </div>
    <h3 class="odd" id="checkbox_element">Develop a Plugin</h3>
    <div class="even marg10 boxshadow1">
    <p>The following steps are needed to enable tag for a module ("mymodule"):</p>
    <ol>
      <li>Add tag input box to your module's item edit form (required)</li>
      <li>Add tag storage to your item submission page (required)</li>
	  <li>Removing tags (required)</li>
      <li>Define functions to build info of tagged items (required)</li>
      <li>Add tag display API to your item display page and include tag template in your item template (optional)</li>
      <li>Add module tag view page and tag list page (optional)</li>
      <li>Add module tag blocks (optional)</li>
    </ol>
    </div>

    <h3 class="odd" id="tag_input_box">Step 1: Add tag input box</h3>
    <div class="even marg10 boxshadow1">
    <table>
    <tbody>
    <tr><td>File:</td><td>edit.item.php</td></tr>
    <tr><td>Code:</td>
    <td><{literal}>
<code>$itemid = $item_obj->isNew() ? 0 : $item_obj->getVar('itemid');
XoopsLoad::load('formtag', 'tag');  // get the TagFormTag class
$form_item->addElement(new \XoopsModules\Tag\FormTag('item_tag', 60, 255, $itemid, $catid = 0));
    <{/literal}></code></td></tr>
    </tbody>
    </table>
    </div>

    <h3 class="odd" id="tag_input_box">Step 2: Add tag storage after item storage</h3>
    <div class="even marg10 boxshadow1">
    <table>
    <tbody>
    <tr><td>File:</td><td>submit.item.php</td></tr>
    <tr><td>Code:</td>
    <td><{literal}>
<code>/** @var \XoopsModules\Tag\TagHandler $tagHandler */
$tagHandler = \XoopsModules\Tag\Helper::getInstance()->getHandler('Tag'); // xoops_getModuleHandler('tag', 'tag');
$tagHandler->updateByItem($_POST['item_tag'], $itemid, $GLOBALS['xoopsModule']->getVar('dirname'), $catid = 0);
    </code><{/literal}></td></tr>
    </tbody>
    </table>
    </div>
	
	<h3 class="odd" id="tag_input_box">Step 3: Removing tags (When content is deleted)</h3>
    <div class="even marg10 boxshadow1">
    <table>
    <tbody>
    <tr><td>File:</td><td>delete.item.php</td></tr>
    <tr><td>Code:</td>
    <td><{literal}>
<code>/** @var \XoopsModules\Tag\TagHandler $tagHandler */
$tagHandler = \XoopsModules\Tag\Helper::getInstance()->getHandler('Tag'); // xoops_getModuleHandler('tag', 'tag');
$tagHandler->updateByItem('', $itemid, $GLOBALS['xoopsModule']->getVar('dirname'), $catid = 0);
    </code><{/literal}></td></tr>
    </tbody>
    </table>
    </div>

    <h3 class="odd" id="tag_input_box">Step 4: Define functions to build info of tagged itemse</h3>
    <div class="even marg10 boxshadow1">
    <table>
    <tbody>
    <tr><td>File:</td><td>/modules/tag/plugin/mymodule.php<br>OR<br>/modules/mymodule/include/plugin.tag.php</td></tr>
    <tr><td>Code:</td>
    <td><{literal}>
<code>/** Get item fields: title, content, time, link, uid, uname, tags *
 * @param array $items
 */
function mymodule_tag_iteminfo(array &$items): bool
{
    if (empty($items) || !is_array($items)) {
    return false;
    }
    $items_id = [];
    foreach (array_keys($items) as $cat_id) {
        // Some handling here to build the link upon catid
        // If catid is not used, just skip it
        foreach (array_keys($items[$cat_id]) as $item_id) {
            // In article, the item_id is "art_id"
            $items_id[] = (int)$item_id;
        }
    }
    $itemHandler = $helper->getHandler('Item', 'module');
    $items_obj   = $itemHandler->getObjects(new \Criteria('itemid', '(' . implode(', ', $items_id) . ')', 'IN'), true);

    foreach (array_keys($items) as $cat_id) {
        foreach (array_keys($items[$cat_id]) as $item_id) {
            $item_obj                 = $items_obj[$item_id];
            $items[$cat_id][$item_id] = [
                'title'   => $item_obj->getVar('item_title'),
                'uid'     => $item_obj->getVar('uid'),
                'link'    => "view.item.php?itemid={$item_id}",
                'time'    => $item_obj->getVar('item_time'),
                'tags'    => \XoopsModules\Tag\Utility::tag_parse_tag($item_obj->getVar('item_tags', 'n')), // optional
                'content' => '',
            ];
        }
    }
    unset($items_obj);
    return true;
}

/** Remove orphan tag-item links *
 * @param $mid
 */
function mymodule_tag_synchronization($mid)
{
    // Optional
}
    <{/literal}></code></td></tr>
    </tbody>
    </table>
    </div>

    <h3 class="odd" id="tag_input_box">Step 5: Display tags on the item page</h3>
    <div class="even marg10 boxshadow1">
    <table>
    <tbody>
    <tr><td>File:</td><td>view.item.php</td></tr>
    <tr><td>Code:</td>
    <td><{literal}>
<code>require_once $GLOBALS['xoops']->path('/modules/tag/include/tagbar.php');
$GLOBALS['xoopsTpl']->assign('tagbar', tagBar($itemid, $catid = 0));
// File: mymodule_item_template.tpl
$GLOBALS['xoopsTpl']->display('db:tag_bar.tpl');
    <{/literal}></code></td></tr>
    </tbody>
    </table>
    </div>

    <h3 class="odd" id="tag_input_box">Step 6: Create tag list page and tag view page</h3>
    <div class="even marg10 boxshadow1">
    <table>
    <tbody>
    <tr><td>File:</td><td>view.item.php</td></tr>
    <tr><td>Code:</td>
    <td><{literal}>
<code>require_once __DIR__ . '/header.php';
require_once $GLOBALS['xoops']->path('/modules/tag/list.tag.php');
    <{/literal}></code></td></tr>
    <tr><td>File:</td><td>view.tag.php</td></tr>
    <tr><td>Code:</td>
    <td><{literal}>
<code>require_once __DIR__ . '/header.php';
require_once $GLOBALS['xoops']->path('/modules/tag/view.tag.php');
    <{/literal}></code></td></tr>
    </tbody>
    </table>
    </div>

    <h3 class="odd" id="tag_input_box">Step 7: Create tag blocks</h3>
    <div class="even marg10 boxshadow1">
    <table>
    <tbody>
    <tr><td>File:</td><td>xoopsversion.php</td></tr>
    <tr><td>Code:</td>
    <td><{literal}>
<code>/*
 * $options:
 *                    $options[0] - number of tags to display
 *                    $options[1] - time duration, in days, 0 for all the time
 *                    $options[2] - max font size (px or %)
 *                    $options[3] - min font size (px or %)
 */
$modversion['blocks'][] = [
    'file'        => 'mymodule_block_tag.php',
    'name'        => 'Module Tag Cloud',
    'description' => 'Show tag cloud',
    'show_func'   => 'mymodule_tag_block_cloud_show',
    'edit_func'   => 'mymodule_tag_block_cloud_edit',
    'options'     => '100|0|150|80',
    'template'    => 'mymodule_tag_block_cloud.tpl',
];
/*
 * $options:
 *                    $options[0] - number of tags to display
 *                    $options[1] - time duration, in days, 0 for all the time
 *                    $options[2] - sort: a - alphabet; c - count; t - time
 */
$modversion['blocks'][] = [
    'file'        => 'mymodule_block_tag.php',
    'name'        => 'Module Top Tags',
    'description' => 'Show top tags',
    'show_func'   => 'mymodule_tag_block_top_show',
    'edit_func'   => 'mymodule_tag_block_top_edit',
    'options'     => '50|30|c',
    'template'    => 'mymodule_tag_block_top.tpl',
];
<{/literal}></code></td></tr>
    <tr><td>File:</td><td>./blocks/module_block_tag.php</td></tr>
    <tr><td>Code:</td>
    <td><{literal}>
<code>/**
 * @param $options
 * @return array
 */
function mymodule_tag_block_cloud_show($options)
{
    require_once $GLOBALS['xoops']->path('/modules/tag/blocks/block.php');

    return tag_block_cloud_show($options, $moduleDirName);
}

/**
 * @param $options
 * @return string
 */
function mymodule_tag_block_cloud_edit($options)
{
    require_once $GLOBALS['xoops']->path('/modules/tag/blocks/block.php');

    return tag_block_cloud_edit($options);
}

/**
 * @param $options
 * @return array
 */
function mymodule_tag_block_top_show($options)
{
    require_once $GLOBALS['xoops']->path('/modules/tag/blocks/block.php');

    return tag_block_top_show($options, $moduleDirName);
}

/**
 * @param $options
 * @return string
 */
function mymodule_tag_block_top_edit($options)
{
    require_once $GLOBALS['xoops']->path('/modules/tag/blocks/block.php');

    return tag_block_top_edit($options);
}
<{/literal}></code></td></tr>
    <tr><td>File:</td><td>./templates/mymodule_tag_block_cloud.tpl</td></tr>
    <tr><td>Code:</td>
    <td><{literal}>
<code><{include file = 'db:tag_block_cloud.tpl'}>
    <{/literal}></code></td></tr>
    <tr><td>File:</td><td>./templates/mymodule_tag_block_top.php</td></tr>
    <tr><td>Code:</td>
    <td><{literal}>
<code><{include file = 'db:tag_block_top.tpl'}>
    <{/literal}></code></td></tr>
    </tbody>
    </table>
    </div>
</div>
