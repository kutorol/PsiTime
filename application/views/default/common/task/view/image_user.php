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
