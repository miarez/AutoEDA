<?php
require_once "../utils.php";
require_once "../src/Inference.php";
require_once "../src/Transformations.php";


$data = [["Name", "Age"], ["Alice", 30], ["Bob", 25]];
$data = [['Alice', 'Bob'], [30, 25]];
//$data = [['Alice', 'Bob'], [30, 25], [30, 25]];

$type = (new Inference())->get_best_match($data);

pp($type, 1);
