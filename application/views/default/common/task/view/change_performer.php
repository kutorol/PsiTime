<p>
    <?=$task_views[73]?>:<br>
    <p align="center">
        <select class="selectpicker col-lg-12" id="performerUser" onchange="changeSelect('performerUser');" data-style="btn-perfomer-user">
            <?php foreach($allUserInProject as $v):?>
                <option  data-color="btn-perfomer-user" value="<?=$v['id_user']?>" <?=($infoTask['performer_id'] == $v['id_user']) ? 'selected' : '';?> ><?=$v['name']?> (<?=$v['login']?>)</option>
            <?php endforeach;?>
        </select>
    </p>
    <hr>
</p>

<script>$(function(){$("#performerUser").selectpicker('refresh');});</script>