<form id="form-tag-jumpto" name="form-tag-jumpto" action="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.tag.php" method="get">
    <div class="form-group row m-1">
        <label class="col-md-3 text-center text-md-right  col-form-label" for="term"><{$smarty.const._MD_TAG_JUMPTO}>: </label>
        <div class="col-md-5">
            <input class="form-control" type="text" id="term" name="term" value="">
        </div>
        <div class="col text-center mt-2 mt-md-0 ">
            <button class="btn btn-primary" type="submit" name="submit" value="<{$smarty.const._SUBMIT}>"><{$smarty.const._SUBMIT}></button>
        </div>
    </div>
</form>