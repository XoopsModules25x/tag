<div class="tag-cloud" style="line-height: 150%; padding: 5px;">
    <{foreach item=tag from=$block.tags}>
        <span class="tag-level-<{$tag.level}>" style="font-size: <{$tag.font}>%; display: inline; padding-right: 5px;">
        <a href="<{$xoops_url}>/modules/<{$block.tag_dirname}>/view.tag.php<{$smarty.const.URL_DELIMITER}><{$tag.term}>/"
           title="<{$tag.title}>" rel="tag"><{$tag.title}></a>
    </span>
    <{/foreach}>
</div>
