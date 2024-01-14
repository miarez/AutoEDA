<?php

require_once "../utils.php";
require_once "../src/Transformations.php";
require_once "../src/Inference.php";
require_once "../src/Plots.php";
require_once "../src/AutoPlot.php";

$autoPlot = new AutoPlot();


$data = [[1,7],[2,8],[3,9],[10,13],[15,2],[2,18],[9,8],[7,2],[3,5],[5,9],[8,2],[4,5],[7,5],[3,3]];

$data = [[1, 2, 3, 10, 3], [7, 8, 9, 13, 5]];
$type = (new Inference())->get_best_match($data);
$json = ($autoPlot)->try_plot($type);

?>

<!DOCTYPE html>
<html lang="en">
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

