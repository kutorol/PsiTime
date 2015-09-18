
<div class="row">
    <div class="container text-center">
        <h2><?=$title?></h2>

        <?php /*Если есть ошибка, то показываем ее*/
        if(isset($error)):?>
            <span class="label label-<?=$status_text?> error"><?=preg_replace('/%s%/', '</span><br><br><span class="label label-success error">',strip_tags($error))?></span>
        <?php endif;?>
    </div>
</div>
<br>