<?php

/**
 * I brute forced this into working. Its 2:30am. I'm pushing this and going to sleep. Don't judge me for whats below lol.
 */

// Scalar type checking functions
function isNA($input) {
    return $input === 'NA'; // Adjust based on how NA is represented
}

function isNull($input) {
    return $input === null;
}

function isNaN($input) {
    try {
        return is_nan($input);
    } catch (Error $e){
        return false;
    }
}

function isBoolean($input) {
    return is_bool($input);
}

function isByte($input) {
    if (!is_string($input)) {
        return false;
    }

    // Check if the string is a hexadecimal representation
    if (preg_match('/^0x[0-9A-Fa-f]{1,2}$/', $input)) {
        // Convert the hexadecimal string to an integer
        $intValue = hexdec($input);

        // Check if the integer is in the byte range
        return $intValue >= 0x00 && $intValue <= 0xFF;
    }

    return false;
}


function isNumeric($input) {
    return is_numeric($input);
}

function isString($input) {
    return is_string($input);
}

function isDate($input) {
    // Define an array of expected date formats
    $dateFormats = ['Y-m-d', 'Y-m-d H:i:s', 'm/d/Y', 'm/d/Y H:i:s', 'Y-m-d\TH:i:s','Y-m-d\TH:i:s\Z'];

    // Check for string format dates
    if (is_string($input)) {
        foreach ($dateFormats as $format) {
            $date = DateTime::createFromFormat($format, $input);
            if ($date && $date->format($format) == $input) {
                return true;
            }
        }
    }

    // Check for numeric epoch timestamps
    if (is_numeric($input)) {
        $inputStr = (string)$input;
        if (strlen($inputStr) === 10 && strtotime('@'.$input) !== false) {
            return true;
        }
    }

    return false;
}



function isLocation($input) {
    // Basic check - consider integrating a location detection library for robustness
    if (!is_string($input)) {
        return false;
    }
    // Simple examples, consider expanding this
    $knownLocations = ['United States of America', 'USA', 'us', 'USA', 'FR', 'CA', 'US', 'CA', 'FR', 'FR','USA', 'FR', 'CA'];
    return in_array($input, $knownLocations, true);
}

// 1-dimensional type checking functions
function isArray($input) {
    return is_array($input);
}

function isVector($input) {
    if (!isArray($input)) return false;
    $firstType = gettype($input[0]);
    foreach ($input as $item) {
        if (gettype($item) !== $firstType) {
            return false;
        }
    }
    return true;
}

function isNumericVector($input) {
    return isVector($input) && allNumeric($input);
}

function isByteVector($input) {
    return isVector($input) && allByte($input);
}

function isDateVector($input) {
    return isVector($input) && allDate($input);
}

function isLocationVector($input) {
    return isVector($input) && allLocation($input);
}

function isSeries($input) {
    // Ensure the input is a vector and has at least two elements
    if (!isVector($input) || sizeof($input) < 2) {
        return false;
    }

    # this sucks
    if(is_string($input[0]) && !isDate($input[0])){
        return false;
    }

    // Determine if the vector is in ascending or descending order
    $ascendingOrder = true;
    $descendingOrder = true;

    $previousValue = $input[0];
    for ($i = 1; $i < sizeof($input); $i++) {
        if ($input[$i] < $previousValue) {
            $ascendingOrder = false;
        }
        if ($input[$i] > $previousValue) {
            $descendingOrder = false;
        }
        $previousValue = $input[$i];

        // If neither ascending nor descending order is maintained, break early
        if (!$ascendingOrder && !$descendingOrder) {
            return false;
        }
    }

    return true;
}

function isDateSeries($input) {
    return isSeries($input) && allDate($input);
}

function _isSet($input) {
    return isArray($input) && count($input) === count(array_unique($input));
}

function isCategorySet($input) {
    return _isSet($input) && allString($input);
}

function isDateSet($input) {

    return _isSet($input) && allDate($input);
}

function isLocationSet($input) {
    return _isSet($input) && allLocation($input);
}

function isSeriesSet($input) {
    return isSeries($input) && _isSet($input);
}

function isDateSeriesSet($input) {
    return isDateSeries($input) && _isSet($input);
}

// 2-dimensional type checking functions
function isFrame($input) {
    if (!isArray($input)) return false;
    $length = count($input[0]);
    foreach ($input as $array) {
        if (!isArray($array) || count($array) !== $length) {
            return false;
        }
    }
    return true;
}

function isDictionary($input) {

    pp([isFrame($input) ,count($input) === 2], 1, 'wooofie');
    // Implement logic for a dictionary type
    return isFrame($input) && count($input) === 2;
}

function isDataFrame($input) {
    if (!isFrame($input)) return false;

    foreach ($input as $column) {
        if (!isVector($column)) {
            return false;
        }
    }
    return true;
}

function isCategoryNumericFrame($input) {
    // Implement logic for this specific type
    return isDataFrame($input);
}

function isDateNumericFrame($input) {
    // Implement logic for this specific type
    return isDataFrame($input);
}

function isMatrix($input) {
    if (!isFrame($input)) return false;
    $firstType = gettype($input[0][0]);
    foreach ($input as $row) {
        foreach ($row as $item) {
            if (gettype($item) !== $firstType) {
                return false;
            }
        }
    }
    return true;
}

// Helper functions to check all elements in an array
function allNumeric($array) {
    foreach ($array as $item) {
        if (!is_numeric($item)) return false;
    }
    return true;
}

function allByte($array) {
    foreach ($array as $item) {
        if (!isByte($item)) return false;
    }
    return true;
}

function allDate($array) {

    foreach ($array as $item) {

        if (!isDate($item)) return false;
    }
    return true;
}

function allLocation($array) {
    foreach ($array as $item) {
        if (!isLocation($item)) return false;
    }
    return true;
}

function allString($array) {
    foreach ($array as $item) {
        if (!is_string($item)) return false;
    }
    return true;
}
function allBoolean($array) {
    foreach ($array as $item) {
        if (!is_bool($item)) return false;
    }
    return true;
}

function is_1D(
    $array
) : bool
{
    return count($array) == count($array, COUNT_RECURSIVE);
}

function inferColumnType($column) : string
{
    // Assuming the $column is an array
    if (isVector($column)) {
        if (isDateSet($column)) return 'DateSet';
        if (isLocationSet($column)) return 'LocationSet';
        if(isCategorySet($column)) return 'CategorySet';


        if (allNumeric($column)) return 'NumericVector';
        if (allString($column)) return 'StringVector';
        if (allBoolean($column)) return 'BooleanVector';
        // Add more checks as needed for other Vector types
    } elseif (isSet($column)) {
        if (allDate($column)) return 'DateSet';
        // Add more checks as needed for other Set types
    }
    return 'Unknown';
}

function inferFrameType($frame) {
    if (!is_array($frame) || is_1D($frame)) return 'Not a Frame';

    $columnTypes = [];
    foreach ($frame as $column) {
        $columnTypes[] = inferColumnType($column);
    }

    # TODO
    # If one of them is a DateSeries, change the other columns to Series


    // Determine frame type based on column types
    if (count(array_unique($columnTypes)) === 1) {
        // All columns have the same type
        if ($columnTypes[0] === 'NumericVector') return 'Matrix';
        // Add more conditions for other uniform column type frames
    }else if (count($columnTypes) === 2 && (in_array('CategorySet', $columnTypes)||in_array('StringVector', $columnTypes)) && in_array('NumericVector', $columnTypes)) {
        return 'Dictionary';

    }else if (count(array_unique($columnTypes)) === 2) {

        if(in_array('CategorySet', $columnTypes) && in_array('NumericVector', $columnTypes)){
            return 'CategoryNumericFrame';
        }
        if((in_array('DateSet', $columnTypes) || in_array('DateSeriesSet', $columnTypes)) && in_array('NumericVector', $columnTypes)){
            return 'DateNumericFrame';
        }
    }
    if(!in_array('Unknown', $columnTypes) && sizeof($frame) > 1){
        return 'DataFrame';
    }
    return 'Frame';
}



function inferDataType($input) {
    // Check for scalar types first
    if (isNA($input)) return 'NA';
    if (isNull($input)) return 'Null';
    if (isNaN($input)) return 'NaN';
    if (isBoolean($input)) return 'Boolean';
    if (isByte($input)) return 'Byte';
    if (isNumeric($input)) {
        // Further checks to differentiate between Date and Numeric
        if (isDate($input)) return 'Date';
        return 'Numeric';
    }
    if (isString($input)) {
        // Further checks for String types that might be Date or Location
        if (isDate($input)) return 'Date';
        if (isLocation($input)) return 'Location';
        return 'String';
    }

    // Check for 1-dimensional types
    if (isArray($input)) {

        if(!is_1D($input)){
            return inferFrameType($input);
        }

        // Further checks for specific Array types
        if (isVector($input)) {
            if (isSeries($input)) {
                if (isDateSeries($input)) {
                    if(_isSet($input)){
                        return 'DateSeriesSet';
                    }
                    return 'DateSeries';
                }
                if(_isSet($input)){
                    return 'SeriesSet';
                }
                return 'Series';
            }


            if (_isSet($input)) {
                if (isDateSet($input)) return 'DateSet';
                if (isLocationSet($input)) return 'LocationSet';
                if (isCategorySet($input)) return 'CategorySet';

                if (isSeriesSet($input)) {
                    if (isDateSeriesSet($input)) return 'DateSeriesSet';
                    return 'SeriesSet';
                }
                return 'Set';
            }
            if (isNumericVector($input)) return 'NumericVector';
            if (isByteVector($input)) return 'ByteVector';
            if (isLocationVector($input)) return 'LocationVector';
            if (isDateVector($input)) return 'DateVector';


            return 'Vector';
        }

        return 'Array';
    }

    // Check for 2-dimensional types
    if (isFrame($input)) {
        if (isDictionary($input)) return 'Dictionary';
        if (isDataFrame($input)) {
            if (isCategoryNumericFrame($input)) return 'CategoryNumericFrame';
            if (isDateNumericFrame($input)) return 'DateNumericFrame';
            return 'DataFrame';
        }
        if (isMatrix($input)) return 'Matrix';
        return 'Frame';
    }

    // Default case if no other type matches
    return 'Unknown Type';
}
