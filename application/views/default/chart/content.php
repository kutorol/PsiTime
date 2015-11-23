<?php if(!isset($notTask)):?>

    <div class="container" >
        <div class="row">
            <div class="col-lg-4">
                Показывать графики в 3D: <input id="showOrNot3DChars" type="checkbox" <?php if($additionalInfoUser['showOrNot3DChars'] == 1){echo "checked class='activeCheckbox'";}?>  value="1"/>
            </div>
            <div class="col-lg-4" >
                Показывать кнопку экспорта графика: <input id="showOrNotExportChars" type="checkbox" <?php if($additionalInfoUser['showOrNotExportChars'] == 1){echo "checked class='activeCheckbox'";}?> value="1"/>
            </div>
        </div>
    </div>

    <hr>



    <!--Подключаем обобщенные функции для построения графиков-->
    <script src="<?=base_url()?>js/charts.js"></script>
    <script>

        /**
         * Показываем вкладку с графиками, сортированными по приоритету
         * We show the tab with the schedules sorted by a priority
         */
        function showPriority()
        {
            var data, categories;
            /**
             * Круглые графики - приоритет
             * Round charts - priority
             */
            getCircleChar("containerPriorityAll", <?=$seriesForJsPriorityAll?>, colorPriority, titleCircle, 'all');
            getCircleChar("containerPriorityAllComplete", <?=$seriesForJsPriorityAllComplete?>, colorPriority, titleCircle, 'complete');
            getCircleChar("containerPriorityAllNeedDo", <?=$seriesForJsPriorityAllNeedDo?>, colorPriority, titleCircle, 'needDo');

            /**
             * Квадратные графики - приоритет
             * Square charts - priority
             */
            getSquareChar('containerPriorityProject', <?=$seriesForJsPriorityProject?>, colorPriority, titleSquare, <?=$titleForJsPriorityProject?>, 'all');
            getSquareChar('containerPriorityProjectComplete', <?=$seriesForJsPriorityProjectComplete?>, colorPriority, titleSquare, <?=$titleForJsPriorityProjectComplete?>, 'complete');
            getSquareChar('containerPriorityProjectNeedDo', <?=$seriesForJsPriorityProjectNeedDo?>, colorPriority, titleSquare, <?=$titleForJsPriorityProjectNeedDo?>, 'needDo');
        }

        /**
         * Показываем вкладку с графиками, сортированными по сложности
         * We show the tab with the schedules sorted by a complexity
         */
        function showComplexity()
        {
            /**
             * Круглые графики - сложность
             * Round charts - complexity
             */
            getCircleChar("containerComplexityAll", <?=$seriesForJsComplexityAll?>, colorComplexity, titleCircle, 'all');
            getCircleChar("containerComplexityAllComplete", <?=$seriesForJsComplexityAllComplete?>, colorComplexity, titleCircle, 'complete');
            getCircleChar("containerComplexityAllNeedDo", <?=$seriesForJsComplexityAllNeedDo?>, colorComplexity, titleCircle, 'needDo');

            /**
             * Квадратные графики - сложность
             * Square charts - complexity
             */
            getSquareChar('containerComplexityProject', <?=$seriesForJsComplexityProject?>, colorComplexity, titleSquare, <?=$titleForJsComplexityProject?>, 'all');
            getSquareChar('containerComplexityProjectComplete', <?=$seriesForJsComplexityProjectComplete?>, colorComplexity, titleSquare, <?=$titleForJsComplexityProjectComplete?>, 'complete');
            getSquareChar('containerComplexityProjectNeedDo', <?=$seriesForJsComplexityProjectNeedDo?>, colorComplexity, titleSquare, <?=$titleForJsComplexityProjectNeedDo?>, 'needDo');
        }

        //цвета графиков для "сложности"
        var colorComplexity =  <?=$colorsForJsComplexity?>;
        //цвета графиков для "приоритета"
        var colorPriority =  <?=$colorsForJsPriority?>;

        //языковые данные, по каждому из графиков, на том языке, который выбрал юзер в строке браузера
        var titleCircle = <?=$titleForJsCPCircle?>;
        var titleSquare = <?=$titleForJsCPSquare?>;

        $(function () {

            //показываем вначале только графиги с сортировкой по сложности
            $('#containerTimeAllUsers').highcharts({
                chart: {
                    type: 'column',
                    options3d: {
                        enabled: true,
                        alpha: 15,
                        beta: 15,
                        viewDistance: 25,
                        depth: 40
                    },
                    marginTop: 80,
                    marginRight: 40
                },
                title: {
                    text: 'Сколько потрачено времени всеми участниками'
                },
                subtitle: {
                    text: '(за все время, по всем проектам)'
                },
                plotOptions: {
                    column: {
                        depth: 25
                    }
                },

                yAxis: {
                    allowDecimals: false,
                    min: 0,
                    title: {
                        text: 'Количество часов'
                    }
                },
                tooltip: {
                    headerFormat: '<span style="color:{series.color}">\u25CF</span> {series.name}: {point.y} ч.',
                    //pointFormat: '<span style="color:{series.color}">\u25CF</span> {point.y} ч.'
                    pointFormat: ''
                },

                plotOptions: {
                    column: {
                        stacking: 'normal',
                        depth: 50
                    }
                },

                series: <?=$series;?>
            });
        });
    </script>



    <div class="container">
        <div class="row">
            <p>Сортировать по:</p>
            <span class="clickLabel activeLabel" show="content_time">Времени</span>
            <span class="clickLabel" show="content_complexity">Сложности</span>
            <span class="clickLabel" show="content_priority">Приоритету</span>
        </div>
    </div>


    <div id="allCharts">
        <!--TIME-->
        <div id="content_time">
            <p>&nbsp;</p>
            <div class="container">
                <div class="row">
                    <p>
                        Всего потрачено времени (на все проекты):
                    <ul>
                        <?php $i=0; $showUsers = false; $countTime = count($allTimesUser) - 1; foreach($allTimesUser as $k=>$time):?>
                            <?php if($i > 0):?>

                                <?php if($showUsers === false): $showUsers = true;?>
                                    <li id="showUsersLink"><a href="">Показать остальных членов команды <i class="fa fa-long-arrow-down"></i></a></li>
                                <?php endif;?>

                                <li class="liNotDisplayUserTime" style="margin-top: 5px; display: none;"><?=$timeForUser[$k]['name']?>: <label class="label label-<?php if($k == $idUser){ echo "info";}?> small-text"><?=$time?></label></li>

                                <?php if($i == $countTime):?>
                                    <li class="liNotDisplayUserTime aDisplay" style="margin-top: 5px; display: none;"><a href="">Скрыть остальных  членов команды <i class="fa fa-long-arrow-up"></i></a></li>
                                <?php endif;?>

                            <?php else:?>
                                <li style="margin-top: 5px;"><?=$timeForUser[$k]['name']?>: <label class="label label-<?php if($k == $idUser){ echo "info";}?> small-text"><?=$time?></label></li>
                            <?php endif;?>
                        <?php $i++; endforeach;?>
                    </ul>
                    </p>
                </div>
            </div>

            <hr>

            <div class="container" >
                <div class="row">
                    <div id="containerTimeAllUsers" style="min-width: 310px; height: 400px; max-width: 900px; margin: 0 auto"></div>
                </div>
            </div>
        </div>
        <!--END TIME-->


        <!--COMPLEXITY-->
        <div  style="display: none;" id="content_complexity">

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



<?php else:?>

    <div class="container">
        <div class="row" align="center">
            <div class="alert alert-danger col-lg-12">
                Вы не создали ни одной задачи
            </div>
        </div>
    </div>
<?php endif;?>