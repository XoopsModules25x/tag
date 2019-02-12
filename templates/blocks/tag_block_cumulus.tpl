<{ $xoTheme->addScript("browse.php?modules/tag/assets/js/swfobject.js") }>
<div id="tags" class=txtcenter>
    <{foreach item=tag from=$block.tags}>
        <a href="<{$xoops_url}>/modules/<{$block.tag_dirname}>/view.tag.php<{$smarty.const.URL_DELIMITER}><{$tag.id}>/"
           title="<{$tag.term}>"><{$tag.title}></a>
    <{/foreach}>

    <script type="text/javascript">
        var rnumber = Math.floor(Math.random() * 9999999);
        var widget_so = new SWFObject("<{$block.flash_params.flash_url}>?r=" + rnumber, "cumulusflash", "<{$block.flash_params.width}>", "<{$block.flash_params.height}>", "9", "<{$block.flash_params.background}>");
        <{$block.flash_params.transparency}>
        widget_so.addParam("allowScriptAccess", "always");
        widget_so.addVariable("tcolor", "<{$block.flash_params.tcolor}>");
        widget_so.addVariable("hicolor", "<{$block.flash_params.hicolor}>");
        widget_so.addVariable("tcolor2", "<{$block.flash_params.tcolor2}>");
        widget_so.addVariable("tspeed", "<{$block.flash_params.speed}>");
        widget_so.addVariable("distr", "true");
        widget_so.addVariable("mode", "tags");
        widget_so.addVariable("tagcloud", "<{$block.flash_params.tags_formatted_flash}>");
        widget_so.write("tags");
    </script>
</div>
<div class="more-link"><a href="<{$xoops_url}>/modules/tag/"><{$smarty.const._MORE}></a></div>
