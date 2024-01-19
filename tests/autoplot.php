<?php

require_once "../utils.php";
require_once "../src/Transformations.php";
require_once "../src/Inference.php";
require_once "../src/Plots.php";
require_once "../src/AutoPlot.php";

$autoPlot = new AutoPlot();


$data = [[1,7],[2,8],[3,9],[10,13],[15,2],[2,18],[9,8],[7,2],[3,5],[5,9],[8,2],[4,5],[7,5],[3,3]];
$data = [[1, 2, 3, 10, 3], [7, 8, 9, 13, 5]];
$data = [["pizza", "hotdogs", "burgers", "ice cream", "chips"], [7, 8, 9, 13, 5]];
$data = [["US", "CA", "FR", "GB", "IT"], [7, 8, 9, 13, 5]];


//$data = [["pizza", "hotdogs", "burgers", "ice cream", "chips"], [7, 8, 9, 13, 5], [100, 200, 300, 240, 150]];
//$data = [["a", "b", "c", "d", "e", "f", "g", "h", "i", "j"], [7, 8, 9, 13, 5, 100, 200, 300, 240, 150]];

$data = [["2023-01-01", "2023-01-02", "2023-01-03", "2023-01-04", "2023-01-05"], [7, 8, 9, 13, 5], [100, 200, 300, 240, 150]];


$type = (new Inference())->get_best_match($data);

//pp($type, 1);



$json = ($autoPlot)->try_plot($type);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>AutoPlot</title>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart', 'scatter', 'geochart', 'bar']});

        const cs = console.log;

        let _data = <?= $json;?>

        window.onload = function () {

            switch(_data.type) {
                case "Google_Pie":
                    renderPie(JSON.parse(_data.data))
                    break;
                case "Google_Scatter":
                    renderScatter(JSON.parse(_data.data))
                    break;
                case "Google_GeoPlot":
                    renderGeoPlot(JSON.parse(_data.data))
                    break;
                case "Google_BarChart":
                    renderBar(JSON.parse(_data.data))
                    break;
                case "Google_LineChart":
                    renderLine(JSON.parse(_data.data))
                    break;
                default:
                    cs("No Known Plot Can Be Rendered")
                    break;
            }
        }

        function renderScatter (_data) {
            let data = new google.visualization.DataTable();

            for(let i = 0; i < _data[0].length; i++)
            {
                data.addColumn('number', `series${i}`);
            }
            data.addRows(_data);

            var options = {
                title: 'Scatter Plot',
                width: 800,
                height: 500
            };

            var chart = new google.charts.Scatter(document.getElementById('chart_div'));
            chart.draw(data, google.charts.Scatter.convertOptions(options));
        }

        function renderPie(
            data_to_render
        ) {

            let data = new google.visualization.DataTable();

            data.addColumn('string', `category`);
            data.addColumn('number', `series`);
            data.addRows(data_to_render);

            let options = {
                title: 'Pie Chart',
                width: 800,
                height: 500
            };

            let chart = new google.visualization.PieChart(document.getElementById('chart_div'));
            chart.draw(data, options);
        }

        function renderGeoPlot() {

            let data = new google.visualization.DataTable();
            data.addColumn('string', `Country`);
            data.addColumn('number', `series`);
            data.addRows([
                ['Germany', 200],
                ['United States', 300],
                ['Brazil', 400],
                ['Canada', 500],
                ['France', 600],
                ['RU', 700]
            ]);

            var options = {};
            var chart = new google.visualization.GeoChart(document.getElementById('chart_div'));

            chart.draw(data, options);
        }

        function renderBar(
            _data
        ) {
            let data = new google.visualization.DataTable()

            data.addColumn('string', 'Category')
            for(let i = 1; i < _data[0].length; i++)
            {
                data.addColumn('number', `series${i}`);
            }
            data.addRows(_data);

            let options = {
                title: 'Bar Chart',
                width: 800,
                height: 500,
                bars: 'vertical' //horizontal
            };

            let chart = new google.charts.Bar(document.getElementById('chart_div'));
            chart.draw(data, google.charts.Bar.convertOptions(options));
        }


        function renderLine(
            _data
        ) {
            let data = new google.visualization.DataTable()

            data.addColumn("string", "Date")
            for(let i = 1; i < _data[0].length; i++)
            {
                data.addColumn('number', `series${i}`);
            }

            data.addRows(_data);

            let options = {
                title: 'Line Chart',
                curveType: 'function',
                legend: { position: 'bottom' }
            };
            let chart = new google.visualization.LineChart(document.getElementById('chart_div'));
            chart.draw(data, options);
        }




    </script>

</head>
<body>
<div id="chart_div" style="width: 900px; height: 500px;"></div>
</body>
</html>

