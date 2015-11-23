

<script src="<?=base_url()?>js/highcharts.js"></script>

<script src="https://code.highcharts.com/modules/exporting.js"></script>

<div class="container">
    <div class="row">
        <p>
            Всего потрачено времени (на все проекты):
            <ul>
                <?php foreach($allTimesUser as $k=>$time):?>
                    <li style="margin-top: 5px;"><?=$timeForUser[$k]['name']?>: <label class="label label-<?php if($k == $idUser){ echo "info";}?> small-text"><?=$time?></label></li>
                <?php endforeach;?>
            </ul>
        </p>
    </div>
</div>

<div class="container">
    <div class="row">

        <hr>
        <div id="containerPriorityProjectNeedDo" style="width:100%; height:400px;"></div>


    </div>
</div>





<script>
    $(function () {

        $('#containerPriorityProjectNeedDo').highcharts({
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
                text: 'Время всех, кто учавствует в ваших проектах'
            },
            subtitle: {
                text: '(за все время)'
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