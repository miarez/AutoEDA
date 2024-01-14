<?php

require_once "../utils.php";
require_once "../src/MatrixTranspose.php";
require_once "../src/Inference.php";
require_once "../src/Plot.php";

pp(getPlotConstructorDataTypes());

$data = [[1, 2, 3, 10, 3], [7, 8, 9, 13, 5]];

$type = (new Inference)->get_best_match($data);
$json = (new Scatter($type))->for_google_charts();


?>

<html lang="">
<head>
    <title>AutoPlot</title>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {'packages':['scatter']});

        const cs = console.log;

        let _data = <?= $json;?>

        window.onload = function () {
            renderScatter(_data)
        }

        function renderScatter (_data)
        {
            let data = new google.visualization.DataTable();
            for(let i = 0; i < _data[0].length; i++)
            {
                data.addColumn('number', `series${i}`);
            }
            data.addRows(_data);
            var options = {
                width: 800,
                height: 500
            };

            var chart = new google.charts.Scatter(document.getElementById('chart_div'));
            chart.draw(data, google.charts.Scatter.convertOptions(options));
        }

    </script>

</head>
<body>
<div id="chart_div" style="width: 900px; height: 500px;"></div>
</body>
</html>

