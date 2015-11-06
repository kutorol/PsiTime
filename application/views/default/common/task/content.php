<?php if(!empty($allTasks)):?>
    <div style="margin-right: 15px;">
        <div class="div">
            <span class="label label-default">Выполнено</span>
            <span class="label label-default">3 задачи</span>
            <span class="label label-default">резерв времени</span>
        </div>

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
                </tr>
                </thead>
                <tbody>

                    <?php foreach($allTasks as $k=>$task):?>
                        <tr class="row_task_<?=$task['id_task'];?>">
                            <td colspan="6" style="border-bottom: none;">
                                <a style="<?php if($task['status'] == 2){echo "text-decoration:line-through; color: #818289; font-weight: bold;";}?>" href="<?=$startUrl;?>/task/view/<?=$task['id_task'];?>"><?=$task['id_task'];?> - <?=$task['title'];?></a><br>
                                <?=$task_views[66]?> <?=$task['title_project'];?>
                                <hr style="margin-bottom: -44px; border: none;">
                            </td>
                        </tr>
                        <tr class="row_task_<?=$task['id_task'];?>">

                            <td><span style="font-size: 13px;" class="label label-<?=$task['color'];?>"><?=$task['name_complexity_'.$segment];?></span></td>
                            <td><?=$task['time_for_complete'];?> <?=$task['time_for_complete_value'];?></td>
                            <td>
                                <select id="statusLevelInList_<?=$task['id_task'];?>" class="selectpicker statusLevelInList">
                                    <?php for($i = 0; $i < 4; $i++):?>
                                        <option value="<?=$i;?>" <?=($task['status'] == $i) ? 'selected' : '';?> ><?=$task_views['status_task_'.$i];?></option>
                                    <?php endfor;?>
                                </select>
                                <script> $(function() { $('#statusLevelInList_<?=$task['id_task'];?>').selectpicker(); });</script>
                            </td>

                            <td align="center" <?=($task['user_id'] == $task['performer_id']) ? "colspan='2'" : "";?> ><img src="<?=base_url()?>img/<?=$task['img_performer'];?>"  style="height: 50px; width: 50px; display: block;"> <?=$task['name_performer'];?></td>
                            <?php if($task['user_id'] != $task['performer_id']):?>
                                <td align="center"><img src="<?=base_url()?>img/<?=$task['user_img'];?>"  style="height: 50px; width: 50px; display: block;"> <?=$task['name'];?></td>
                            <?php endif;?>
                            <td align="center"><span style="display: block;" class="<?php if($task['color_priority'] != ''):?>label label-<?=$task['color_priority']?><?php endif;?>"><i class="<?=$task['icon_priority']?> "></i> <?=$task['title_priority']?></span></td>
                        </tr>
                    <?php endforeach;?>

                </tbody>
            </table>
        </div>

    </div>

    <?php if($pagination['status'] != 'error'):?>
        <?=$pagination['pagination']?>
    <?php endif;?>

<?php else:?>
    <div class="alert alert-danger col-lg-9">
        <?=$task_views[67]?>
    </div>
<?php endif;?>