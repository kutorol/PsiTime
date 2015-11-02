<div class="row">
    <div class="container text-center">
        <h2 class="<?php if(isset($infoTask['status'])){ if($infoTask['status'] == 3) echo "priehaliKinaNeBudet";}?>"><?=$title?></h2>

        <?php /*Если есть ошибка, то показываем ее*/
        if(isset($error)):?>
            <span id="mainError" class="label label-<?=$status_text?> error"><?=preg_replace('/%s%/', '</span><br class="additionalError"><br class="additionalError"><span class="label label-success error additionalError">', preg_replace('<br>', '/span><br class="additionalError"><br class="additionalError"><span class="label label-success error additionalError"',strip_tags($error, "<br>")))?></span>
        <?php endif;?>
    </div>
</div>
<br>