<div class="row">
    <div class="container text-center">
        <form action="<?=base_url()?><?=$segment?>" method="post">
            <div class="col-xs-12 col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4">
                <div class="input-group col-xs-12">
                    <input type="text" class="form-control col-xs-12" name="login" placeholder="<?=$input_form_lang[0][$segment]?>" required>
                    <br><br>
                    <span class="label label-danger error"><?=strip_tags(form_error('login'))?></span>
                </div>
                <div class="input-group col-xs-12">
                    <input type="password" class="form-control col-xs-12" name="pass" placeholder="<?=$input_form_lang[2][$segment]?>" required>
                    <br><br>
                    <span class="label label-danger error"><?=strip_tags(form_error('pass'))?></span>
                </div>

                <div class="input-group col-xs-12">
                    <button type="submit" class="btn btn-primary col-xs-12" name="enter_to_time"><?=$input_form_lang[5][$segment]?></button>
                </div>

                <div class="row">
                    <a href="<?=base_url()?><?=$segment?>/welcome/registration" class="pull-left"><?=$input_form_lang[6][$segment]?></a>
                    <a href="<?=base_url()?><?=$segment?>/welcome/forgot" class="pull-right"><?=$input_form_lang[7][$segment]?></a>
                </div>

            </div>

        </form>
    </div>
</div>
