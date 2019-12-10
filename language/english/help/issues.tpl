
<script>
    $(function(){
      $("#includedContent").load("<{$xoops_url}>/modules/tag/include/issues.php");
    });
</script>
<div id="help-template" class="outer">
    <h1 class="head">Help:
        <a class="ui-corner-all tooltip" href="<{$xoops_url}>/modules/tag/admin/index.php"
           title="Back to the administration of Tag"> xForms <img src="<{xoAdminIcons home.png}>"
                                                                     alt="Back to the Administration of Tag">
        </a></h1>
    <!-- -----Help Content ---------- -->
    <h4 class="odd">Report Issues</h4>
    <p class="even">
        To report an issue with the module please go to <a href="https://github.com/XoopsModules25x/tag/issues/" target="_blank">https://github.com/XoopsModules25x/tag/issues/</a>.
    </p>
    <div id="includedContent"></div>
    <!-- -----Help Content ---------- -->
</div>
