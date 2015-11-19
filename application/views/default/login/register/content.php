<div class="row">
    <div class="container text-center">
        <form action="<?=$startUrl;?>/welcome/registration" method="post">
            <div class="col-xs-12 col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4">
                <div class="input-group col-xs-12">
                    <input type="text" class="form-control col-xs-12" name="name" value="<?=set_value("name")?>" placeholder="<?=$input_form_lang[1][$segment]?>" required>
                    <br><br>
                    <span class="label label-danger error"><?=strip_tags(form_error('name'))?></span>
                </div>
				
				<div class="input-group col-xs-12">
                    <input type="text" class="form-control col-xs-12" value="<?=set_value("login")?>" name="login" placeholder="<?=$input_form_lang[0][$segment]?>" required>
                    <br><br>
                    <span class="label label-danger error"><?=strip_tags(form_error('login'))?></span>
                </div>
				
				<div class="input-group col-xs-12">
                    <input type="email" class="form-control col-xs-12" value="<?=set_value("email")?>" name="email" placeholder="Email" required>
                    <br><br>
                    <span class="label label-danger error"><?=strip_tags(form_error('email'))?></span>
                </div>
				
				<div class="input-group col-xs-12">
                    <input type="password" class="form-control col-xs-12" name="pass" placeholder="<?=$input_form_lang[2][$segment]?>" required>
                    <br><br>
                    <span class="label label-danger error"><?=strip_tags(form_error('pass'))?></span>
                </div>
				
				<div class="input-group col-xs-12">
                    <input type="password" class="form-control col-xs-12" name="pass_too" placeholder="<?=$input_form_lang[3][$segment]?>" required>
                    <br><br>
                    <span class="label label-danger error"><?=strip_tags(form_error('pass_too'))?></span>
                </div>

                <div class="input-group col-xs-12">
                    <input type="number" class="form-control col-xs-12" name="hoursInDayToWork" placeholder="<?=$task_views[40]?>" required>
                    <br><br>
                    <span class="label label-danger error"><?=strip_tags(form_error('hoursInDayToWork'))?></span>
                </div>


                <div class="input-group col-xs-12">
                    <button type="submit" class="btn btn-primary col-xs-12" name="registration_btn"><?=$input_form_lang[6][$segment]?></button>
                </div>

                <div class="row">
                    <a href="<?=$startUrl;?>" class="pull-left"><?=$input_form_lang[5][$segment]?></a>
                    <a href="<?=$startUrl;?>/welcome/forgot" class="pull-right"><?=$input_form_lang[7][$segment]?></a>
                </div>

            </div>

        </form>
    </div>
</div>
