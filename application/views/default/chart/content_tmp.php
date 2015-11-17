

<script src="<?=base_url()?>js/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-3d.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>

<div class="container">
    <div class="row">
        <div>
            Всего затрачено времени на все проекты: <?=$myTimeCompliteForTask;?>
            <br>
            Всего секунд: <?=$time['allSeconds'];?>
            <br>
            По проектам:<br>

            <?php $i = 0; $countP = count($time['by_project']); $aaa = ""; foreach($time['by_project'] as $k=>$project):?>

                <?php if($i < $countP):?>
                    <?php
                        $aaa .= "{name: '".$k."',data: [".$project."]},";
                    ?>
                <?php else:?>
                    <?php
                    $aaa .= "{name: '".$k."',data: [".$project."]}";
                    ?>
                <?php endif;?>
                <?=$k?> (<?=$project?>) <br>
            <?php endforeach;?>


        </div>
        <hr>
        <div id="container_23" style="width:100%; height:400px;"></div>
<hr>
        <div id="container" style="min-width: 310px; height: 400px; max-width: 600px; margin: 0 auto"></div>
    </div>
</div>





<script>
    $(function () {
        $('#container_23').highcharts({
            chart: {
                type: 'column',
                margin: 75,
                options3d: {
                    enabled: true,
                    alpha: 10,
                    beta: 25,
                    depth: 70
                }
            },
            title: {
                text: '3D chart with null values'
            },
            subtitle: {
                text: 'Notice the difference between a 0 value and a null point'
            },
            plotOptions: {
                column: {
                    depth: 40
                }
            },
            xAxis: {
                categories: ['Проекты']
            },
            yAxis: {
                title: {
                    text: "Время"
                }
            },
            series: [<?=$aaa?>]
        });


        // Build the chart
        $('#container').highcharts({
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            title: {
                text: 'Сложность задач за все время'
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: false
                    },
                    showInLegend: true
                }
            },
            series: [{

                colorByPoint: true,
                data: [{
                    name: 'Microsoft Internet Explorer',
                    y: 56.33
                }, {
                    name: 'Chrome',
                    y: 24.03,
                    sliced: true,
                    selected: true
                }, {
                    name: 'Firefox',
                    y: 10.38
                }, {
                    name: 'Safari',
                    y: 4.77
                }, {
                    name: 'Opera',
                    y: 0.91
                }, {
                    name: 'Proprietary or Undetectable',
                    y: 0.2
                }]
            }]
        });

    });
</script>