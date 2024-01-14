<?php

class MatrixTranspose {

    /**
     * Pairwise zips two arrays into an Nx2 matrix.
     *
     * @param array $array1 The first array.
     * @param array $array2 The second array.
     * @return array The pairwise zipped array.
     */
    public static function pairwiseZip(array $array1, array $array2): array {
        $zippedArray = [];
        $length = min(count($array1), count($array2));
        for ($i = 0; $i < $length; $i++) {
            $zippedArray[] = [$array1[$i], $array2[$i]];
        }
        return $zippedArray;
    }

    /**
     * Transposes a matrix.
     * @param array $matrix The matrix to transpose.
     * @return array The transposed matrix.
     */
    public static function transpose(array $matrix): array {
        $transposed = [];
        foreach ($matrix as $row => $columns) {
            foreach ($columns as $column => $value) {
                $transposed[$column][$row] = $value;
            }
        }
        return $transposed;
    }

    /**
     * Calculates the determinant of a matrix.
     *
     * @param array $matrix The matrix.
     * @return float The determinant.
     */
    public static function determinant(array $matrix): float {
        // Determinant calculation logic goes here
    }

    /**
     * Inverts a matrix.
     *
     * @param array $matrix The matrix to invert.
     * @return array The inverted matrix.
     */
    public static function invert(array $matrix): array {
        // Inversion logic goes here
    }

    /**
     * Calculates the trace of a matrix.
     *
     * @param array $matrix The matrix.
     * @return float The trace value.
     */
    public static function trace(array $matrix): float {
        $trace = 0;
        $size = min(count($matrix), count($matrix[0]));
        for ($i = 0; $i < $size; $i++) {
            $trace += $matrix[$i][$i];
        }
        return $trace;
    }

    /**
     * Creates an identity matrix of a given size.
     *
     * @param int $size The size of the identity matrix.
     * @return array The identity matrix.
     */
    public static function identity(int $size): array {
        $identity = [];
        for ($i = 0; $i < $size; $i++) {
            for ($j = 0; $j < $size; $j++) {
                $identity[$i][$j] = ($i === $j) ? 1 : 0;
            }
        }
        return $identity;
    }
}
