
<div class="row">
    <div class="container text-center">
        <h2><?=$title?></h2>

        <?php /*Если есть ошибка, то показываем ее*/
        if(isset($error)):?>
            <span id="mainError" class="label label-<?=$status_text?> error"><?=preg_replace('/%s%/', '</span><br><br><span class="label label-success error">', preg_replace('<br>', '/span><br><br><span class="label label-success error"',strip_tags($error, "<br>")))?></span>
        <?php endif;?>
    </div>
</div>
<br>