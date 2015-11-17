
<!--TODO позволить человеку выбирать в каком виде показывать графики - 3d или нет. так же при  ригистрации он должен указать количество рабочего времени!!!!!!!!!!-->


<script src="<?=base_url()?>js/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-3d.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>

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





<script>
    $(function () {

        // Build the chart
        $('#containerComplexityAll').highcharts({
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            colors: [<?=$colorsForJsComplexity?>],
            title: {
                text: 'Число всех задач, с сортировкой по сложности'
            },
            subtitle: {
                text: '(за все время)'
            },
            tooltip: {
                pointFormat: '<b>{point.percentage:.1f}%</b>'
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
                data: [<?=$seriesForJsComplexityAll?>]
            }]
        });

        $('#containerComplexityProject').highcharts({
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
            colors: [<?=$colorsForJsComplexity?>],
            title: {
                text: 'Число всех задач, с сортировкой по сложности и проекту'
            },
            subtitle: {
                text: '(за все время)'
            },
            plotOptions: {
                column: {
                    depth: 25
                }
            },
            xAxis: {
                categories: [<?=$titleForJsComplexityProject?>],
                title: {
                    text: 'Название проекта'
                }
            },
            yAxis: {
                allowDecimals: false,
                min: 0,
                title: {
                    text: 'Количество задач'
                }
            },
            tooltip: {
                headerFormat: '<b>{point.key}</b><br>',
                pointFormat: '<span style="color:{series.color}">\u25CF</span> {series.name}: {point.y} / {point.stackTotal}'
            },

            plotOptions: {
                column: {
                    stacking: 'normal',
                    depth: 50
                }
            },

            series: [<?=$seriesForJsComplexityProject?>]
        });











        // Build the chart
        $('#containerComplexityAllComplete').highcharts({
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            colors: [<?=$colorsForJsComplexity?>],
            title: {
                text: 'Число всех задач, с сортировкой по сложности и статусом "выполнено"'
            },
            subtitle: {
                text: '(за все время)'
            },
            tooltip: {
                pointFormat: '<b>{point.percentage:.1f}%</b>'
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
                data: [<?=$seriesForJsComplexityAllComplete?>]
            }]
        });

        $('#containerComplexityProjectComplete').highcharts({
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
            colors: [<?=$colorsForJsComplexity?>],
            title: {
                text: 'Число всех задач, с сортировкой по сложности и проекту и статусом "выполнено"'
            },
            subtitle: {
                text: '(за все время)'
            },
            plotOptions: {
                column: {
                    depth: 25
                }
            },
            xAxis: {
                categories: [<?=$titleForJsComplexityProjectComplete?>],
                title: {
                    text: 'Название проекта'
                }
            },
            yAxis: {
                allowDecimals: false,
                min: 0,
                title: {
                    text: 'Количество задач'
                }
            },
            tooltip: {
                headerFormat: '<b>{point.key}</b><br>',
                pointFormat: '<span style="color:{series.color}">\u25CF</span> {series.name}: {point.y} / {point.stackTotal}'
            },

            plotOptions: {
                column: {
                    stacking: 'normal',
                    depth: 50
                }
            },

            series: [<?=$seriesForJsComplexityProjectComplete?>]
        });



        // Build the chart
        $('#containerComplexityAllNeedDo').highcharts({
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            colors: [<?=$colorsForJsComplexity?>],
            title: {
                text: 'Число всех задач, с сортировкой по сложности, которые ждут выполнения'
            },
            subtitle: {
                text: '(за все время)'
            },
            tooltip: {
                pointFormat: '<b>{point.percentage:.1f}%</b>'
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
                data: [<?=$seriesForJsComplexityAllNeedDo?>]
            }]
        });

        $('#containerComplexityProjectNeedDo').highcharts({
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
            colors: [<?=$colorsForJsComplexity?>],
            title: {
                text: 'Число всех задач, с сортировкой по сложности и проекту, которые ждут выполнения'
            },
            subtitle: {
                text: '(за все время)'
            },
            plotOptions: {
                column: {
                    depth: 25
                }
            },
            xAxis: {
                categories: [<?=$titleForJsComplexityProjectNeedDo?>],
                title: {
                    text: 'Название проекта'
                }
            },
            yAxis: {
                allowDecimals: false,
                min: 0,
                title: {
                    text: 'Количество задач'
                }
            },
            tooltip: {
                headerFormat: '<b>{point.key}</b><br>',
                pointFormat: '<span style="color:{series.color}">\u25CF</span> {series.name}: {point.y} / {point.stackTotal}'
            },

            plotOptions: {
                column: {
                    stacking: 'normal',
                    depth: 50
                }
            },

            series: [<?=$seriesForJsComplexityProjectNeedDo?>]
        });

    });
</script>