<?php if(!isset($notTask)):?>

    <div class="container">
        <div class="row">
            <p>Сортировать по:</p>
            <span class="clickLabel activeLabel" show="content_complexity">Сложности</span>
            <span class="clickLabel" show="content_priority">Приоритету</span>
        </div>
    </div>


    <div id="allCharts">
        <!--COMPLEXITY-->
            <div  id="content_complexity">

            <div class="container">
                <div class="row" align="center">
                    <h3 class="label label-info"><?=$chart_controller[1]?></h3>
                </div>
            </div>

            <hr>

            <div class="container" style="color: red">
                <div class="row">
                    <div class="col-lg-6">
                        <div id="containerComplexityAll" style="min-width: 310px; height: 300px; max-width: 500px; margin: 0 auto"></div>
                    </div>

                    <div class="col-lg-6">
                        <div id="containerComplexityProject" style="min-width: 310px; height: 400px; max-width: 600px; margin: 0 auto"></div>
                    </div>

                    <div class="clearfix"></div>
                    <hr>

                    <div class="col-lg-6">
                        <div id="containerComplexityAllComplete" style="min-width: 310px; height: 300px; max-width: 500px; margin: 0 auto"></div>
                    </div>

                    <div class="col-lg-6">
                        <div id="containerComplexityProjectComplete" style="min-width: 310px; height: 400px; max-width: 600px; margin: 0 auto"></div>
                    </div>


                    <div class="clearfix"></div>
                    <hr>

                    <div class="col-lg-6">
                        <div id="containerComplexityAllNeedDo" style="min-width: 310px; height: 300px; max-width: 500px; margin: 0 auto"></div>
                    </div>

                    <div class="col-lg-6">
                        <div id="containerComplexityProjectNeedDo" style="min-width: 310px; height: 400px; max-width: 600px; margin: 0 auto"></div>
                    </div>
                </div>
            </div>

        </div>
        <!--END COMPLEXITY-->

        <!--PRIORITY-->
            <div  style="display: none;" id="content_priority">

            <div class="container">
                <div class="row" align="center">
                    <h3 class="label label-info"><?=$chart_controller[2]?></h3>
                </div>
            </div>

            <hr>

            <div class="container" style="color: red">
                <div class="row">
                    <div class="col-lg-6">
                        <div id="containerPriorityAll" style="min-width: 310px; height: 300px; max-width: 500px; margin: 0 auto"></div>
                    </div>

                    <div class="col-lg-6">
                        <div id="containerPriorityProject" style="min-width: 310px; height: 400px; max-width: 600px; margin: 0 auto"></div>
                    </div>

                    <div class="clearfix"></div>
                    <hr>

                    <div class="col-lg-6">
                        <div id="containerPriorityAllComplete" style="min-width: 310px; height: 300px; max-width: 500px; margin: 0 auto"></div>
                    </div>

                    <div class="col-lg-6">
                        <div id="containerPriorityProjectComplete" style="min-width: 310px; height: 400px; max-width: 600px; margin: 0 auto"></div>
                    </div>


                    <div class="clearfix"></div>
                    <hr>

                    <div class="col-lg-6">
                        <div id="containerPriorityAllNeedDo" style="min-width: 310px; height: 300px; max-width: 500px; margin: 0 auto"></div>
                    </div>

                    <div class="col-lg-6">
                        <div id="containerPriorityProjectNeedDo" style="min-width: 310px; height: 400px; max-width: 600px; margin: 0 auto"></div>
                    </div>
                </div>
            </div>

        </div>
        <!--END PRIORITY-->
    </div>


    <!--HighChar-->
    <!--!!!!!!!!! Если использовать в комерческом проекто, то эти графики ПЛАТНЫЕ http://shop.highsoft.com/highcharts.html-->
    <!--!!!!!!!!! If you use a commercial project, these charts are PAID http://shop.highsoft.com/highcharts.html-->
    <script src="<?=base_url()?>js/highcharts.js"></script>
    <?php if($additionalInfoUser['showOrNot3DChars'] == 1):?>
        <!--Если удалить, то графики не будут 3D-->
        <script src="<?=base_url()?>js/highcharts-3d.js"></script>
    <?php endif;?>

    <?php if($additionalInfoUser['showOrNotExportChars'] == 1):?>
        <!--Добавляет кнопку экспорта в картинку текущий график-->
        <script src="<?=base_url()?>js/exporting.js"></script>
    <?php endif;?>

    <!--Подключаем обобщенные функции для построения графиков-->
    <script src="<?=base_url()?>js/charts.js"></script>
    <script>

        function showPriority()
        {
            var data, categories;
            /**
             * Круглые графики - приоритет
             */
            data =  <?=$seriesForJsPriorityAll?>;
            getCircleChar("containerPriorityAll", data, colorPriority, titleCircle, 'all');

            data =  <?=$seriesForJsPriorityAllComplete?>;
            getCircleChar("containerPriorityAllComplete", data, colorPriority, titleCircle, 'complete');

            data =  <?=$seriesForJsPriorityAllNeedDo?>;
            getCircleChar("containerPriorityAllNeedDo", data, colorPriority, titleCircle, 'needDo');

            /**
             * Квадратные графики - приоритет
             */
            data =  <?=$seriesForJsPriorityProject?>;
            categories = <?=$titleForJsPriorityProject?>;
            getSquareChar('containerPriorityProject', data, colorPriority, titleSquare, categories, 'all');

            data =  <?=$seriesForJsPriorityProjectComplete?>;
            categories = <?=$titleForJsPriorityProjectComplete?>;
            getSquareChar('containerPriorityProjectComplete', data, colorPriority, titleSquare, categories, 'complete');

            data =  <?=$seriesForJsPriorityProjectNeedDo?>;
            categories = <?=$titleForJsPriorityProjectNeedDo?>;
            getSquareChar('containerPriorityProjectNeedDo', data, colorPriority, titleSquare, categories, 'needDo');
        }

        //цвета графиков для "сложности"
        var colorComplexity =  <?=$colorsForJsComplexity?>;
        //цвета графиков для "приоритета"
        var colorPriority =  <?=$colorsForJsPriority?>;
        //языковые данные, по каждому из графиков, на том языке, который выбрал юзер в строке браузера
        var titleCircle = <?=$titleForJsCPCircle?>;
        var titleSquare = <?=$titleForJsCPSquare?>;

        $(function () {







            /**
             * Круглые графики - сложность
             */
            var data =  <?=$seriesForJsComplexityAll?>;
            getCircleChar("containerComplexityAll", data, colorComplexity, titleCircle, 'all');

            data =  <?=$seriesForJsComplexityAllComplete?>;
            getCircleChar("containerComplexityAllComplete", data, colorComplexity, titleCircle, 'complete');

            data =  <?=$seriesForJsComplexityAllNeedDo?>;
            getCircleChar("containerComplexityAllNeedDo", data, colorComplexity, titleCircle, 'needDo');

            /**
             * Квадратные графики - сложность
             */
            data =  <?=$seriesForJsComplexityProject?>;
            var categories = <?=$titleForJsComplexityProject?>;
            getSquareChar('containerComplexityProject', data, colorComplexity, titleSquare, categories, 'all');

            data =  <?=$seriesForJsComplexityProjectComplete?>;
            categories = <?=$titleForJsComplexityProjectComplete?>;
            getSquareChar('containerComplexityProjectComplete', data, colorComplexity, titleSquare, categories, 'complete');

            data =  <?=$seriesForJsComplexityProjectNeedDo?>;
            categories = <?=$titleForJsComplexityProjectNeedDo?>;
            getSquareChar('containerComplexityProjectNeedDo', data, colorComplexity, titleSquare, categories, 'needDo');


            var showPriorityBool = false;
            $(".clickLabel").on('click', function(){
                $(".activeLabel").removeClass("activeLabel");
                $(this).addClass("activeLabel");

                var whatShow = $(this).attr("show");
                $("#allCharts").fadeOut(150, function(){
                    $(this).children().hide();
                    $("#"+whatShow).show();
                    $(this).fadeIn(150,function(){
                        if(whatShow == "content_priority" && showPriorityBool === false)
                        {
                            showPriorityBool = true;
                            showPriority();
                        }
                    });
                });
            });


        });
    </script>

<?php else:?>

    <div class="container">
        <div class="row" align="center">
            <div class="alert alert-danger col-lg-12">
                Вы не создали ни одной задачи
            </div>
        </div>
    </div>
<?php endif;?>