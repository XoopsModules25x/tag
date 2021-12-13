<!-- phppp (D.J.): http://xoopsforge.com; https://xoops.org.cn -->

<nav aria-label="breadcrumb">
    <ol class="breadcrumb" >
        <li class="breadcrumb-item active"><span class="fa fa-tags text-secondary fa-lg fa-fw mr-2 mt-1"></span><{$module_name}></li>
    </ol>
</nav>

<!--
<a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/list.tag.php" title="<{$smarty.const._MD_TAG_TAGS}>"><{$smarty.const._MD_TAG_TAGS}></a><br />
<a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/list.tag.php<{$smarty.const.URL_DELIMITER}><{$tag_term}>" title="<{$tag_page_title|strip_tags}>" rel="tag"><{$tag_page_title|regex_replace:'/^.+g>/U':''|replace:'</strong>':''}></a><br />
<a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/list.tag.php<{$smarty.const.URL_DELIMITER}><{$tag_term}>" title="<{$tag_page_title|strip_tags}>" rel="tag"><{$tag_page_title}></a>
-->

<form id="form-tag-jumpto" name="form-tag-jumpto" action="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.tag.php" method="get">
    <div class="form-row justify-content-center py-3">
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

<div class="border pt-2 pb-3 my-4 px-2 rounded text-center">
    <{foreach item=tag from=$tags}>
        <span class="tag-level-<{$tag.level}> mx-1 text-nowrap" style="font-size: <{$tag.font*1.15}>%;">
            <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.tag.php<{$smarty.const.URL_DELIMITER}><{$tag.term}>" title="<{$tag.title}>" rel="tag"><{$tag.title}></a>
        </span>
    <{/foreach}>
</div>

<div id="pagenav" class="text-center py-2 mb-3">
    <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/list.tag.php" title="<{$smarty.const._MD_TAG_TAGS}>" class="btn btn-secondary px-4" role="button" ><{$smarty.const._MD_TAG_TAGS}>...</a>
<!--
   <{$pagenav}> 
-->
</div>

