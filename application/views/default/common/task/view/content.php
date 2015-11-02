<?php if(!empty($infoTask)):?>

    <!--This function help add attach file itno server (AJAX with progress bar)-->
    <script src="<?=base_url();?>js/upload/script.js"></script>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-8">
               <p>
                   <?=$task_views[69];?> <label class="label label-info small-text"><?=$infoTask['time_add']?></label>
                   <?php if($infoTask['time_start'] != ''){ echo " &nbsp; ".$task_views[70]." <label class='label label-info small-text'>".$infoTask['time_start']."</label>";}?>
               </p>



                <h3><?=$infoTask['title']?></h3>
                <p><?=$infoTask['text']?></p>

                <hr>
                <p >
                    <a href="" class="editTaskA" data-switch="open">
                        <span class="edit"><?=$task_views[68];?></span> <?=$task_views[71];?> <i class="fa fa-arrow-down"></i>
                    </a>
                </p>

                <!--edit task-->
                <div style="display: none;" id="showFadeEditTask">

                </div>
                <!--end edit task-->
                <hr>







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

                <!--Dont touch-->
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
                <!--end time-->

                <!--task level-->
                <p>
                    <?=$task_views[34]?>:
                    <p align="center">
                        <select class="selectpicker col-lg-12" data-style="btn-<?php if($infoTask['color'] != ''):?><?=$infoTask['color']?><?php endif;?>">
                            <?php foreach($complexity as $v):?>
                                <option value="<?=$v['id_complexity']?>" <?=($v['id_complexity'] == $infoTask['id_complexity']) ? 'selected' : '';?> > <?=$v['name_complexity_'.$segment]?></option>
                            <?php endforeach;?>
                        </select>
                    </p>
                    <hr>
                </p>
                <!--end task level-->

                <!--status task-->
                <p>
                    <?=$task_views[64]?>:<br>
                    <select class="selectpicker statusLevelInList col-lg-12" data-style="btn-info">
                        <?php for($i = 0; $i < 4; $i++):?>
                            <option value="<?=$i;?>" <?=($infoTask['status'] == $i) ? 'selected' : '';?> ><?=$task_views['status_task_'.$i];?></option>
                        <?php endfor;?>
                    </select>
                <hr>
                </p>
                <!--end status task-->

                <!--priority-->
                <p>
                    <?=$task_views[62]?>:<br>
                    <p align="center">
                        <select class="selectpicker col-lg-12" data-style="btn-<?php if($infoTask['color_priority'] != ''):?><?=$infoTask['color_priority']?><?php endif;?>">
                            <?php foreach($priority as $v):?>
                                <option value="<?=$v['id_priority']?>" <?=($v['id_priority'] == $infoTask['id_priority']) ? 'selected' : '';?> data-icon="<?=$v['icon']?>" > <?=$v['title_'.$segment]?></option>
                            <?php endforeach;?>
                        </select>
                    </p>
                    <hr>
                </p>
                <!--end priority-->

                <!--image user-->
                <?php if($infoTask['user_id'] != $infoTask['performer_id']):?>

                    <?=$task_views[61]?>:<br>
                    <p align="center">
                        <img src="<?=base_url()?>img/<?=$infoTask['img_performer'];?>" style="height: 100px; width: 100px; display: block;">
                        <?=$infoTask['name_performer'];?>
                    </p>

                    <hr>

                    <br><br>
                    <?=$task_views[65]?>:<br>
                    <p align="center">
                        <img src="<?=base_url()?>img/<?=$infoTask['user_img'];?>"  style="height: 100px; width: 100px; display: block;">
                        <?=$infoTask['name'];?>
                    </p>
                <?php else:?>

                    <?=$task_views[61]?>, <?=$task_views[65]?>:<br><br>
                    <p align="center">
                        <img src="<?=base_url()?>img/<?=$infoTask['img_performer'];?>"  style="height: 100px; width: 100px; display: block;">
                        <?=$infoTask['name_performer'];?>
                    </p>
                    <hr>
                <?php endif;?>
                <!--end image user-->

                <p>&nbsp;</p>
                <p>&nbsp;</p>

            </div>
        </div>
    </div>







<?php endif;?>