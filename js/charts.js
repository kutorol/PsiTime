/**
 * Функция создания круглого графика с процентным соотношением
 * The function of creating a circular graph with percentage of
 * @param idEl
 * @param data
 * @param color
 * @param title
 * @param whatChar
 */
function getCircleChar(idEl, data, color, title, whatChar)
{
    $('#'+idEl).highcharts({
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie',
            options3d: {
                enabled: true,
                alpha: 45,
                beta: 0
            }
        },
        colors: color,
        title: {
            text: title[whatChar].main
        },
        subtitle: {
            text: title[whatChar].subtitle
        },
        tooltip: {
            pointFormat: '<b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                depth: 35,
                dataLabels: {
                    enabled: false
                },
                showInLegend: true
            }
        },
        series: [{
            colorByPoint: true,
            data: data
        }]
    });
}


/**
 * Функция создания квадратного графика с количественным соотношением
 * Function of creation of the square schedule with a quantitative ratio
 * @param idEl
 * @param data
 * @param color
 * @param title
 * @param categ
 * @param whatChar
 */
function getSquareChar(idEl, data, color, title, categ, whatChar)
{
    $('#'+idEl).highcharts({
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
        colors: color,
        title: {
            text: title[whatChar].main
        },
        subtitle: {
            text: title[whatChar].subtitle
        },
        xAxis: {
            categories: categ,
            title: {
                text: title[whatChar].xAxis
                }
            },
        yAxis: {
            allowDecimals: false,
            min: 0,
            title: {
                text: title[whatChar].yAxis
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
        series: data
    });
}