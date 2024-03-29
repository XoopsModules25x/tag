<!-- phppp (D.J.): https://xoopsforge.com; https://xoops.org.cn -->

<div class="tag-page-title">
    <h3><{$tag_breadcrumb}></h3>
    <{* <h3><a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/" title="<{$tag_page_title}>"><{$tag_page_title}></a></h3> *}>
    <{* <h3><{$tag_page_title}></h3> *}><{* removed unnecessary link - just reloads this page *}>
</div>

<div class="tag-jumpto" style="padding-top: 10px;">
    <form id="form-tag-jumpto" name="form-tag-jumpto" action="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.tag.php"
          method="get">
        <div>
            <label for="term" style="float: left; margin-right: 1em;"><{$lang_jumpto}>: </label><input type="text" id="term" name="term" value="" size="20" class="right"> <input type="submit" name="submit" value="<{$smarty.const._SUBMIT}>">
        </div>
    </form>
</div>

<div class="tag-cloud" style="margin-top: 10px; padding: 10px; border: solid 2px #ddd; line-height: 150%;">
    <{foreach item=tag from=$tags}>
        <span class="tag-item tag-level-<{$tag.level}>" style="font-size: <{$tag.font}>%; margin-right: 5px;">
            <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.tag.php<{$smarty.const.URL_DELIMITER}><{$tag.term}>"
            title="<{$tag.title}>" rel="tag"><{$tag.title}></a>
        </span>
    <{/foreach}>
</div>


<div id="pagenav" style="padding-top: 10px;">
    <{$pagenav}>
</div>
