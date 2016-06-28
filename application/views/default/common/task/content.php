<?php if(!empty($allTasks)):?>
    <div style="margin-right: 15px; margin-top: -30px;">

        <div class="table-task">
            <table>
                <thead>
                <tr >

                    <th align="center"><?=$task_views[34]?></th>
                    <th align="center"><?=$task_views[45]?></th>
                    <th align="center"><?=$task_views[64]?></th>
                    <th align="center"><?=$task_views[61]?></th>
                    <th align="center"><?=$task_views[65]?></th>
                    <th align="center"><?=$task_views[62]?></th>
                    <th align="center">-</th>
                </tr>
                </thead>
                <tbody>

                    <?php foreach($allTasks as $k=>$task):?>
                        <tr class="row_task_<?=$task['id_task'];?>" id="rowTrTaskAll_<?=$task['id_task'];?>">
                            <td colspan="6" style="border-bottom: none;">
                                <a style="<?php if($task['status'] == 2){echo "text-decoration:line-through; color: #818289; font-weight: bold;";}?>"  href="<?=$startUrl;?>/task/view/<?=$task['id_task'];?>"><?=$task['id_task'];?> - <?=$task['title'];?></a><br>
                                <?=$task_views[66]?> <?=$task['title_project'];?>
                                <hr style="margin-bottom: -40px; border: none;">
                            </td>
                        </tr>
                        <tr class="row_task_<?=$task['id_task'];?>">

                            <td><span style="font-size: 13px;" class="label label-<?=$task['color'];?>"><?=$task['name_complexity_'.$segment];?></span></td>
                            <td><?=$task['time_for_complete'];?> <?=$task['time_for_complete_value'];?></td>
                            <td>
                                <?=$task_views['status_task_'.$task['status']];?>

                                <!--<select id="statusLevelInList_<?/*=$task['id_task'];*/?>" class="selectpicker statusLevelInList">
                                    <?php /*for($i = 0; $i < 4; $i++):*/?>
                                        <option value="<?/*=$i;*/?>" <?/*=($task['status'] == $i) ? 'selected' : '';*/?> ><?/*=$task_views['status_task_'.$i];*/?></option>
                                    <?php /*endfor;*/?>
                                </select>
                                <script> $(function() { $('#statusLevelInList_<?/*=$task['id_task'];*/?>').selectpicker(); });</script>-->
                            </td>

                            <td align="center" <?=($task['user_id'] == $task['performer_id']) ? "colspan='2'" : "";?> ><img src="<?=base_url()?>img/<?=$task['img_performer'];?>"  style="height: 50px; width: 50px; display: block;"> <?=$task['name_performer'];?></td>

                            <?php if($task['user_id'] != $task['performer_id']):?>
                                <td align="center">
                                    <img src="<?=base_url()?>img/<?=$task['user_img'];?>"  style="height: 50px; width: 50px; display: block;"> <?=$task['name'];?>
                                </td>
                            <?php endif;?>

                            <td align="center">
                                <span style="display: block;" class="<?php if($task['color_priority'] != ''):?>label label-<?=$task['color_priority']?><?php endif;?>">
                                    <i class="<?=$task['icon_priority']?> "></i> <?=$task['title_priority']?>
                                </span>
                            </td>

                            <td align="center">
                                <?php if($idUser == $task['performer_id'] || $idUser == $task['user_id']):?>
                                    <div class="btn btn-danger" onclick="deleteData('task/deleteTask', 'rowTrTaskAll_', <?=$task['id_task']?>, undefined, 'notRedirect');">
                                        <i class="fa fa-trash-o"></i>
                                    </div>
                                <?php endif;?>
                            </td>
                        </tr>
                    <?php endforeach;?>

                </tbody>
            </table>
        </div>

    </div>

    <!--PAGINATION-->
    <?php if($pagination['status'] != 'error'):?>
        <br>
        <?php if(isset($countPageView)):?>
            <div class="col-lg-3">
                <?=$task_views[75]?> <span class="label label-success small-text"><?=$countPageView?></span>
            </div>

            <form class="form-inline " onsubmit="getMeMyPage(); return false;">
                <div class="form-group">
                    <label for="getMyPage"><?=$task_views[77]?> </label>
                    <div class="input-group">

                        <input type="number" class="form-control" id="getMyPage" placeholder="<?=$task_views[76]?>">
                        <div class="input-group-addon btn btn-success" onclick="getMeMyPage();"><i class="fa fa-hand-o-up"></i>
                        </div>
                    </div>
                </div>
            </form>

            <div class="clearfix"></div>

        <?php endif;?>

        <?=$pagination['pagination']?>
    <?php endif;?>
    <!--END PAGINATION-->

<?php else:?>
    <div class="alert alert-danger col-lg-9">
        <?=$task_views[67]?>
    </div>
<?php endif;?>