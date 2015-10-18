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
                        <form action="" method="post">

                            <fieldset>
                                <legend><?=$task_views[33]?></legend>
                                <div class="col-lg-12" style="margin-bottom: 15px;">
                                    <div class="label label-info" style="border-radius: 5px 5px 0px 0px;"><?=$task_views[34]?></div>
                                    <div>
                                        <select id="taskLevel" class="btn-success form-control"  style="margin-top: 4px;">
                                            <option class="btn-success"><?=$task_views[35]?></option>
                                            <option class=" btn-warning"><?=$task_views[36]?></option>
                                            <option class=" btn-danger"><?=$task_views[37]?></option>
                                        </select>
                                    </div>
                                </div>

                                <div class="clearfix"></div>

                                <div class="col-lg-12">
                                    <div class="label label-info" style="border-radius: 5px 5px 0px 0px;"><?=$task_views[38]?></div>
                                    <div class="input-group col-xs-12">
                                        <input type="text" class="form-control col-xs-12" placeholder="<?=$task_views[38]?>">
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


                            <?php if(!is_numeric($time_start_day) || !is_numeric($time_end_day)):?>
                                <fieldset>
                                    <legend><?=$task_views[40]?></legend>
                                    <div style="margin-top: -20px; margin-bottom: 20px; color: #ccc;"><?=$task_views[41]?></div>

                                    <div class="col-lg-12">
                                        <div class="label label-info" style="border-radius: 5px 5px 0px 0px;"><?=$task_views[42]?></div>
                                        <div class="input-group col-xs-12">
                                            <input type="number" class="form-control col-xs-12" placeholder="<?=$task_views[42]?>" value="9">
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="label label-info" style="border-radius: 5px 5px 0px 0px;"><?=$task_views[43]?></div>
                                        <div class="input-group col-xs-12">
                                            <input type="number" class="form-control col-xs-12" placeholder="<?=$task_views[43]?>" value="18">
                                        </div>
                                    </div>
                                </fieldset>
                            <?php endif;?>


                            <fieldset>
                                <legend><?=$task_views[44]?></legend>

                                <div class="col-lg-12">
                                    <div class="label label-info" style="border-radius: 5px 5px 0px 0px;"><?=$task_views[45]?></div>
                                    <div class="input-group col-xs-12">
                                        <input type="number" class="form-control col-xs-12" placeholder="<?=$task_views[45]?>" value="18">
                                    </div>
                                </div>

                                <div class="col-lg-12" style="margin-bottom: 15px;">
                                    <div class="label label-info" style="border-radius: 5px 5px 0px 0px;"><?=$task_views[46]?></div>
                                    <div>
                                        <select class="form-control"  style="margin-top: 4px;">
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
                                    <button type="submit" class="btn btn-primary col-lg-12 pull-right"><?=$task_views[32]?></button>
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
                    <div class="list-group" id="menu-projects">
                    <?php $i = 0; foreach($myProjects as $key=>$project):?>
                        <a href="#" class="<?=($i == 0) ? 'active' : '';?> list-group-item navigate-project" data-id="<?=$project['id_project']?>"><?=$project['title']?></a>
                    <?php $i++; endforeach;?>
                    </div>
                <?php endif;?>
                <!--END ALL PROJECTS-->


            </div>
            <!--END ALL PROJECT, FILTER-->

            <!--ALL TASK-->
            <div class="col-lg-9" >

                <div class="row table-task">
                    <?php if(!empty($myProjects)):?>

                        <div style="margin-right: 15px;">
                            <div class="div">
                                <span class="label label-default">Выполнено</span>
                                <span class="label label-default">3 задачи</span>
                                <span class="label label-default">резерв времени</span>
                            </div>

                            <div class="table-task">
                                <table>
                                    <thead>
                                    <tr>
                                        <td>Задача</td>
                                        <td>Сложность</td>
                                        <td>Приступили</td>
                                        <td>Завершили</td>
                                        <td>Выполнили</td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>правка</td>
                                        <td>легко</td>
                                        <td>10:25 15.09.2015</td>
                                        <td>17:25 16.10.2015</td>
                                        <td>5 минут</td>
                                    </tr>
                                    <tr>
                                        <td>правка 2</td>
                                        <td>трудно</td>
                                        <td>10:25 15.09.2015</td>
                                        <td>17:25 16.10.2015</td>
                                        <td>6 часов</td>
                                    </tr>
                                    <tr>
                                        <td>правка 3</td>
                                        <td>средне</td>
                                        <td>10:25 15.09.2015</td>
                                        <td>17:25 16.10.2015</td>
                                        <td>12 часов 9 минут</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>

                        </div>

                    <?php else:?>
                        <div class="alert alert-danger col-lg-9">
                            Вы не создали ни одной задачи! <a href="#" class="alert-link">Добавить задачу!</a>
                        </div>

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