<hr>

<?php
/**
 * Если прикреплен к проекту
 * If attached to the project
 */
if($statusUser == 1):?>

    <!--ADD TASK-->
    <div class="row" align="center">
        <div class="btn btn-warning" id="addTaskBtnForm">Добавить задачу <i class="fa fa-plus"></i></div>
    </div>

    <div class="clearfix"></div>
    <div class="row" id="addTaskForm" style="display: none;">
        <hr>
        <div class="container">
            <div>

                <div class="row">

                    <div class="col-lg-4">
                        <form action="" method="post">

                            <fieldset>
                                <legend>Задача</legend>
                                <div class="col-lg-12" style="margin-bottom: 15px;">
                                    <div class="label label-info" style="border-radius: 5px 5px 0px 0px;">Сложность</div>
                                    <div>
                                        <select id="taskLevel" class="btn-success form-control"  style="margin-top: 4px;">
                                            <option class="btn-success">Легко</option>
                                            <option class=" btn-warning">Средне</option>
                                            <option class=" btn-danger">Трудно</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="clearfix"></div>

                                <div class="col-lg-12">
                                    <div class="label label-info" style="border-radius: 5px 5px 0px 0px;">Название</div>
                                    <div class="input-group col-xs-12">
                                        <input type="text" class="form-control col-xs-12" placeholder="Название">
                                    </div>
                                </div>

                                <div class="clearfix"></div>

                                <div class="col-lg-12">
                                    <div class="label label-info" style="border-radius: 5px 5px 0px 0px;">Описание</div>
                                    <div class="input-group col-xs-12">
                                       <textarea name="textTask" rows="3"  class="form-control" placeholder="Описание"></textarea>
                                    </div>
                                </div>
                            </fieldset>


                            <?php if(!is_numeric($time_start_day) || !is_numeric($time_end_day)):?>
                                <fieldset>
                                    <legend>Время рабочего дня (одни раз)</legend>
                                    <div style="margin-top: -20px; margin-bottom: 20px; color: #ccc;">Можно изменить в профиле</div>

                                    <div class="col-lg-6">
                                        <div class="label label-info" style="border-radius: 5px 5px 0px 0px;">Начало дня</div>
                                        <div class="input-group col-xs-12">
                                            <input type="number" class="form-control col-xs-12" placeholder="Начало дня" value="9">
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="label label-info" style="border-radius: 5px 5px 0px 0px;">Конец дня</div>
                                        <div class="input-group col-xs-12">
                                            <input type="number" class="form-control col-xs-12" placeholder="Конец дня" value="18">
                                        </div>
                                    </div>
                                </fieldset>
                            <?php endif;?>


                            <fieldset>
                                <legend>Временные рамки задачи</legend>

                                <div class="col-lg-12">
                                    <div class="label label-info" style="border-radius: 5px 5px 0px 0px;">Примерное время выполнения</div>
                                    <div class="input-group col-xs-12">
                                        <input type="number" class="form-control col-xs-12" placeholder="Конец дня" value="18">
                                    </div>
                                </div>

                                <div class="col-lg-12" style="margin-bottom: 15px;">
                                    <div class="label label-info" style="border-radius: 5px 5px 0px 0px;">Время измерения</div>
                                    <div>
                                        <select class="form-control"  style="margin-top: 4px;">
                                            <option>минуты</option>
                                            <option>часы</option>
                                            <option>дни</option>
                                            <option>недели</option>
                                            <option>месяцы</option>
                                        </select>
                                    </div>
                                </div>

                            </fieldset>

                            <div class="col-lg-12">
                                <div class="input-group col-lg-12">
                                    <button type="submit" class="btn btn-primary col-lg-12 pull-right">Добавить</button>
                                </div>
                            </div>
                        </form>
                    </div>
























                    <div class="col-lg-8">

                        <fieldset>
                            <legend>Прикрепите картинку/документ:</legend>

                            <script src="<?=base_url();?>js/upload/script.js"></script>

                            <form id="fileupload" action="<?=$startUrl;?>/task/addTaskAttachFile" method="POST" enctype="multipart/form-data" />


                                <p><span style="color: red;">*</span> Максимальный размер файла 10 Мб</p>

                                <div class="row-fluid" id="upl_button_div">
                                    <div class="span12">
                                        <div id="dropZone" class="dropzone" align="center">
                                            Перетащите сюда <i class="fa fa-download"></i>
                                            <input name="userfile" class="input_opacity" type="file">
                                        </div>
                                    </div>
                                </div>

<br>
                                <div class="progress">
                                    <div id="bar" class="progress-bar progress-bar-success progress-bar-striped active bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
                                </div>

                                <div class="form-actions fileupload-buttonbar no-margin">
                                    <div class="btn btn-small btn-default" id="fake_upload_button">
                                        <i class="icon-plus"></i>
                                        <span>Выбрать FOTO...</span>
                                    </div>
                                </div>

                                    <br><br>
                            </form>
                        </fieldset>


                        <fieldset>
                            <legend>Прикрепленные файлы:</legend>

                            <?php if(isset($filesAttach)):?>
                                <p>В прошлый раз вы не добавили задачу. Это то что вы прикрепляли!</p>
                            <?php endif;?>

                            <div class="row" id="fileAttach">

                                <?php if(isset($filesAttach)):?>
                                    <?php foreach($filesAttach as $k=>$v):?>
                                        <div class="col-lg-2" id="delete_<?=$k?>">
                                            <div title="Удалить" onclick="delAttach('<?=$v["src"]?>', 'delete_<?=$k?>');" class="btn btn-danger deleteAttachFile">
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
                        <fieldset>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr>
    <!--END ADD TASK-->


    <div class="container-fluid" id="allTasks">
        <div class="row">

            <!--ALL PROJECT, FILTER-->
            <div class="col-lg-3" >


                <!--PROJECTS-->
                <?php if(!empty($myProjects)):?>
                    <div class="list-group" id="menu-projects">
                    <?php $i = 0; foreach($myProjects as $key=>$project):?>
                        <a href="#" class="<?=($i == 0) ? 'active' : '';?> list-group-item navigate-project" data-id="<?=$project['id_project']?>"><?=$project['title']?></a>
                    <?php $i++; endforeach;?>
                    </div>
                <?php endif;?>



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





<?php
/**
 * Если не прикреплен к проекту
 * If not attached to the draft
 */
 else:?>
    <div class="row">
        <div class="container">
            <div>
                <?=$task_views[15]?>
            </div>
        </div>
    </div>
<?php endif;?>