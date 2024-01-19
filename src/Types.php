<?php

interface IType {};

# Scalar Types (0-dim)
interface IScalar {}
interface INullish {}
interface INumeric {}
interface IString {}
interface IDate {}
interface IExtensionType {}

# List Types (1-dim)
interface IList {}
# Table Types (2-dim)
interface ITable {}
interface IDataFrame {}
interface IMatrix {}


class Type {

    public static function try_set(
        $value
    ) : self | Unknown
    {
        if(static::matches_type($value)) {
            return new static($value);
        }
        return new Unknown($value);
    }

}

class Unknown extends Type implements IType {
    public mixed $value;
    protected function __construct(
        $value
    )
    {
        $this->value = $value;
    }

    public static function set(
        $value
    ) : Itype
    {
        return new Unknown($value);
    }
}


class _Null extends Type implements IType, IScalar, INullish {

    # Can't declare null type in php
    public $value;

    public function __construct(
        $value = NULL
    )
    {
        $this->value = $value;
    }
    public static function matches_type(
        $value
    ) : bool
    {
        return $value === null;
    }
}



class _Boolean extends Type implements IType, IScalar {

    public bool $value;

    public function __construct(
        bool $value
    )
    {
        $this->value = $value;
    }
    public static function matches_type(
        $value
    ) : bool
    {
        return is_bool($value);
    }

}

class _Byte extends Type implements IType, IScalar {

    public string $value;

    public function __construct(
        string $value
    )
    {
        $this->value = $value;
    }
    public static function matches_type(
        $value
    ) : bool
    {
        if (!is_string($value)) {
            return false;
        }
        // Check if the string is a hexadecimal representation
        if (preg_match('/^0x[0-9A-Fa-f]{1,2}$/', $value)) {
            // Convert the hexadecimal string to an integer
            $intValue = hexdec($value);
            // Check if the integer is in the byte range
            return $intValue >= 0x00 && $intValue <= 0xFF;
        }
        return false;
    }
}


class _Numeric extends Type implements IType, IScalar, INumeric {

    public int|float $value;

    public function __construct(
        int|float $value
    )
    {
        $this->value = $value;
    }
    public static function matches_type(
        $value
    ) : bool
    {
        return is_numeric($value);
    }
}


class _Nan extends Type implements IType, IScalar, INullish {

    public $value;

    public function __construct(
        $value
    )
    {
        $this->value = $value;
    }
    public static function matches_type(
        $value
    ) : bool
    {
        try {
            return is_nan($value);
        } catch (Error $e){
            return false;
        }
    }
}


class _String extends Type implements IType, IScalar, IString {

    public string $value;

    public array $texture;

    protected function __construct(
        string $value
    )
    {
        $this->value = $value;
        $this->determine_texture();
    }
    public static function matches_type(
        $value
    ) : bool
    {
        return is_string($value);
    }

    private function determine_texture()
    {
        $this->texture["length"] = strlen($this->value);
    }

}


class _NA extends Type implements IType, IScalar, INullish {

    # String because for now we'll use `NA` to represent it
    public string $value;

    public function __construct(
        string $value
    )
    {
        $this->value = $value;
    }
    public static function matches_type(
        $value
    ) : bool
    {
        if(!is_string($value)) return false;
        return strtoupper($value) === 'NA';
    }
}


class _Date extends Type implements IType, IScalar, IString, IDate {

    public array $texture;
    public string|int $value;

    public static array $formats =['Y-m-d', 'Y-m-d H:i:s', 'm/d/Y', 'm/d/Y H:i:s', 'Y-m-d\TH:i:s','Y-m-d\TH:i:s\Z'];

    public function __construct(
        string|int $value
    )
    {
        $this->value = $value;
        $this->determine_texture();
    }
    public static function matches_type(
        $value
    ) : bool
    {
        if(!self::check_format($value)) return false;
        return true;
    }

    private static function check_format(
        $value
    ) : string|false
    {
        if (is_string($value)) {
            foreach (self::$formats as $format) {
                $date = DateTime::createFromFormat($format, $value);
                if ($date && $date->format($format) == $value) {
                    return $format;
                }
            }
        }

        // Check for numeric epoch timestamps
        if (is_numeric($value)) {
            $inputStr = (string)$value;
            if (strlen($inputStr) === 10 && strtotime('@'.$value) !== false) {
                return "epoch";
            }
        }
        return false;
    }
    private function determine_texture() : void
    {
        $this->texture["format"] = self::check_format($this->value);
    }
}

class _Location extends Type implements IType, IScalar, IString, IExtensionType {

    public string $value;

    public function __construct(
        string $value
    )
    {
        $this->value = $value;
    }
    public static function matches_type(
        $value
    ) : bool
    {
        // Basic check - consider integrating a location detection library for robustness
        if (!is_string($value)) {
            return false;
        }
        // TODO:
        $knownLocations = ['United States of America', 'USA', 'us', 'USA', 'FR', 'CA', 'US', 'CA', 'FR', 'FR','USA', 'FR', 'CA', "US", "CA", "FR", "GB", "IT"];
        return in_array($value, $knownLocations, true);
    }
}

class _Array extends Type implements IType, IList {

    public array $texture;
    public array $value;

    protected function __construct(
        array $value
    )
    {
        $this->value = $value;
        $this->determine_texture();
    }
    public static function matches_type(
        $value
    ) : bool
    {
        return is_array($value);
    }
    private function determine_texture() : void
    {
        $this->texture = [
            "length" => sizeof($this->value),
        ];
    }
}

class _Vector extends _Array implements IType, IList {

    public static function matches_type(
        $value
    ) : bool
    {
        if (!_Array::matches_type($value)) return false;
        if(empty($value)) return false;

        $element_type = get_class((new Inference)->get_best_match($value[0]));
        foreach ($value as $item){
            if(!$element_type::matches_type($item))
            {
                return false;
            }
        }
        return true;
    }

}

class _NumericVector extends _Array implements IType, IList {

    public static function matches_type(
        $value
    ) : bool
    {
        return _Vector::matches_type($value) && Inference::is_all($value, "_Numeric");
    }

}

class _BooleanVector extends _Array implements IType, IList {

    public static function matches_type(
        $value
    ) : bool
    {
        return _Vector::matches_type($value) && Inference::is_all($value, "_Boolean");
    }

}



class _StringVector extends _Array implements IType, IList {

    public static function matches_type(
        $value
    ) : bool
    {
        return _Vector::matches_type($value) && Inference::is_all($value, "_String");
    }

}

class _ByteVector extends _Array implements IType, IList {

    public static function matches_type(
        $value
    ) : bool
    {
        return _Vector::matches_type($value) && Inference::is_all($value, "_Byte");
    }

}


class _DateVector extends _Array implements IType, IList {

    public static function matches_type(
        $value
    ) : bool
    {
        return _Vector::matches_type($value) && Inference::is_all($value, "_Date");
    }

}


class _LocationVector extends _Array implements IType, IList {

    public static function matches_type(
        $value
    ) : bool
    {
        return _Vector::matches_type($value) && Inference::is_all($value, "_Location");
    }

}



class _Series extends _Array implements IType, IList {

    public static function matches_type(
        $value
    ) : bool
    {

        if(!_Vector::matches_type($value)) return false;


        # Single Value Arrays Can't be a Series by definition
        if(sizeof($value) < 2) return false;

        # A little ugly
        if(_StringVector::matches_type($value) && !_DateVector::matches_type($value)) return false;

        return Inference::is_ordered($value);
    }
}

class _DateSeries extends _Array implements IType, IList {

    public static function matches_type(
        $value
    ) : bool
    {
        return _Series::matches_type($value) && Inference::is_all($value, "_Date");
    }

}


class _Set extends _Array implements IType, IList {

    public static function matches_type(
        $value
    ) : bool
    {

        if(!_Vector::matches_type($value)) return false;
        $unique       = @array_unique($value); #WTF...
        $unique_count = count($unique);
        return count($value) === $unique_count;
    }

}

class _SeriesSet extends _Array implements IType, IList {

    public static function matches_type(
        $value
    ) : bool
    {
        return _Series::matches_type($value) && _Set::matches_type($value);
    }

}


# Technically StringSet
class _CategorySet extends _Array implements IType, IList {

    public static function matches_type(
        $value
    ) : bool
    {
        return _Set::matches_type($value) && (Inference::is_all($value, '_String') || Inference::is_all($value, "_Date"));
    }
}


class _LocationSet extends _Array implements IType, IList {

    public static function matches_type(
        $value
    ) : bool
    {
        return _Set::matches_type($value) && Inference::is_all($value, "_Location");
    }
}

class _DateSet extends _Array implements IType, IList {

    public static function matches_type(
        $value
    ) : bool
    {
        return _Set::matches_type($value) && Inference::is_all($value, "_Date");
    }
}

class _DateSeriesSet extends _Array implements IType, IList {

    public static function matches_type(
        $value
    ) : bool
    {
        return _DateSet::matches_type($value) && _DateSeries::matches_type($value);
    }
}


class _Frame extends Type implements IType, ITable {

    public array $value;
    public array $texture;

    protected function __construct(
        array $value
    )
    {
        $this->value = $value;
        $this->determine_texture();
    }

    public static function matches_type(
        $value
    ) : bool
    {
        if(!_Array::matches_type($value)) return false;
        if(Inference::is_1D($value)) return false;
        if(!Inference::all_same_length($value)) return false;
        return true;
    }
    private function determine_texture() : void
    {
        $inference = new Inference();
        $column_details = [];
        foreach($this->value as $column)
        {
            $type       = $inference->get_best_match($column);
            $row_count  = $type->texture['length'];
            $column_details[] = [
                "type"      => get_class($type),
                "texture"   => $type->texture,
            ];
        }
        $this->texture = [
            "columns"       => sizeof($this->value),
            "rows"          => $row_count,
            "column_details" => $column_details,
        ];
    }
}


class _DataFrame extends _Frame implements IType, ITable, IDataFrame {

    public static function matches_type(
        $value
    ) : bool
    {
        if(!_Frame::matches_type($value)) return false;

        foreach($value as $columns)
        {
            $type = Inference::all($columns);
            if(!$type) return false;
        }
        return true;
    }
}

class _Matrix extends _Frame implements IType, ITable, IMatrix {

    public static function matches_type(
        $value
    ) : bool
    {
        if(!_DataFrame::matches_type($value)) return false;

        $types = [];
        foreach($value as $columns)
        {
            $type = Inference::all($columns);
            $types[$type] = 1;;
        }
        if(sizeof($types) > 1) return false;
        return true;
    }
}


class _2xN_NumericMatrix extends _Frame implements IType, ITable, IMatrix {

    public static function matches_type(
        $value
    ) : bool
    {
        if(!_DataFrame::matches_type($value)) return false;

        if(sizeof($value) !== 2) return false;
        foreach($value as $columns)
        {
            if(!_NumericVector::matches_type($columns)){
                return false;
            }
        }
        return true;
    }
}

class _Nx2_NumericMatrix extends _Frame implements IType, ITable, IMatrix {

    public static function matches_type(
        $value
    ) : bool
    {
        if(!_DataFrame::matches_type($value)) return false;
        foreach($value as $columns)
        {
            if(sizeof($columns) !== 2) return false;
            if(!_NumericVector::matches_type($columns)){
                return false;
            }
        }

        return true;
    }
}

class _Nx2_CategoryNumericDictionaryFrame extends _Frame implements IType, ITable, IMatrix {

    public static function matches_type(
        $value
    ) : bool
    {
        if(!_Frame::matches_type($value)) return false;
        # potential infinite loop
        if(_2xN_CategoryNumericDictionaryFrame::matches_type(Transformations::transpose($value))){
            return true;
        }
        return false;
    }
}

class _Nx2_LocationCategoryNumericDictionaryFrame extends _Frame implements IType, ITable, IMatrix {


    public static function matches_type(
        $value
    ) : bool
    {
        if(!_Frame::matches_type($value)) return false;
        # potential infinite loop
        if(_2xN_LocationCategoryNumericDictionaryFrame::matches_type(Transformations::transpose($value))){
            return true;
        }
        return false;
    }
}


class _DictionaryFrame extends _Frame implements IType, ITable {

    public static function matches_type(
        $value
    ) : bool
    {
        if(!_DataFrame::matches_type($value)) return false;
        if(sizeof($value) !== 2) return false;
        $hold = [];
        $sets = 0;
        foreach($value as $columns)
        {
            $type = Inference::all($columns);
            $sets += _Set::matches_type($columns);
            $hold[$type][] = 1;
        }

        if(sizeof($hold) !== 2) return false;
        if($sets === 0) return false;

        return true;
    }
}



class _StringVectorNumericVectorFrame extends _Frame implements IType, ITable {

    public static function matches_type(
        $value
    ) : bool
    {
        if(!_DataFrame::matches_type($value)) return false;

        $has_category = $has_numeric = $has_boolean = 0;
        foreach($value as $columns)
        {
            $has_category += _StringVector::matches_type($columns);
            $has_numeric += _NumericVector::matches_type($columns);
            $has_boolean += _BooleanVector::matches_type($columns);
        }

        if($has_category !== 1) return false;
        if($has_numeric < 1) return false;
        if($has_boolean > 0) return false;

        return true;
    }
}


class _CategorySetNumericVectorFrame extends _Frame implements IType, ITable {

    public static function matches_type(
        $value
    ) : bool
    {
        if(!_DataFrame::matches_type($value)) return false;

        $has_category = $has_numeric = $has_boolean = 0;
        foreach($value as $columns)
        {
            $has_category += _CategorySet::matches_type($columns);
            $has_numeric += _NumericVector::matches_type($columns);
            $has_boolean += _BooleanVector::matches_type($columns);
        }

        if($has_category !== 1) return false;
        if($has_numeric < 1) return false;
        if($has_boolean > 0) return false;

        return true;
    }
}


class _CategorySetNumericVectorFrame_Transposed extends _Frame implements IType, ITable {

    public static function matches_type(
        $value
    ) : bool
    {
        if(!_Frame::matches_type($value)) return false;
        if(_CategorySetNumericVectorFrame::matches_type(Transformations::transpose($value))) {
            return true;
        }
        return false;
    }
}

class _DateSeriesSetNumericVectorFrame_Transposed extends _Frame implements IType, ITable {

    public static function matches_type(
        $value
    ) : bool
    {
        if(!_Frame::matches_type($value)) return false;
        if(_DateSeriesSetNumericVectorFrame::matches_type(Transformations::transpose($value))) {
            return true;
        }
        return false;
    }
}

class _2xN_CategoryNumericDictionaryFrame extends _Frame implements IType, ITable {

    public static function matches_type(
        $value
    ) : bool
    {
        if(!_DataFrame::matches_type($value)) return false;

        $has_category = $has_numeric = 0;
        foreach($value as $columns)
        {
            $has_category += _CategorySet::matches_type($columns);
            $has_numeric += _NumericVector::matches_type($columns);
        }

        if($has_category !== 1) return false;
        if($has_numeric !== 1) return false;

        return true;
    }
}


class _2xN_LocationCategoryNumericDictionaryFrame extends _Frame implements IType, ITable {

    public static function matches_type(
        $value
    ) : bool
    {
        if(!_DataFrame::matches_type($value)) return false;

        $has_location_category = $has_numeric = 0;
        foreach($value as $columns)
        {
            $has_location_category += _LocationVector::matches_type($columns);
            $has_numeric += _NumericVector::matches_type($columns);
        }

        if($has_location_category !== 1) return false;
        if($has_numeric !== 1) return false;

        return true;
    }
}


class _DateVectorNumericVectorFrame extends _Frame implements IType, ITable {

    public static function matches_type(
        $value
    ) : bool
    {
        if(!_DataFrame::matches_type($value)) return false;

        $has_category = $has_numeric = 0;
        foreach($value as $columns)
        {
            $has_category += _DateVector::matches_type($columns);
            $has_numeric += _NumericVector::matches_type($columns);
        }

        if($has_category !== 1) return false;
        if($has_numeric < 1) return false;

        return true;
    }
}

class _DateSetNumericVectorFrame extends _Frame implements IType, ITable {

    public static function matches_type(
        $value
    ) : bool
    {
        if(!_DataFrame::matches_type($value)) return false;

        $has_category = $has_numeric = 0;
        foreach($value as $columns)
        {
            $has_category += _DateSet::matches_type($columns);
            $has_numeric += _NumericVector::matches_type($columns);
        }

        if($has_category !== 1) return false;
        if($has_numeric < 1) return false;

        return true;
    }
}



class _DateSeriesNumericVectorFrame extends _Frame implements IType, ITable {

    public static function matches_type(
        $value
    ) : bool
    {
        if(!_DataFrame::matches_type($value)) return false;

        $has_category = $has_numeric = 0;
        foreach($value as $columns)
        {
            $has_category += _DateSeries::matches_type($columns);
            $has_numeric += _NumericVector::matches_type($columns);
        }

        if($has_category !== 1) return false;
        if($has_numeric < 1) return false;

        return true;
    }
}

class _DateSeriesSetNumericVectorFrame extends _Frame implements IType, ITable {

    public static function matches_type(
        $value
    ) : bool
    {
        if(!_DataFrame::matches_type($value)) return false;

        $has_category = $has_numeric = 0;
        foreach($value as $columns)
        {
            $has_category += _DateSeriesSet::matches_type($columns);
            $has_numeric += _NumericVector::matches_type($columns);
        }


        if($has_category !== 1) return false;
        if($has_numeric < 1) return false;

        return true;
    }
}


class _CategorySetDateRangeFrame extends _Frame implements IType, ITable {

    public static function matches_type(
        $value
    ) : bool
    {
        if(!_DataFrame::matches_type($value)) return false;
        if(sizeof($value) !== 3) return false;

        $inference = new Inference();

        $has_category_set = $has_date_vector = 0;
        foreach($value as $columns)
        {
            $has_category_set += get_class($inference->get_best_match($columns)) == "_CategorySet";
            $has_date_vector += _DateVector::matches_type($columns);
        }
        if($has_category_set !== 1) return false;
        if($has_date_vector !== 2) return false;
        return true;
    }
}


class _CategorySetDateRangeFrame_Transposed extends _Frame implements IType, ITable {

    public static function matches_type(
        $value
    ) : bool
    {
        if(!_DataFrame::matches_type($value)) return false;
        if(!_CategorySetDateRangeFrame::matches_type(Transformations::transpose($value))){
            return false;
        }
        return true;
    }
}