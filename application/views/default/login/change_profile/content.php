<div class="row">
    <div class="container text-center">
        <form action="<?=$startUrl;?>/welcome/changeProfile" method="post">
            <div class="col-xs-12 col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4">

                <div class="input-group col-xs-12">
                    <input type="text" class="form-control col-xs-12" name="name" placeholder="<?=$input_form_lang[1][$segment]?>: <?=$userData['name']?>" >
                    <br><br>
                    <span class="label label-danger error"><?=strip_tags(form_error('name'))?></span>
                </div>

                <div class="input-group col-xs-12">
                    <input type="text" class="form-control col-xs-12" name="login" placeholder="<?=$input_form_lang[0][$segment]?>: <?=$userData['login']?>" >
                    <br><br>
                    <span class="label label-danger error"><?=strip_tags(form_error('login'))?></span>
                </div>

                <div class="input-group col-xs-12">
                    <input type="email" class="form-control col-xs-12" name="email" placeholder="Email: <?=$userData['email']?>" >
                    <br><br>
                    <span class="label label-danger error"><?=strip_tags(form_error('email'))?></span>
                </div>


                <div class="input-group col-xs-12">
                    <button type="submit" class="btn btn-primary col-xs-12" name="change_profile"><?=$input_form_lang[13][$segment]?></button>
                </div>



            </div>

        </form>
    </div>
</div>
