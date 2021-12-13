<{if $tagbar|default:false}>
    <ul class="list-inline mb-0">
        <li class="list-inline-item"><{$tagbar.title}>:</li>
        <{foreach item=tag from=$tagbar.tags}>
             <{if $tag|strip_tags|lower|trim == $tag_title|strip_tags|lower}>
                <li class="list-inline-item"><{$tag|replace:"'>":"' class=\"text-warning\" > <span class=\"fa fa-tag\"></span> "}></li>
            <{else}>
                <li class="list-inline-item"><{$tag|replace:"'>":"' > <span class=\"fa fa-tag\"></span> "}></li>
            <{/if}>
        <{/foreach}>
    </ul>
<{/if}>