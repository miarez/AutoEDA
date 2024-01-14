<?php

require_once '../utils.php';
require_once '../src/MatrixTranspose.php'; // Adjust the path as needed

// Test Data
$array1 = [1, 2, 3];
$array2 = [4, 5, 6];

$zipped = MatrixTranspose::pairwiseZip($array1, $array2);
//pp($zipped, 0, "pairwiseZip:");

$matrix = [
    [1, 2, 3],
    [4, 5, 6],
    [7, 8, 9]
];

$matrix = [
    [1, 2, 3],
    [4, 5, 6]
];

$transposed = MatrixTranspose::transpose($matrix);
pp($transposed, 1, "Transpose:");

// Testing trace
echo "\nTesting trace:\n";
$trace = MatrixTranspose::trace($matrix);
echo $trace . "\n";

$size = 3;
// Testing identity
echo "\nTesting identity:\n";
$identityMatrix = MatrixTranspose::identity($size);
print_r($identityMatrix);

