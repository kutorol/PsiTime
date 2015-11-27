
<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
                <p></p>
            </div>
            <div class="modal-footer">
                <button type="button" id="closeMyModal" class="btn btn-default" data-dismiss="modal"><?=$task_views[58]?></button>
            </div>
        </div>

    </div>
</div>

        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src="<?=base_url()?>js/bootstrap.js"></script>
        <!--видоизмененные alert, confirm, promt и стандартный bootstrap modal, короче тут модалки-->
        <script src="<?=base_url()?>js/bootbox.min.js"></script>
        <!--Преобразуем стандартный select-->
        <script src="<?=base_url()?>js/bootstrap-select.min.js"></script>

        <?php if(isset($useTagIt)):?>
            <!--MULTI TAGS INPUT JQUERY-->
            <script src="<?=base_url()?>js/tag-it.min.js" type="text/javascript" charset="utf-8"></script>
            <!--END TAGS INPUT-->
        <?php endif;?>


        <script src="<?=base_url()?>js/script.js"></script>



        <?php
        /**
         * Подгружаем скрипты драг н дроп загрузки картинок или документов на сайт
         * To load scripts drag n drop upload pictures and documents on the website
         */
        if(isset($attachUploadSripts)):?>
            <script src="<?=base_url()?>js/upload/jquery.ui.widget.js"></script>
            <script src="<?=base_url()?>js/upload/jquery.fileupload.js"></script>
            <script src="<?=base_url()?>js/upload/jquery.iframe-transport.js"></script>
            <script src="<?=base_url()?>js/upload/jquery.fileupload-process.js"></script>
            <script src="<?=base_url()?>js/upload/jquery.fileupload-validate.js"></script>
        <?php endif;?>

        <?php if(isset($useCheckbox)):?>
            <script src="<?=base_url()?>js/bootstrap-checkbox.js"></script>
        <?php endif;?>

    <div class="container-fluid" style="color: #ccc;">
        <p>&nbsp;</p>
        <div class="row text-center">
            vers: <?=VERSION_APP;?>
        </div>
    </div>

    </body>
</html>