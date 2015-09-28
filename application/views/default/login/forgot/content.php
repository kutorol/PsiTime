<div class="row">
    <div class="container text-center">
        <form action="<?=$startUrl;?>/welcome/forgot" method="post">
            <div class="col-xs-12 col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4">
                <div class="input-group col-xs-12">
                    <input type="email" class="form-control col-xs-12" name="email" placeholder="Email" required>
                    <br><br>
                    <span class="label label-danger error"><?=strip_tags(form_error('email'))?></span>
                </div>

                <div class="input-group col-xs-12">
                    <button type="submit" class="btn btn-primary col-xs-12" name="forgot_btn"><?=$input_form_lang[8][$segment]?></button>
                </div>

                <div class="row">
                    <a href="<?=$startUrl;?>" class="pull-left"><?=$input_form_lang[5][$segment]?></a>
                    <a href="<?=$startUrl;?>/welcome/registration" class="pull-right"><?=$input_form_lang[6][$segment]?></a>
                </div>

            </div>

        </form>
    </div>
</div>
