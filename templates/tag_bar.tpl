<{if $tagbar}>
    <strong><{$tagbar.title}>:</strong>
    <{foreach item=tag from=$tagbar.tags}><{$tagbar.delimiter}> <{$tag}>&nbsp;&nbsp;<{/foreach}>
<{/if}>
