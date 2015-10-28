<div class="row">
    <div class="container text-center">
        <form action="<?=$startUrl;?>/welcome/changeProfile" method="post">
            <div class="col-xs-12 col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4">

                <div class="input-group col-xs-12">
                    <input type="text" class="form-control col-xs-12" name="name" value="<?=set_value("name")?>" placeholder="<?=$input_form_lang[1][$segment]?>: <?=$userData['name']?>" >
                    <br><br>
                    <span class="label label-danger error"><?=strip_tags(form_error('name'))?></span>
                </div>

                <div class="input-group col-xs-12">
                    <input type="text" class="form-control col-xs-12" name="login" value="<?=set_value("login")?>" placeholder="<?=$input_form_lang[0][$segment]?>: <?=$userData['login']?>" >
                    <br><br>
                    <span class="label label-danger error"><?=strip_tags(form_error('login'))?></span>
                </div>

                <div class="input-group col-xs-12">
                    <input type="email" class="form-control col-xs-12" name="email" value="<?=set_value("email")?>" placeholder="Email: <?=$userData['email']?>" >
                    <br><br>
                    <span class="label label-danger error"><?=strip_tags(form_error('email'))?></span>
                </div>

                <div class="input-group col-xs-12">
                    <label for="hours"><span style="color:red;">*</span> <?=$welcome_controller[32]?></label>
                    <input type="number" class="form-control col-xs-12" name="hours" value="<?=(is_numeric($userData['hoursInDayToWork'])) ? $userData['hoursInDayToWork'] : set_value("hours");?>" placeholder="<?=$userData['hoursInDayToWork']?>" required>
                    <br><br>
                    <span class="label label-danger error"><?=strip_tags(form_error('hours'))?></span>
                </div>


                <div class="input-group col-xs-12">
                    <button type="submit" class="btn btn-primary col-xs-12" name="change_profile"><?=$input_form_lang[13][$segment]?></button>
                </div>

            </div>

        </form>
    </div>
</div>

<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>



<div class="container">
    <div class="row">
        <div class="col-lg-4">
            <fieldset>
                <legend><?=$welcome_controller[34]?></legend>
                <div align="center">
                    <img src="<?=base_url()?>img/<?=$userData['img']?>" id="avatarImg" class="col-lg-12" alt="avatar">
                </div>
            </fieldset>
        </div>

        <div class="col-lg-8">
            <fieldset>
                <legend><?=$welcome_controller[35]?></legend>

                <!--This function help add attach file itno server (AJAX with progress bar)-->
                <script src="<?=base_url();?>js/upload/script.js"></script>
                <!--Не менять этот URL, туда будут отсылаться файлы-->
                <form id="fileupload" action="<?=$startUrl;?>/task/addTaskAttachFile" method="POST" enctype="multipart/form-data" />

                <p><span style="color: red;">*</span> <?=$welcome_controller[33]?></p>

                <!--Поле для переноса файла для загрузки (drag n drop)-->
                <div class="row-fluid" id="upl_button_div">
                    <div class="span12">
                        <div id="dropZone" class="dropzone" align="center">
                            <?=$task_views[54]?> <i class="fa fa-download"></i>
                            <input name="userfile" class="input_opacity" type="file">
                        </div>
                    </div>
                </div>
                <!--КОНЕЦ Поле для переноса файла для загрузки (drag n drop)-->

                <br>
                <!--Прогресс бар загрузки-->
                <div class="progress">
                    <div id="bar" class="progress-bar progress-bar-success progress-bar-striped active bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
                </div>
                <!--КОНЕЦ Прогресс бар загрузки-->

                <!--Кнопка загрузки файла-->
                <div class="form-actions fileupload-buttonbar no-margin">
                    <div class="btn btn-small btn-default" id="fake_upload_button">
                        <i class="icon-plus"></i>
                        <span><?=$task_views[55]?></span>
                    </div>
                </div>
                <!--КОНЕЦ Кнопка загрузки файла-->

                <br><br>
                </form>
            </fieldset>
        </div>
    </div>
</div>

<p style="display: none;" id="AvatarOrNo">avatar</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
