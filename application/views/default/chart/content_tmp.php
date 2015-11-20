

<script src="<?=base_url()?>js/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-3d.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>

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
                text: 'Число всех задач, с сортировкой по приоритету и проекту, которые ждут выполнения'
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
                categories: ['первый проект', 'второй проект'],
                title: {
                    text: 'Название проекта'
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
                headerFormat: '<b>{point.key}</b><br>',
                //pointFormat: '<span style="color:{series.color}">\u25CF</span> {point.y} ч.'
                pointFormat: '<span style="color:{series.color}">\u25CF</span> {series.name}: {point.y} ч.'
            },

            plotOptions: {
                column: {
                    stacking: 'normal',
                    depth: 50
                }
            },

            series: [{
                name: 'Коля',
                data: [<?=$hours__;?>, <?=$hours__ + 3.25;?>]
            },
                {
                    name: 'Вася',
                    data: [<?=$hours__ - 2;?>, <?=$hours__ + 1.65;?>]
                }]
        });

    });
</script>