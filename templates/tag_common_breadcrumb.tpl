<div class="tag_headertable">
    <div class="tag_breadcrumb">
        <{foreach item=bread from=$breadcrumb name=bcloop}>
            <span class="bread">
    <{if ($bread.link)}>
        <a href="<{$bread.link}>" title="<{$bread.title}>"><{$bread.title}></a>
    <{else}>
        <{$bread.title}>
    <{/if}>
    </span>
            <{if !$smarty.foreach.bcloop.last}>
                <span class="delimiter">&gt;</span>
            <{/if}>
        <{/foreach}>
        <hr>
    </div>
</div>
