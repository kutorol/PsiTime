<hr>

<?php
/**
 * Если прикреплен к проекту
 * If attached to the project
 */
if($statusUser == 1):?>

    <!--BUTTON ADD TASK-->
    <div class="row" align="center">
        <div class="btn btn-warning" id="addTaskBtnForm"><?=$task_views[32]?> <i class="fa fa-plus"></i></div>
    </div>
    <!--END BUTTON ADD TASK-->

    <div class="clearfix"></div>

    <!--Тут форма добавления задачи-->
    <div class="row" id="addTaskForm" style="display: none;">
        <hr>
        <div class="container">
            <div>

                <div class="row">

                    <!--Задаем параметры добавляемой задачи-->
                    <div class="col-lg-4">
                        <form method="post" action="" id="formAddTask">
                        <fieldset>
                            <legend><?=$task_views[33]?></legend>


                            <div class="col-lg-12" style="margin-bottom: 15px;">
                                <div class="label label-info" style="border-radius: 5px 5px 0px 0px;"><?=$task_views[59];?></div>
                                <div>
                                    <!--ALL PROJECTS-->
                                    <?php if(!empty($myProjects)):?>
                                        <select id="projectSelect" class="form-control"  style="margin-top: 4px;">
                                            <?php foreach($myProjects as $key=>$project):?>
                                                <option value="<?=$project['id_project']?>"><?=$project['title']?></option>
                                            <?php endforeach;?>
                                        </select>
                                    <?php endif;?>
                                    <!--END ALL PROJECTS-->
                                </div>
                            </div>

                            <div class="col-lg-12" style="margin-bottom: 15px;">
                                <div class="label label-info" style="border-radius: 5px 5px 0px 0px;"><?=$task_views[61];?></div>
                                <div>
                                    <!--Выбор исполнителя-->
                                    <?php if(!empty($myProjects)):?>
                                        <select id="perfomerUser" class="form-control"  style="margin-top: 4px;">
                                            <?php foreach($myProjects[0]['userInProject'] as $key=>$user):?>
                                                <option value="<?=$user['id_user']?>"><?=$user['name']?> (<?=$user['login']?>)</option>
                                            <?php endforeach;?>
                                        </select>
                                    <?php endif;?>
                                    <!--END Выбор исполнителя-->
                                </div>
                            </div>

                            <div class="col-lg-12" style="margin-bottom: 15px;">
                                <div class="label label-info" style="border-radius: 5px 5px 0px 0px;"><?=$task_views[34]?></div>
                                <div>
                                    <select id="taskLevel" class="btn-success form-control"  style="margin-top: 4px;">
                                        <?php foreach($complexity as $v):?>
                                            <option class="btn-<?=$v['color']?>" value="<?=$v['id_complexity']?>"><?=$v['name_complexity_'.$segment]?></option>
                                        <?php endforeach;?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-12" style="margin-bottom: 15px;">
                                <div class="label label-info" style="border-radius: 5px 5px 0px 0px;"><?=$task_views[62]?></div>
                                <div style="margin-top: -18px;">
                                    <select id="priorityLevel" class="selectpicker" >
                                        <?php foreach($priority as $v):?>
                                            <option value="<?=$v['id_priority']?>" data-icon="<?=$v['icon']?>" > <?=$v['title_'.$segment]?></option>
                                        <?php endforeach;?>
                                    </select>
                                </div>
                            </div>

                            <div class="clearfix"></div>

                            <div class="col-lg-12">
                                <div class="label label-info" style="border-radius: 5px 5px 0px 0px;"><?=$task_views[38]?></div>
                                <div class="input-group col-xs-12">
                                    <input type="text" id="titleTask" class="form-control col-xs-12" placeholder="<?=$task_views[38]?>" required>
                                </div>
                            </div>

                            <div class="clearfix"></div>

                            <div class="col-lg-12">
                                <div class="label label-info" style="border-radius: 5px 5px 0px 0px;"><?=$task_views[39]?></div>
                                <div class="input-group col-xs-12">
                                   <textarea name="textTask" rows="3"  class="form-control" placeholder="<?=$task_views[39]?>"></textarea>
                                </div>
                            </div>
                        </fieldset>


                    <?php if(!is_numeric($hoursInDayToWork)):?>
                        <fieldset id="onceTime" >
                    <?php else:?>
                        <fieldset id="onceTime" class="hidden">
                    <?php endif;?>

                            <legend><?=$task_views[40]?></legend>
                            <div style="margin-top: -20px; margin-bottom: 20px; color: #ccc;"><?=$task_views[41]?></div>

                            <div class="col-lg-12">
                                <div class="label label-info" style="border-radius: 5px 5px 0px 0px;"><?=$task_views[40]?></div>
                                <div class="input-group col-xs-12">
                                    <input type="number" required id="hoursInDayToWork" class="form-control col-xs-12" placeholder="<?=$task_views[40]?>" value="8">
                                </div>
                            </div>

                        </fieldset>



                        <fieldset>
                            <legend><?=$task_views[44]?></legend>

                            <div class="col-lg-12">
                                <div class="label label-info" style="border-radius: 5px 5px 0px 0px;"><?=$task_views[45]?></div>
                                <div class="input-group col-xs-12">
                                    <input type="number" id="estimatedTimeForTask" required class="form-control col-xs-12" placeholder="<?=$task_views[45]?>" >
                                </div>
                            </div>

                            <div class="col-lg-12" style="margin-bottom: 15px;">
                                <div class="label label-info" style="border-radius: 5px 5px 0px 0px;"><?=$task_views[46]?></div>
                                <div>
                                    <select class="form-control"  id="measurementTime" style="margin-top: 4px;">
                                        <option value="0"><?=$task_views[47]?></option>
                                        <option value="1"><?=$task_views[48]?></option>
                                        <option value="2"><?=$task_views[49]?></option>
                                        <option value="3"><?=$task_views[50]?></option>
                                        <option value="4"><?=$task_views[51]?></option>
                                    </select>
                                </div>
                            </div>

                        </fieldset>

                        <div class="col-lg-12">
                            <div class="input-group col-lg-12">
                                <?php if(!empty($myProjects)):?>
                                    <button type="submit" id="addTaskBtn" class="btn btn-primary col-lg-12 pull-right"><?=$task_views[32]?></button>
                                <?php endif;?>
                            </div>
                        </div>
                    </form>
                    </div>
                    <!--КОНЕЦ Задаем параметры добавляемой задачи-->

                    <!--Прикрепляем файлы к создаваемой задаче-->
                    <div class="col-lg-8">
                        <fieldset>
                            <legend><?=$task_views[52]?></legend>

                            <!--This function help add attach file itno server (AJAX with progress bar)-->
                            <script src="<?=base_url();?>js/upload/script.js"></script>
                            <!--Не менять этот URL, туда будут отсылаться файлы-->
                            <form id="fileupload" action="<?=$startUrl;?>/task/addTaskAttachFile" method="POST" enctype="multipart/form-data" />

                                <p><span style="color: red;">*</span> <?=$task_views[53]?></p>

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

                        <fieldset>
                            <legend><?=$task_views[56]?></legend>

                            <?php if(isset($filesAttach)):?>
                                <p><?=$task_views[57]?></p>
                            <?php endif;?>

                            <div class="row" id="fileAttach">

                                <?php if(isset($filesAttach)):?>
                                    <?php foreach($filesAttach as $k=>$v):?>
                                        <div class="col-lg-2" id="delete_<?=$k?>">
                                            <div title="<?=$js[21]?>" onclick="delAttach('<?=$v["src"]?>', 'delete_<?=$k?>');" class="btn btn-danger deleteAttachFile">
                                                <i class="fa fa-times"></i>
                                            </div>
                                            <div class="thumbnail" align="center">
                                                <div class="options" onClick="showDownloadImageDoc('<?=$v["src"]?>', '<?=$v["ext"]?>', '<?=$v["title"]?>');" title="<?=$v['title']?>" data-ext="<?=$v["ext"]?>">
                                                    <?php
                                                        switch($v["ext"])
                                                        {
                                                            case 'pdf':     echo '<i class="fa fa-file-pdf-o iconPdf"></i>';            break;
                                                            case "word":    echo '<i class="fa fa-file-word-o iconWord"></i>';          break;
                                                            case "exel":    echo '<i class="fa fa-file-excel-o iconExel"></i>';         break;
                                                            case "pPoint":  echo '<i class="fa fa-file-powerpoint-o iconPPoint"></i>';  break;
                                                            case "text":    echo '<i class="fa fa-file-text-o iconAttach"></i>';        break;
                                                            case "video":   echo '<i class="fa fa-file-video-o iconAttach"></i>';       break;
                                                            case "audio":   echo '<i class="fa fa-file-audio-o iconAttach"></i>';       break;
                                                            case "img":     echo '<img src="'.$v["src"].'" alt="'.$v["title"].'">';     break;
                                                            default:        echo '<i class="fa fa-file-archive-o iconAttach"></i>';
                                                        }
                                                    ?>
                                                </div>
                                                <div class="longText" title="<?=$v['title']?>" ><?=$v["title"]?></div>
                                            </div>
                                        </div>
                                    <?php endforeach;?>
                                <?php endif;?>

                            </div>
                        </fieldset>
                    </div>
                    <!--КОНЕЦ Прикрепляем файлы к создаваемой задаче-->
                </div>
            </div>
        </div>
    </div>
    <hr>
    <!--КОНЕЦ Тут форма добавления задачи-->

    <div class="container-fluid" id="allTasks">
        <div class="row">

            <!--ALL PROJECT, FILTER-->
            <div class="col-lg-3" >

                <!--ALL PROJECTS-->
                <?php if(!empty($myProjects)):?>
                    <fieldset><legend><?=$task_views[7]?>:</legend></fieldset>

                    <div class="col-lg-offset-1">
                        <div class="list-group" id="menu-projects">
                            <a href="#" id="allProjectsTasks" data-id-project="all" class="<?php if(!isset($dontUseSelectProject)){ echo "active"; }?> list-group-item navigate-project" onclick="getAllTask();"><?=$task_views[63]?>  <span class="badge" id="countProject_all"><?=$countProject_all?></span></a>
                        <?php foreach($myProjects as $key=>$project):?>
                            <a href="#" class="list-group-item navigate-project" data-id-project="<?=$project['id_project']?>" onclick="getAllTask(<?=$project['id_project']?>);"><?=$project['title']?>  <span class="badge" id="countProject_<?=$project['id_project']?>"><?=$countTask['countProject_'.$project['id_project']]?></span></a>
                        <?php endforeach;?>
                        </div>
                    </div>
                <?php endif;?>
                <!--END ALL PROJECTS-->

                <p>&nbsp;</p>
                <p>&nbsp;</p>

                <!--FILTER START-->
                <fieldset><legend><?=$task_views[78]?></legend></fieldset>
                <div class="col-lg-offset-1">
                    <div id="checkboxForFilterStatus">
                        <b class="clickHideShow"><span class="nameFilter"><?=$task_views[79]?></span> <i class="fa fa-arrow-up"></i></b>
                        <div class="openDiv">
                            <p>
                                <ul class="ul-top-margin" >
                                    <?php for($counterStatus = 0; $counterStatus < 4; $counterStatus++):?>
                                        <li class="max-height-li"><?=$task_views['status_task_'.$counterStatus]?> <input type="checkbox" class="checkbox" value="<?=$counterStatus?>"/></li>
                                    <?php endfor;?>
                                </ul>
                                <br>
                            </p>
                        </div>

                        <hr>
                    </div>

                    <div id="checkboxForFilterPriority">
                        <b class="clickHideShow"><span class="nameFilter"><?=$task_views[80]?></span> <i class="fa fa-arrow-down"></i></b>
                        <div style="display: none;">
                            <p >
                                <ul class="ul-top-margin">
                                    <?php foreach($priority as $v):?>
                                        <li class="max-height-li"><?=$v['title_'.$segment]?> <i class="<?=$v['icon']?>"></i> <input type="checkbox" class="checkbox" value="<?=$v['id_priority']?>"/></li>
                                    <?php endforeach;?>
                                </ul>
                                <br>
                            </p>
                        </div>

                        <hr>
                    </div>

                    <div id="checkboxForFilterComplexity">
                        <b class="clickHideShow"><span class="nameFilter"><?=$task_views[81]?></span> <i class="fa fa-arrow-down"></i></b>
                        <div style="display: none;">
                            <p >
                                <ul class="ul-top-margin">
                                    <?php foreach($complexity as $v):?>
                                        <li class="max-height-li"><?=$v['name_complexity_'.$segment]?> <input type="checkbox" class="checkbox" value="<?=$v['id_complexity']?>"/></li>
                                    <?php endforeach;?>
                                </ul>
                                <br>
                            </p>
                        </div>

                        <hr>
                    </div>

                    <div id="checkboxForFilterPerformer">
                        <b class="clickHideShow"><span class="nameFilter"><?=$task_views[83]?></span> <i class="fa fa-arrow-down"></i></b>
                        <div style="display: none;">
                            <p>
                                <ul class="ul-top-margin">
                                    <li class="max-height-li"><span class="label label-info small-text"><?=$task_views[84]?></span> <input type="checkbox" class="checkbox" value="<?=$idUser?>"/></li>
                                    <?php foreach($allUsersForFilters as $user):?>
                                        <li class="max-height-li"><?=$user['name']?> (<?=$user['login']?>) <input type="checkbox" class="checkbox" value="<?=$user['id_user']?>"/></li>
                                    <?php endforeach;?>
                                </ul>
                                <br>
                            </p>
                        </div>

                        <hr>
                    </div>

                    <div class="btn btn-primary" onclick="getAllTaskWithFilter();"><?=$task_views[82]?></div>


                    <div id="resetFilters" >
                        <div class="clearfix"></div>
                        <hr>
                        <div class="pull-left">
                            <div class="btn btn-danger disabled">Сохранить фильтр</div>
                        </div>

                        <div class="pull-right">
                            <div class="btn btn-danger" onclick="resetFilters();"><?=$task_views[85]?></div>
                        </div>


                    </div>

                </div>
                <!--FILTER END-->
            </div>
            <!--END ALL PROJECT, FILTER-->



            <!--ALL TASK-->
            <div class="col-lg-9" >

                <div class="row table-task" id="allTaskHere">
                    <?php if(!empty($myProjects)):?>

                        <!--Тут находиться все задачи, если они есть-->
                        <?php if(isset($renderViewTask)){ echo $renderViewTask; }?>
                        <!--КОНЕЦ Тут находиться все задачи, если они есть-->

                    <?php endif;?>
                </div>

            </div>
            <!--END ALL TASK-->
        </div>
    </div>

    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>



<?php
/**
 * Если не прикреплен к проекту, показываем сообщение без всяких форм
 * If not attached to the draft, show a message without any form
 */
 else:?>
    <div class="row">
        <div class="container">
            <div align="center">
                <h4><i class="fa fa-exclamation-triangle" style="color: red;"></i> <?=$task_views[15]?></h4>
                <hr>
                <p>
                    <?php if($segment == 'ru'):?>
                        <img src="<?=base_url()?>img/notProject.jpg">
                    <?php else:?>
                        <img src="<?=base_url()?>img/notProject_.jpg">
                    <?php endif;?>
                </p>

            </div>
        </div>
    </div>
<?php endif;?>