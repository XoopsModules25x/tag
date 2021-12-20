<!-- phppp (D.J.): http://xoopsforge.com; https://xoops.org.cn -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><span class="fa fa-tags text-secondary fa-lg fa-fw mr-2 mt-1"></span><a href="index.php"><{$module_name}></a></li>
        <li class="breadcrumb-item active"><{$smarty.const._MD_TAG_TAGS}></li>
<!--
    <li class="breadcrumb-item active"><a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/list.tag.php<{$smarty.const.URL_DELIMITER}><{$tag_term|default:''}>" title="<{$tag_page_title|strip_tags}>" rel="tag"><{$tag_page_title|regex_replace:'/^.+g>/U':''|replace:'</strong>':''}></a></li>
-->
    </ol>
</nav>

<div class="border pt-2 pb-3 my-4 px-2 mx-2 rounded text-center">
    <{foreach item=tag from=$tags}>
        <span class="tag-level-<{$tag.level}> mx-1 text-nowrap" style="font-size: <{$tag.font}>%;">
            <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.tag.php<{$smarty.const.URL_DELIMITER}><{$tag.term}>" title="<{$tag.title}>" rel="tag"><{$tag.title}></a>
        </span>
    <{/foreach}>
</div>

<form id="form-tag-jumpto" name="form-tag-jumpto" action="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.tag.php" method="get">
    <div class="form-row justify-content-center py-1 py-md-3">
        <div class="col-12 col-md-4 text-md-right text-center">
            <label class="text-right col-form-label" for="term"><{$smarty.const._MD_TAG_JUMPTO}>: </label>
        </div>
        <div class="col-7 col-md-4 text-md-right text-center">
            <input class="form-control" type="text" id="term" name="term" value="">
        </div>
        <div class="col-9 col-md-4 text-md-left text-center mt-2 mt-md-0">
            <button class="btn btn-primary" type="submit" name="submit" value="<{$smarty.const._SUBMIT}>"><{$smarty.const._SUBMIT}></button>
        </div>
    </div>
</form>

<{if $pagenav|default:false}>
    <div id="pagenav" class="pt-5">
        <{$pagenav}>
    </div>
<{/if}>