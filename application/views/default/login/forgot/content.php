<div class="row">
    <div class="container text-center">
        <form action="<?=base_url()?>welcome/forgot" method="post">
            <div class="col-xs-12 col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4">
                <div class="input-group col-xs-12">
                    <input type="email" class="form-control col-xs-12" name="email" placeholder="Email" required>
                    <br><br>
                    <span class="label label-danger error"><?=strip_tags(form_error('email'))?></span>
                </div>

                <div class="input-group col-xs-12">
                    <button type="submit" class="btn btn-primary col-xs-12" name="forgot_btn">Востановить</button>
                </div>

                <div class="row">
                    <a href="<?=base_url()?>" class="pull-left">Войти</a>
                    <a href="<?=base_url()?>welcome/registration" class="pull-right">Регистрация</a>
                </div>

            </div>

        </form>
    </div>
</div>
