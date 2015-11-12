<?php if(!empty($infoTask)):?>

    <!--This function help add attach file itno server (AJAX with progress bar)-->
    <script src="<?=base_url();?>js/upload/script.js"></script>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-8">




                <h3 id="tastTitleInfo"><?=$infoTask['title']?></h3>
                <p id="taskTextInfo"><?=$infoTask['text']?></p>

                <hr>

                <?php if($idUser == $infoTask['performer_id'] || $idUser == $infoTask['user_id']):?>
                    <div id="editMyTask">
                        <p >
                            <a href="" class="editTaskA" data-switch="open">
                                <span class="edit"><?=$task_views[68];?></span> <?=$task_views[71];?> <i class="fa fa-arrow-down"></i>
                            </a>
                        </p>

                        <!--edit task-->
                        <div style="display: none;" id="showFadeEditTask">
                            <form method="post" action="" onsubmit="editDescTask(); return false;" >
                                <div class="form-group">
                                    <label for="titleTaskInfo"><?=$task_views[38]?>:</label>
                                    <input type="title" class="form-control" id="titleTaskInfo" placeholder="<?=$task_views[38]?>" value="<?=$infoTask['title']?>">
                                </div>


                                <div class="form-group">
                                    <label for="textTask"><?=$task_views[39]?>:</label>
                                    <textarea  class="form-control" placeholder="<?=$task_views[39]?>"  onkeypress="if(event.ctrlKey && event.keyCode==13) {$('#saveEditTask').click(); return false;}" rows="5" id="textTaskInfo"><?=$infoTask['text']?></textarea>
                                </div>

                                <div class="form-group">
                                    <div class="pull-right">
                                        <button type="button" class="btn btn-primary" id="saveEditTask"><?=$task_views[17]?></button>
                                    </div>
                                </div>
                            </form>
                            <div class="clearfix"></div>
                        </div>
                        <!--end edit task-->
                        <hr>
                    </div>
                <?php endif;?>







                <?php if(isset($filesAttach)):?>
                    <fieldset id="hideFieldsetAttach">
                        <legend><?=$task_views[56]?></legend>

                        <div class="row" id="fileAttach">

                            <?php foreach($filesAttach as $k=>$v):?>
                                <div class="col-lg-2" id="delete_<?=$k?>">
                                    <div title="<?=$js[21]?>" onclick="delAttach('<?=$v["src"]?>', 'delete_<?=$k?>', <?=$infoTask['id_task']?>);" class="btn btn-danger deleteAttachFile">
                                        <i class="fa fa-times"></i>
                                    </div>
                                    <div class="thumbnail" align="center">
                                        <div class="options" onClick="showDownloadImageDoc('<?=$v["src"]?>', '<?=$v["ext"]?>', '<?=$v["title"]?>', <?=$infoTask['id_task']?>);" title="<?=$v['title']?>" data-ext="<?=$v["ext"]?>">
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

                        </div>
                    </fieldset>

                <?php else:?>

                    <fieldset style="display: none;" id="hideFieldsetAttach">
                        <legend><?=$task_views[56]?></legend>
                        <div class="row" id="fileAttach"></div>
                    </fieldset>

                <?php endif;?>



                <p>&nbsp;</p>
                <p>&nbsp;</p>

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

                <p>&nbsp;</p>
                <p>&nbsp;</p>

                <!--Dont touch, its for update task-->
                <p style="display: none;" id="attachInfoTask">info</p>
                <p style="display: none;" id="idTaskInfo"><?=$infoTask['id_task']?></p>
                <!--Dont touch-->





            </div>





            <div class="col-lg-4">

                <!--project-->
                <p>
                    <?=$task_views[66]?> "<?=$infoTask['title_project'];?>"
                    <hr>
                </p>
                <!--end project-->

                <!--time-->
                <p>
                    <?=$task_views[45]?>: <label class="label label-success small-text"><?=$infoTask['time_for_complete'];?> <?=$infoTask['time_for_complete_value'];?></label>
                    <hr>
                </p>

                <div>
                    <p>
                        <?=$task_views[69];?> <label class="label label-info small-text"><?=$infoTask['time_add']?></label>
                    </p>
                    <p id="startTimeTask">
                        <?php if($infoTask['time_start'] != ''){ echo $task_views[70]." <label class='label label-info small-text'>".$infoTask['time_start']."</label>";}?>
                    </p>
                    <p id="endTimeTask">
                        <?php if($infoTask['time_end'] != ''){ echo $task_views[72]." <label class='label label-info small-text'>".$infoTask['time_end']."</label>";}?>
                    </p>
                    <hr>
                </div>
                <!--end time-->

                <!--task level-->
                <p>
                    <?=$task_views[34]?>:
                    <p align="center">
                        <select onchange="changeSelectTask('taskLevelInfo');" class="selectpicker col-lg-12" id="taskLevelInfo" data-style="btn-<?php if($infoTask['color'] != ''):?><?=$infoTask['color']?><?php endif;?>">
                            <?php $i = 0; foreach($complexity as $v):?>
                                <option data-color="btn-<?=$v['color']?>" value="<?=$v['id_complexity']?>" <?=($v['id_complexity'] == $infoTask['id_complexity']) ? 'selected' : '';?> > <?=$v['name_complexity_'.$segment]?></option>
                            <?php endforeach;?>
                        </select>
                    </p>
                    <hr>
                </p>
                <!--end task level-->

                <!--status task-->
                <p>
                    <?=$task_views[64]?>:<br>
                    <div align="center" id="tutStatus">
                        <?php if($idUser == $infoTask['performer_id'] || $idUser == $infoTask['user_id']):?>
                        <select onchange="changeSelectTask('statusLevelInfo');" class="selectpicker col-lg-12" data-style="<?php if($infoTask['status'] != 2){echo "btn-info";}?>" id="statusLevelInfo">
                            <?php if($infoTask['status'] == 0){$i = 0;}else{$i = 1;}?>
                            <?php for($i; $i < 4; $i++):?>
                                <option data-color="<?php if($i != 2){echo "btn-info";}?>" value="<?=$i;?>" <?=($infoTask['status'] == $i) ? 'selected' : '';?> ><?=$task_views['status_task_'.$i];?></option>
                            <?php endfor;?>
                        </select>
                        <?php else:?>
                            <?=$task_views['status_task_'.$i];?>
                        <?php endif;?>
                    </div>
                <hr>
                </p>
                <!--end status task-->

                <!--priority-->
                <p>
                    <?=$task_views[62]?>:<br>
                    <p align="center">
                        <select class="selectpicker col-lg-12" id="priorityLevelInfo" onchange="changeSelectTask('priorityLevelInfo');" data-style="btn-<?php if($infoTask['color_priority'] != ''):?><?=$infoTask['color_priority']?><?php endif;?>">
                            <?php foreach($priority as $v):?>
                                <option  data-color="btn-<?=$v['color']?>" value="<?=$v['id_priority']?>" <?=($v['id_priority'] == $infoTask['id_priority']) ? 'selected' : '';?> data-icon="<?=$v['icon']?>" > <?=$v['title_'.$segment]?></option>
                            <?php endforeach;?>
                        </select>
                    </p>
                    <hr>
                </p>
                <!--end priority-->

                <!--user performer-->
                <div id="change_performer">
                    <?=$changeUserView;?>
                </div>
                <!--end user performer-->

                <!--image user-->
                <div id="newViewImgUser">
                    <?=$userImageView;?>
                </div>
                <!--end image user-->


                <?php if($idUser == $infoTask['performer_id'] || $idUser == $infoTask['user_id']):?>
                    <!--delete-->
                    <div align="center" id="deleteTask">
                        <p>&nbsp;</p>
                        <p>
                            <div class="btn btn-danger" id="deleteTask_<?=$infoTask['id_task']?>" onclick="deleteData('task/deleteTask', 'deleteTask_', <?=$infoTask['id_task']?>, {check: true, url: '/task'});"><?=$task_views[74]?> <i class="fa fa-trash-o"></i></div>
                        </p>
                    </div>
                    <!--end delete-->
                <?php endif; ?>

                <p>&nbsp;</p>
                <p>&nbsp;</p>

            </div>
        </div>
    </div>







<?php endif;?>