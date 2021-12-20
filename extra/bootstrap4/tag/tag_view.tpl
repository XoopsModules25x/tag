<nav aria-label="breadcrumb">
    <ol class="breadcrumb" >
        <li class="breadcrumb-item"><span class="fa fa-tags text-secondary fa-lg fa-fw mr-2 mt-1"></span><a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/"><{$module_name}></a></li>
        <li class="breadcrumb-item"><a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/list.tag.php" title="<{$smarty.const._MD_TAG_TAGS}>"><{$smarty.const._MD_TAG_TAGS}></a></li>
        <li class="breadcrumb-item active" aria-current="page"><{$tag_title}></li>
<!--
    <li class="breadcrumb-item active"><a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.tag.php<{$smarty.const.URL_DELIMITER}><{$tag_term}>" title="<{$tag_title}>" rel="tag"><{$tag_title}></a></li>
-->    
    </ol>
</nav>

<div class="d-flex justify-content-between flex-wrap">
    <div class="flex-grow-1">
        <h5><a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.tag.php<{$smarty.const.URL_DELIMITER}><{$tag_term}>" title="<{$tag_title}>" rel="tag" class="text-warning"><span class="fa fa-tag fa-lg fa-fw mt-1"></span> <{$tag_title}></a></h5>
    </div>
    <{if $pagenav|default:false}>
        <div class="mt-1">
            <div id="pagenav" class="text-right"><{$pagenav}></div>
        </div>
    <{/if}>
</div>

<div class="mb-3">
    <{foreach item=article from=$tag_articles}>
        <div class="card mt-3">
            <div class="card-body">	
                <span class="fa fa-list fa-lg"></span> <a href="<{$xoops_url}>/modules/<{$article.dirname}>/" title="<{$article.module}>"><{$article.module}></a>: 
                <a href="<{$xoops_url}>/modules/<{$article.dirname}>/<{$article.link}>" title="<{$article.title}>"><span class="font-weight-bold"><{$article.title}></span></a>
                <span class="badge badge-secondary badge-pill text-muted">
                <span class="fa fa-user fa-sm"></span> <a href="<{$xoops_url}>/userinfo.php?uid=<{$article.uid}>" title="<{$article.uname}>"><{$article.uname}></a>
                <span class="fa fa-calendar fa-sm ml-2"></span> <{$article.time}></span>
				
                <div class="tag-item-content">
                    <{$article.content}>
                </div>
			</div>

            <{assign var=tagbar value=$article.tags}>
            <{if $tagbar|default:false}>
                <div class="card-footer text-muted">
                    <{include file="db:tag_bar.tpl"}>
                </div>    
            <{/if}>
        </div>
    <{/foreach}>
    
    <{if $pagenav|default:false}>
        <div id="pagenav" class="text-right mt-3"><{$pagenav}></div>
    <{/if}>
</div>

<{if $tag_addon|default:false}>
    <div class="tag-item-meta mt-3 text-center bg-secondary rounded p-1 mb-3">
        <{$tag_addon.title}>:<br />
        <{foreach item=addon from=$tag_addon.addons}>
            <span class="mx-3 text-nowrap"><span class="fa fa-external-link text-muted"></span> <{$addon}></span>
        <{/foreach}>
    </div>
<{/if}>

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