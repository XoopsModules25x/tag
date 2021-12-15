<div class="my-2 px-md-5 px-1 rounded text-center">
    <{foreach item=tag from=$block.tags}>
        <span class="tag-level-<{$tag.level}> mx-1 text-nowrap" style="font-size: <{$tag.font}>%">
            <a href="<{$xoops_url}>/modules/<{$block.tag_dirname}>/view.tag.php<{$smarty.const.URL_DELIMITER}><{$tag.term}>/" title="<{$tag.title}>" rel="tag"><{$tag.title}></a>
        </span>
    <{/foreach}>
</div>
