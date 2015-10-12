<div class="row">
    <div class="container text-center">
        <form action="<?=$startUrl;?>/welcome/changePassword" method="post">
            <div class="col-xs-12 col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4">
                <div class="input-group col-xs-12">
                    <input type="text" class="form-control col-xs-12" value="<?=set_value("passOld")?>" name="passOld" placeholder="<?=$input_form_lang[10][$segment]?>" required>
                    <br><br>
                    <span class="label label-danger error"><?=strip_tags(form_error('passOld'))?></span>
                </div>

                <div class="input-group col-xs-12">
                    <input type="text" class="form-control col-xs-12" value="<?=set_value("passNew")?>" name="passNew" placeholder="<?=$input_form_lang[11][$segment]?>" required>
                    <br><br>
                    <span class="label label-danger error"><?=strip_tags(form_error('passNew'))?></span>
                </div>

                <div class="input-group col-xs-12">
                    <input type="text" class="form-control col-xs-12" value="<?=set_value("passNewRepeat")?>" name="passNewRepeat" placeholder="<?=$input_form_lang[12][$segment]?>" required>
                    <br><br>
                    <span class="label label-danger error"><?=strip_tags(form_error('passNewRepeat'))?></span>
                </div>

                <div class="input-group col-xs-12">
                    <button type="submit" class="btn btn-primary col-xs-12" name="change_pass"><?=$input_form_lang[9][$segment]?></button>
                </div>

                <div class="row text-center">
                    <a href="<?=$startUrl;?>/task" class="pull-left"><?=$header_menu_lang[0][$segment]?></a>
                </div>

            </div>

        </form>
    </div>
</div>
