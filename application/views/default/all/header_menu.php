<div class="navbar navbar-default">
    <div class="container">

        <?php if($auth_user == true):?>
            <div class="navbar-header ">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#responsive-menu">
                    <!-- кнопка для мобилок сворачиающее меню -->
                    <span class="sr-only">open </span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>
            <div class="collapse navbar-collapse " id="responsive-menu">
                <ul class="nav navbar-nav">
                    <li><a href="#">Пункт меню</a></li>
                    <li><a href="#">Пункт меню</a></li>
                    <li><a href="<?=base_url()?>welcome/logout">Выход</a></li>
                </ul>
            </div>
        <?php else:?>
            <div class="navbar-header "></div>
        <?php endif;?>

    </div>
</div>