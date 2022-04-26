<html><head>
    <title>chartjs-plugin-streaming sample</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/gh/google/code-prettify@master/loader/prettify.css">
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/min/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4"></script>
    <script src="https://cdn.jsdelivr.net/npm/hammerjs@2.0.8"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom@0.7.7"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-streaming@1.9.0"></script>
    <style>
        canvas {
            -moz-user-select: none;
            -webkit-user-select: none;
            -ms-user-select: none;
        }
        .chart {
            margin: auto;
            width: 75%;
        }
    </style>
    <style type="text/css">/* Chart.js */
        @keyframes chartjs-render-animation{from{opacity:.99}to{opacity:1}}.chartjs-render-monitor{animation:chartjs-render-animation 1ms}.chartjs-size-monitor,.chartjs-size-monitor-expand,.chartjs-size-monitor-shrink{position:absolute;direction:ltr;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1}.chartjs-size-monitor-expand>div{position:absolute;width:1000000px;height:1000000px;left:0;top:0}.chartjs-size-monitor-shrink>div{position:absolute;width:200%;height:200%;left:0;top:0}</style></head>

<body>
<div class="chart"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
    <canvas id="myChart" style="display: block; touch-action: none; user-select: none; -webkit-user-drag: none; -webkit-tap-highlight-color: rgba(0, 0, 0, 0); width: 1375px; height: 687px;" width="1375" height="687" class="chartjs-render-monitor"></canvas>
</div>
<div class="container mt-3">
    <p class="text-center">
        <button type="button" class="btn btn-outline-primary btn-sm" id="randomizeData">Randomize Data</button>
        <button type="button" class="btn btn-outline-primary btn-sm" id="addDataset">Add Dataset</button>
        <button type="button" class="btn btn-outline-primary btn-sm" id="removeDataset">Remove Dataset</button>
        <button type="button" class="btn btn-outline-primary btn-sm" id="addData">Add Data</button>
        <button type="button" class="btn btn-outline-primary btn-sm" id="resetZoom">Reset Zoom</button>
    </p>
</div>
<script>
    var chartColors = {
        red: 'rgb(255, 99, 132)',
        orange: 'rgb(255, 159, 64)',
        yellow: 'rgb(255, 205, 86)',
        green: 'rgb(75, 192, 192)',
        blue: 'rgb(54, 162, 235)',
        purple: 'rgb(153, 102, 255)',
        grey: 'rgb(201, 203, 207)'
    };

    function randomScalingFactor() {
        return (Math.random() > 0.5 ? 1.0 : -1.0) * Math.round(Math.random() * 100);
    }

    function onRefresh(chart) {
        var now = Date.now();
        chart.data.datasets.forEach(function(dataset) {
            dataset.data.push({
                x: now,
                y: randomScalingFactor()
            });
        });
    }

    var color = Chart.helpers.color;
    var config = {
        type: 'line',
        data: {
            datasets: [{
                label: 'Dataset 1 (linear interpolation)',
                backgroundColor: color(chartColors.red).alpha(0.5).rgbString(),
                borderColor: chartColors.red,
                fill: false,
                lineTension: 0,
                borderDash: [8, 4],
                data: []
            }, {
                label: 'Dataset 2 (cubic interpolation)',
                backgroundColor: color(chartColors.blue).alpha(0.5).rgbString(),
                borderColor: chartColors.blue,
                fill: false,
                cubicInterpolationMode: 'monotone',
                data: []
            }]
        },
        options: {
            title: {
                display: true,
                text: 'Zoom plugin sample'
            },
            scales: {
                xAxes: [{
                    type: 'realtime',
                    realtime: {
                        duration: 20000,
                        refresh: 1000,
                        delay: 2000,
                        onRefresh: onRefresh
                    }
                }],
                yAxes: [{
                    type: 'linear',
                    display: true,
                    scaleLabel: {
                        display: true,
                        labelString: 'value'
                    }
                }]
            },
            tooltips: {
                mode: 'nearest',
                intersect: false
            },
            hover: {
                mode: 'nearest',
                intersect: false
            },
            pan: {
                enabled: true,
                mode: 'x',
                rangeMax: {
                    x: 4000
                },
                rangeMin: {
                    x: 0
                }
            },
            zoom: {
                enabled: true,
                mode: 'x',
                rangeMax: {
                    x: 20000
                },
                rangeMin: {
                    x: 1000
                }
            }
        }
    };

    window.onload = function() {
        var ctx = document.getElementById('myChart').getContext('2d');
        window.myChart = new Chart(ctx, config);
    };

    document.getElementById('randomizeData').addEventListener('click', function() {
        config.data.datasets.forEach(function(dataset) {
            dataset.data.forEach(function(dataObj) {
                dataObj.y = randomScalingFactor();
            });
        });

        window.myChart.update();
    });

    var colorNames = Object.keys(chartColors);
    document.getElementById('addDataset').addEventListener('click', function() {
        var colorName = colorNames[config.data.datasets.length % colorNames.length];
        var newColor = chartColors[colorName];
        var newDataset = {
            label: 'Dataset ' + (config.data.datasets.length + 1),
            backgroundColor: color(newColor).alpha(0.5).rgbString(),
            borderColor: newColor,
            fill: false,
            lineTension: 0,
            data: []
        };

        config.data.datasets.push(newDataset);
        window.myChart.update();
    });

    document.getElementById('removeDataset').addEventListener('click', function() {
        config.data.datasets.pop();
        window.myChart.update();
    });

    document.getElementById('addData').addEventListener('click', function() {
        onRefresh(window.myChart);
        window.myChart.update();
    });

    document.getElementById('resetZoom').addEventListener('click', function() {
        window.myChart.resetZoom();
    });
</script>



</body></html>
