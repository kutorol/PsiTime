<div class="row">
    <div class="container text-center">
        <form action="<?=base_url()?>" method="post">
            <div class="col-xs-12 col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4">
                <div class="input-group col-xs-12">
                    <input type="text" class="form-control col-xs-12" name="login" placeholder="Логин" required>
                    <br><br>
                    <span class="label label-danger error"><?=strip_tags(form_error('login'))?></span>
                </div>
                <div class="input-group col-xs-12">
                    <input type="password" class="form-control col-xs-12" name="pass" placeholder="Пароль" required>
                    <br><br>
                    <span class="label label-danger error"><?=strip_tags(form_error('pass'))?></span>
                </div>

                <div class="input-group col-xs-12">
                    <button type="submit" class="btn btn-primary col-xs-12" name="enter_to_time">Войти</button>
                </div>

                <div class="row">
                    <a href="<?=base_url()?>welcome/registration" class="pull-left">Регистрация</a>
                    <a href="<?=base_url()?>welcome/forgot" class="pull-right">Забыли пароль?</a>
                </div>

            </div>

        </form>
    </div>
</div>
