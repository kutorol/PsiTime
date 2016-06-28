<div class="navbar navbar-default">
    <div class="container">


            <div class="navbar-header ">
                <?php if($auth_user === true):?>
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#responsive-menu">
                    <!-- кнопка для мобилок сворачивающееся меню -->
                    <span class="sr-only">Open </span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <?php endif;?>
            </div>
            <div class="collapse navbar-collapse" id="responsive-menu">
                <ul class="nav navbar-nav">

                    <li>
                        <a class="navbar-brand" href="<?=$startUrl?><?=($login != "") ? "/task" : "";?>">
                            PsiTime <span style="color: rgb(91, 192, 222);">&beta;eta</span>
                        </a>
                    </li>

                    <li>
                        <div class="btn-group alignCenter">

                            <button style="margin-top: 8px;" class="btn btn-info dropdown-toggle" data-toggle="dropdown" data-original-title="<?=$languages_desc[0][$segment]?>" title="<?=$languages_desc[0][$segment]?>">
                                &nbsp; <?=$languages_desc[0][$segment]?> &nbsp; <i class="fa fa-globe"></i>
                            </button>
                            <ul class="dropdown-menu alignL">
                                <?php foreach($languages_desc as $k=>$v):?>
                                    <?php if($k !== 0 && is_string($k)):?>
                                        <li><a href="<?=base_url()?><?=$k?>/<?=$currentUrl?>"><?=$v?></a></li>
                                    <?php endif;?>
                                <?php endforeach;?>
                            </ul>

                        </div>
                    </li>


                    <?php if($auth_user === true):?>

                        <li><a href="<?=$startUrl;?>/task"><?=$header_menu_lang[0][$segment]?> <i class="fa fa-home"></i></a></li>
                        <li><a href="<?=$startUrl;?>/welcome/changeProfile"><?=$welcome_controller[28]?> <i class="fa fa-cog"></i></a></li>
                        <li><a href="<?=$startUrl;?>/welcome/changePassword"><?=$input_form_lang[9][$segment]?> <i class="fa fa-pencil-square-o"></i></a></li>
                        <li><a href="<?=$startUrl;?>/task/addProject"><?=$task_views[0]?> <i class="fa fa-plus"></i></a></li>
                        <li><a href="<?=$startUrl;?>/chart"><?=$header_menu_lang[2][$segment]?> <i class="fa fa-bar-chart"></i></a></li>
                        <li><a href="<?=$startUrl;?>/welcome/logout"><?=$header_menu_lang[1][$segment]?> <i class="fa fa-sign-out"></i></a></li>

                    <?php else:?>
                        <li><a href="<?=base_url()?><?=$segment?>"><?=$header_menu_lang[0][$segment]?></a></li>
                    <?php endif;?>

                </ul>
            </div>



    </div>
</div>

