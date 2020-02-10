
<script>
    $(function(){
      $("#includedContent").load("<{$xoops_url}>/modules/tag/include/issues.php");
    });
</script>
<div id="help-template" class="outer">
    <{include file=$smarty.const._MI_TAG_HELP_HEADER}>
    <!-- -----Help Content ---------- -->
    <h4 class="odd">Report Issues</h4>
    <p class="even">
        To report an issue with the module please go to <a href="https://github.com/XoopsModules25x/tag/issues/" target="_blank">https://github.com/XoopsModules25x/tag/issues/</a>.
    </p>
    <div id="includedContent"></div>
    <!-- -----Help Content ---------- -->
</div>
