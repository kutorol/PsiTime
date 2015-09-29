<div class="row">
    <div class="container text-center">
        <form action="<?=$startUrl;?>/welcome/forgot" method="post">
            <div class="col-xs-12 col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4">
                <div class="input-group col-xs-12">
                    <input type="text" class="form-control col-xs-12" name="nameProject" placeholder="<?=$task_views[1]?>" required>
                    <br><br>
                    <span class="label label-danger error"><?=strip_tags(form_error('nameProject'))?></span>
                </div>

                <div class="input-group col-xs-12">
                    <input type="text" class="form-control col-xs-12 ui-autocomplete-input" id="userAutocomplete" autocomplete="off" role="textbox" aria-autocomplete="list" aria-haspopup="true" name="mainUser" placeholder="<?=$task_views[2]?>" required>
                    <input type="hidden" name="value" id="userAutocompleteHide" value="">
                    <br><br>
                    <span class="label label-danger error" id="autocomplete_error"  data-role="tagsinput" ><?=strip_tags(form_error('mainUser'))?></span>
                </div>

                <div class="input-group col-xs-12">
                    <button type="submit" class="btn btn-primary col-xs-12" name="addProject_btn"><?=$task_views[0]?></button>
                </div>

                <div class="row">
                    <a href="<?=$startUrl;?>/task" class="pull-left"><?=$header_menu_lang[0][$segment]?></a>
                </div>

            </div>

        </form>
    </div>
</div>
