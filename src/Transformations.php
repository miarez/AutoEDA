<?php

class Transformations {
    public static array $KNOWN_TRANSFORMATIONS = [
        # from type
        "_2xN_NumericMatrix" => [
            # to type               # via this method
            "_Nx2_NumericMatrix" => "transpose",
        ],
        "_2xN_CategoryNumericDictionaryFrame" => [
            "_Nx2_CategoryNumericDictionaryFrame" => "transpose",
        ],
        "_2xN_LocationCategoryNumericDictionaryFrame" => [
            "_Nx2_LocationCategoryNumericDictionaryFrame" => "transpose",
        ],
        "_CategorySetNumericVectorFrame" => [
            "_CategorySetNumericVectorFrame_Transposed" => "transpose",
        ],
    ];

    public static function pairwiseZip(array $array1, array $array2): array {
        $zippedArray = [];
        $length = min(count($array1), count($array2));
        for ($i = 0; $i < $length; $i++) {
            $zippedArray[] = [$array1[$i], $array2[$i]];
        }
        return $zippedArray;
    }


    public static function transpose(array $matrix): array {
        $transposed = [];
        foreach ($matrix as $row => $columns) {
            foreach ($columns as $column => $value) {
                $transposed[$column][$row] = $value;
            }
        }
        return $transposed;
    }

}
