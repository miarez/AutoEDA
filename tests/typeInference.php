<?php
/**
 * Code IS WIP. I'm tired, its 2:30am, shit is messy but fuck it it works (sorta)
 */

require_once "../utils.php";
require_once "../src/TypeInference.php";

$tests = [
    // Scalar Types
    ['name' => 'NA Test', 'value' => 'NA', 'expected_output' => 'NA'],
    ['name' => 'Null Test', 'value' => null, 'expected_output' => 'Null'],
    ['name' => 'NaN Test', 'value' => NAN, 'expected_output' => 'NaN'],
    ['name' => 'Boolean True Test', 'value' => true, 'expected_output' => 'Boolean'],
    ['name' => 'Boolean False Test', 'value' => false, 'expected_output' => 'Boolean'],
    ['name' => 'Byte Test', 'value' => '0x5A', 'expected_output' => 'Byte'],
    ['name' => 'Numeric Integer Test', 'value' => 42, 'expected_output' => 'Numeric'],
    ['name' => 'Numeric Float Test', 'value' => 3.14, 'expected_output' => 'Numeric'],
    ['name' => 'String Test', 'value' => 'Hello, World!', 'expected_output' => 'String'],
    ['name' => 'Date String Test', 'value' => '2024-01-12', 'expected_output' => 'Date'],
    ['name' => 'Date Epoch Test', 'value' => '1705128571', 'expected_output' => 'Date'],
    ['name' => 'Location Test', 'value' => 'USA', 'expected_output' => 'Location'],


    // List Types
    ['name' => 'Array Test', 'value' => [1, "apple", true], 'expected_output' => 'Array'],
    ['name' => 'Vector Test', 'value' => ['hello', 'world', 'meow', 'meow'], 'expected_output' => 'Vector'],
    ['name' => 'NumericVector Test', 'value' => [1.1, 2.2, 3.3, 1.4, 1.4], 'expected_output' => 'NumericVector'],
    ['name' => 'ByteVector Test', 'value' => ['0x5A', '0x2D', '0x6F', '0x6F'], 'expected_output' => 'ByteVector'],
    ['name' => 'DateVector Test', 'value' => ['2023-01-03T00:00:00', '2023-01-02T00:00:00', '2023-01-03T00:00:01', '2023-01-03T00:00:01'], 'expected_output' => 'DateVector'],
    ['name' => 'LocationVector Test', 'value' => ['US', 'CA', 'FR', 'FR'], 'expected_output' => 'LocationVector'],
    ['name' => 'Series Test', 'value' => [0, 1, 1, 2, 3, 4], 'expected_output' => 'Series'],
    ['name' => 'DateSeries Test', 'value' => ['2024-01-12', '2024-01-13', '2024-01-14', '2024-01-14'], 'expected_output' => 'DateSeries'],
    ['name' => 'Set Test', 'value' => [3, 1, 4, 2], 'expected_output' => 'Set'],
    ['name' => 'CategorySet Test', 'value' => ['apple', 'banana', 'cherry', 'airpods'], 'expected_output' => 'CategorySet'],
    ['name' => 'DateSet Test', 'value' => ['2024-01-12', '2024-01-13', '2024-01-14', '2024-01-11'], 'expected_output' => 'DateSet'],
    ['name' => 'LocationSet Test', 'value' => ['USA', 'FR', 'CA'], 'expected_output' => 'LocationSet'],
    ['name' => 'SeriesSet Test', 'value' => [0, 1, 2, 3, 4], 'expected_output' => 'SeriesSet'],
    ['name' => 'DateSeriesSet Test', 'value' => ['2024-01-12', '2024-01-13', '2024-01-14'], 'expected_output' => 'DateSeriesSet'],

    // Table Types
    ['name' => 'Frame Test', 'value' => [["Name", "Age"], ["Alice", 30], ["Bob", 25]], 'expected_output' => 'Frame'],
    ['name' => 'Dictionary Test', 'value' => [['Alice', 'Bob'], [30, 25]], 'expected_output' => 'Dictionary'],
    ['name' => 'DataFrame Test', 'value' => [['Alice', 'Bob'], [30, 25], [true, false]], 'expected_output' => 'DataFrame'],
    ['name' => 'CategoryNumericFrame Test', 'value' => [['Alice', 'Bob'], [30, 25], [100, 109]], 'expected_output' => 'CategoryNumericFrame'],
    ['name' => 'DateNumericFrame Test', 'value' => [['2024-01-12', '2024-01-13'], [30, 25], [100, 109]], 'expected_output' => 'DateNumericFrame'],
    ['name' => 'Matrix Test', 'value' => [[1, 2, 3], [4, 5, 6], [7, 8, 9]], 'expected_output' => 'Matrix'],
];


$out = $detailed_out = [];
foreach ($tests as $index=>$test) {
    $result = inferDataType($test['value']);
    $success = $result === $test['expected_output'] ? 1 : 0;
    $out[$test['name']] = $success ?: $result;
    $detailed_out[$test['name']] =
        [
            "expected_output" => $test['expected_output'],
            "output" => $result,
            "success" => $success,
        ];
}

pp($out, 1);
pp($detailed_out, 1);