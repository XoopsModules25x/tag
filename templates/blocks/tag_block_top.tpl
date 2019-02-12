<div class="tag-list">
    <{foreach item=tag from=$block.tags}>
        <span style="display: block; padding: 2px 5px;">
    <a href="<{$xoops_url}>/modules/<{$block.tag_dirname}>/view.tag.php<{$smarty.const.URL_DELIMITER}><{$tag.term}>/"
       title="<{$tag.term}>" rel="tag"><{$tag.term}></a> (<{$tag.count}>)
</span>
    <{/foreach}>
</div>
