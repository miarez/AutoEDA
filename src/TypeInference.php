<?php

class Utils {



    public static function is_all($list, $of_type) : bool
    {
        foreach ($list as $item){
            if(!$of_type::matches_type($item))
            {
                return false;
            }
        }
        return true;
    }

    # this will probably die?
    public static function all(array $list) : bool|String
    {
        $match = self::get_best_match($list[0]);
        foreach ($list as $item){
            if(!$match::matches_type($item))
            {
                return false;
            }
        }
        return get_class($match);
    }

    public static function get_best_match(
        $value,
        $debug = false
    ) : IType
    {
        global $types;
        $match = Unknown::set($value);
        foreach($types as $type)
        {
            $result_type = $type::try_set($value);
            if(get_class($result_type) !== "Unknown")
            {
                if($debug) pp($result_type, 0, "MATCHES TYPE $type");
                $match = $result_type;
            }
        }
        return $match;
    }

    public static function is_ordered(
        array $input
    ) : bool
    {
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

    # todo replace with get dimension
    public static function is_1D(
        $array
    ) : bool
    {
        return count($array) == count($array, COUNT_RECURSIVE);
    }

    public static function all_same_length(
        array $array
    ) : bool
    {
        $length = sizeof($array[0]);
        foreach($array as $v){
            if(sizeof($v) !== $length){
                return false;
            }
        }
        return true;
    }

}




class TypeInference {

}

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


class Unknown implements IType {
    public mixed $value;
    public function __construct(
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


class _Null implements IType, IScalar, INullish {

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
    public static function try_set(
        $value
    ) : _Null | Unknown
    {
        if(self::matches_type($value))
        {
            return new _Null($value);
        }
        return new Unknown($value);
    }
}



class _Boolean implements IType, IScalar {

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
    public static function try_set(
        $value
    ) : _Boolean | Unknown
    {
        if(self::matches_type($value))
        {
            return new _Boolean($value);
        }
        return new Unknown($value);
    }
}

class _Byte implements IType, IScalar {
    
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
    public static function try_set(
        $value
    ) : _Byte | Unknown
    {
        if(self::matches_type($value))
        {
            return new _Byte($value);
        }
        return new Unknown($value);
    }
}


class _Numeric implements IType, IScalar, INumeric {

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
    public static function try_set(
        $value
    ) : _Numeric | Unknown
    {
        if(self::matches_type($value))
        {
            return new _Numeric($value);
        }
        return new Unknown($value);
    }
}


class _Nan implements IType, IScalar, INullish {

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
    public static function try_set(
        $value
    ) : _Nan | Unknown
    {
        if(self::matches_type($value))
        {
            return new _Nan($value);
        }
        return new Unknown($value);
    }
}


class _String implements IType, IScalar, IString {

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
        return is_string($value);
    }
    public static function try_set(
        $value
    ) : IType
    {
        if(self::matches_type($value))
        {
            return new _String($value);
        }

        return new Unknown($value);
    }
}


class _NA implements IType, IScalar, INullish {

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
    public static function try_set(
        $value
    ) : _NA | Unknown
    {
        if(self::matches_type($value))
        {
            $value = strtoupper($value);
            return new _NA($value);
        }
        return new Unknown($value);
    }
}


class _Date implements IType, IScalar, IDate {

    public string|int $value;

    public function __construct(
         string|int $value
    )
    {
        $this->value = $value;
    }
    public static function matches_type(
        $value
    ) : bool
    {
        // Define an array of expected date formats
        $dateFormats = ['Y-m-d', 'Y-m-d H:i:s', 'm/d/Y', 'm/d/Y H:i:s', 'Y-m-d\TH:i:s','Y-m-d\TH:i:s\Z'];

        // Check for string format dates
        if (is_string($value)) {
            foreach ($dateFormats as $format) {
                $date = DateTime::createFromFormat($format, $value);
                if ($date && $date->format($format) == $value) {
                    return true;
                }
            }
        }

        // Check for numeric epoch timestamps
        if (is_numeric($value)) {
            $inputStr = (string)$value;
            if (strlen($inputStr) === 10 && strtotime('@'.$value) !== false) {
                return true;
            }
        }
        return false;
    }
    public static function try_set(
        $value
    ) : _Date | Unknown
    {
        if(self::matches_type($value))
        {
            return new _Date($value);
        }
        return new Unknown($value);
    }
}

class _Location implements IType, IScalar, IExtensionType {

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
        $knownLocations = ['United States of America', 'USA', 'us', 'USA', 'FR', 'CA', 'US', 'CA', 'FR', 'FR','USA', 'FR', 'CA'];
        return in_array($value, $knownLocations, true);
    }
    public static function try_set(
        $value
    ) : _Location | Unknown
    {
        if(self::matches_type($value))
        {
            return new _Location($value);
        }
        return new Unknown($value);
    }
}

class _Array implements IType, IList {

    public array $value;

    public function __construct(
        array $value
    )
    {
        $this->value = $value;
    }
    public static function matches_type(
        $value
    ) : bool
    {
        return is_array($value);
    }
    public static function try_set(
        $value
    ) : _Array | Unknown
    {
        if(self::matches_type($value))
        {
            return new _Array($value);
        }
        return new Unknown($value);
    }
}

class _Vector implements IType, IList {

    public array $value;

    public function __construct(
        array $value
    )
    {
        $this->value = $value;
    }
    public static function matches_type(
        $value
    ) : bool
    {
        if (!_Array::matches_type($value)) return false;

        $element_type = get_class(Utils::get_best_match($value[0]));
        foreach ($value as $item){
            if(!$element_type::matches_type($item))
            {
                return false;
            }
        }
        return true;
    }
    public static function try_set(
        $value
    ) : _Vector | Unknown
    {
        if(self::matches_type($value))
        {
            return new _Vector($value);
        }
        return new Unknown($value);
    }
}

class _NumericVector implements IType, IList {

    public array $value;

    public function __construct(
        array $value
    )
    {
        $this->value = $value;
    }
    public static function matches_type(
        $value
    ) : bool
    {
        return _Vector::matches_type($value) && Utils::is_all($value, "_Numeric");
    }

    public static function try_set(
        $value
    ) : _NumericVector | Unknown
    {
        if(self::matches_type($value))
        {
            return new _NumericVector($value);
        }
        return new Unknown($value);
    }
}

class _BooleanVector implements IType, IList {

    public array $value;

    public function __construct(
        array $value
    )
    {
        $this->value = $value;
    }
    public static function matches_type(
        $value
    ) : bool
    {
        return _Vector::matches_type($value) && Utils::is_all($value, "_Boolean");
    }

    public static function try_set(
        $value
    ) : _BooleanVector | Unknown
    {
        if(self::matches_type($value))
        {
            return new _BooleanVector($value);
        }
        return new Unknown($value);
    }
}



class _StringVector implements IType, IList {

    public array $value;

    public function __construct(
        array $value
    )
    {
        $this->value = $value;
    }
    public static function matches_type(
        $value
    ) : bool
    {
        return _Vector::matches_type($value) && Utils::is_all($value, "_String");
    }

    public static function try_set(
        $value
    ) : _StringVector | Unknown
    {
        if(self::matches_type($value))
        {
            return new _StringVector($value);
        }
        return new Unknown($value);
    }
}


class _ByteVector implements IType, IList {

    public array $value;

    public function __construct(
        array $value
    )
    {
        $this->value = $value;
    }
    public static function matches_type(
        $value
    ) : bool
    {
        return _Vector::matches_type($value) && Utils::is_all($value, "_Byte");
    }

    public static function try_set(
        $value
    ) : _ByteVector | Unknown
    {
        if(self::matches_type($value))
        {
            return new _ByteVector($value);
        }
        return new Unknown($value);
    }
}


class _DateVector implements IType, IList {

    public array $value;

    public function __construct(
        array $value
    )
    {
        $this->value = $value;
    }
    public static function matches_type(
        $value
    ) : bool
    {
        return _Vector::matches_type($value) && Utils::is_all($value, "_Date");
    }

    public static function try_set(
        $value
    ) : _DateVector | Unknown
    {
        if(self::matches_type($value))
        {
            return new _DateVector($value);
        }
        return new Unknown($value);
    }
}


class _LocationVector implements IType, IList {

    public array $value;

    public function __construct(
        array $value
    )
    {
        $this->value = $value;
    }
    public static function matches_type(
        $value
    ) : bool
    {
        return _Vector::matches_type($value) && Utils::is_all($value, "_Location");
    }

    public static function try_set(
        $value
    ) : _LocationVector | Unknown
    {
        if(self::matches_type($value))
        {
            return new _LocationVector($value);
        }
        return new Unknown($value);
    }
}



class _Series implements IType, IList {

    public array $value;

    public function __construct(
        array $value
    )
    {
        $this->value = $value;
    }
    public static function matches_type(
        $value
    ) : bool
    {

        if(!_Vector::matches_type($value)) return false;


        # Single Value Arrays Can't be a Series by definition
        if(sizeof($value) < 2) return false;

        # A little ugly
        if(_StringVector::matches_type($value) && !_DateVector::matches_type($value)) return false;

        return Utils::is_ordered($value);
    }

    public static function try_set(
        $value
    ) : _Series | Unknown
    {
        if(self::matches_type($value))
        {
            return new _Series($value);
        }
        return new Unknown($value);
    }
}

class _DateSeries implements IType, IList {

    public array $value;

    public function __construct(
        array $value
    )
    {
        $this->value = $value;
    }
    public static function matches_type(
        $value
    ) : bool
    {
        return _Series::matches_type($value) && Utils::is_all($value, "_Date");
    }

    public static function try_set(
        $value
    ) : _DateSeries | Unknown
    {
        if(self::matches_type($value))
        {
            return new _DateSeries($value);
        }
        return new Unknown($value);
    }
}


class _Set implements IType, IList {

    public array $value;

    public function __construct(
        array $value
    )
    {
        $this->value = $value;
    }
    public static function matches_type(
        $value
    ) : bool
    {

        if(!_Vector::matches_type($value)) return false;
        $unique       = @array_unique($value); #WTF...
        $unique_count = count($unique);
        return count($value) === $unique_count;
    }

    public static function try_set(
        $value
    ) : _Set | Unknown
    {
        if(self::matches_type($value))
        {
            return new _Set($value);
        }
        return new Unknown($value);
    }
}

class _SeriesSet implements IType, IList {

    public array $value;

    public function __construct(
        array $value
    )
    {
        $this->value = $value;
    }
    public static function matches_type(
        $value
    ) : bool
    {
        return _Series::matches_type($value) && _Set::matches_type($value);
    }

    public static function try_set(
        $value
    ) : _SeriesSet | Unknown
    {
        if(self::matches_type($value))
        {
            return new _SeriesSet($value);
        }
        return new Unknown($value);
    }
}


class _CategorySet implements IType, IList {

    public array $value;

    public function __construct(
        array $value
    )
    {
        $this->value = $value;
    }
    public static function matches_type(
        $value
    ) : bool
    {
        return _Set::matches_type($value) && (Utils::is_all($value, '_String') || Utils::is_all($value, "_Date"));
    }

    public static function try_set(
        $value
    ) : _CategorySet | Unknown
    {
        if(self::matches_type($value))
        {
            return new _CategorySet($value);
        }
        return new Unknown($value);
    }
}


class _LocationSet implements IType, IList {

    public array $value;

    public function __construct(
        array $value
    )
    {
        $this->value = $value;
    }
    public static function matches_type(
        $value
    ) : bool
    {
        return _Set::matches_type($value) && Utils::is_all($value, "_Location");
    }

    public static function try_set(
        $value
    ) : _LocationSet | Unknown
    {
        if(self::matches_type($value))
        {
            return new _LocationSet($value);
        }
        return new Unknown($value);
    }
}

class _DateSet implements IType, IList {

    public array $value;

    public function __construct(
        array $value
    )
    {
        $this->value = $value;
    }
    public static function matches_type(
        $value
    ) : bool
    {
        return _Set::matches_type($value) && Utils::is_all($value, "_Date");
    }

    public static function try_set(
        $value
    ) : _DateSet | Unknown
    {
        if(self::matches_type($value))
        {
            return new _DateSet($value);
        }
        return new Unknown($value);
    }
}

class _DateSeriesSet implements IType, IList {

    public array $value;

    public function __construct(
        array $value
    )
    {
        $this->value = $value;
    }
    public static function matches_type(
        $value
    ) : bool
    {
        return _DateSet::matches_type($value) && _DateSeries::matches_type($value);
    }

    public static function try_set(
        $value
    ) : _DateSeriesSet | Unknown
    {
        if(self::matches_type($value))
        {
            return new _DateSeriesSet($value);
        }
        return new Unknown($value);
    }
}


class _Frame implements IType, ITable {

    public array $value;

    public function __construct(
        array $value
    )
    {
        $this->value = $value;
    }
    public static function matches_type(
        $value
    ) : bool
    {
        if(!_Array::matches_type($value)) return false;
        if(Utils::is_1D($value)) return false;
        if(!Utils::all_same_length($value)) return false;
        return true;
    }

    public static function try_set(
        $value
    ) : _Frame | Unknown
    {
        if(self::matches_type($value))
        {
            return new _Frame($value);
        }
        return new Unknown($value);
    }
}


class _DataFrame implements IType, ITable {

    public array $value;

    public function __construct(
        array $value
    )
    {
        $this->value = $value;
    }
    public static function matches_type(
        $value
    ) : bool
    {
        if(!_Frame::matches_type($value)) return false;

        foreach($value as $columns)
        {
            $type = Utils::all($columns);
            if(!$type) return false;
        }
        return true;
    }

    public static function try_set(
        $value
    ) : _DataFrame | Unknown
    {
        if(self::matches_type($value))
        {
            return new _DataFrame($value);
        }
        return new Unknown($value);
    }
}

class _Matrix implements IType, ITable {

    public array $value;

    public function __construct(
        array $value
    )
    {
        $this->value = $value;
    }
    public static function matches_type(
        $value
    ) : bool
    {
        if(!_DataFrame::matches_type($value)) return false;

        $types = [];
        foreach($value as $columns)
        {
            $type = Utils::all($columns);
            $types[$type] = 1;;
        }
        if(sizeof($types) > 1) return false;
        return true;
    }

    public static function try_set(
        $value
    ) : _Matrix | Unknown
    {
        if(self::matches_type($value))
        {
            return new _Matrix($value);
        }
        return new Unknown($value);
    }
}



class _DictionaryFrame implements IType, ITable {

    public array $value;

    public function __construct(
        array $value
    )
    {
        $this->value = $value;
    }
    public static function matches_type(
        $value
    ) : bool
    {
        if(!_DataFrame::matches_type($value)) return false;

        $hold = [];
        $sets = 0;
        foreach($value as $columns)
        {
            $type = Utils::all($columns);
            $sets += _Set::matches_type($columns);
            $hold[$type][] = 1;
        }

        if(sizeof($hold) !== 2) return false;

        if($sets === 0) return false;

        return true;
    }

    public static function try_set(
        $value
    ) : _DictionaryFrame | Unknown
    {
        if(self::matches_type($value))
        {
            return new _DictionaryFrame($value);
        }
        return new Unknown($value);
    }
}



class _StringVectorNumericVectorFrame implements IType, ITable {

    public array $value;

    public function __construct(
        array $value
    )
    {
        $this->value = $value;
    }
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

    public static function try_set(
        $value
    ) : _StringVectorNumericVectorFrame | Unknown
    {
        if(self::matches_type($value))
        {
            return new _StringVectorNumericVectorFrame($value);
        }
        return new Unknown($value);
    }
}


class _CategorySetNumericVectorFrame implements IType, ITable {

    public array $value;

    public function __construct(
        array $value
    )
    {
        $this->value = $value;
    }
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

    public static function try_set(
        $value
    ) : _CategorySetNumericVectorFrame | Unknown
    {
        if(self::matches_type($value))
        {
            return new _CategorySetNumericVectorFrame($value);
        }
        return new Unknown($value);
    }
}


class _DateVectorNumericVectorFrame implements IType, ITable {

    public array $value;

    public function __construct(
        array $value
    )
    {
        $this->value = $value;
    }
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

    public static function try_set(
        $value
    ) : _DateVectorNumericVectorFrame | Unknown
    {
        if(self::matches_type($value))
        {
            return new _DateVectorNumericVectorFrame($value);
        }
        return new Unknown($value);
    }
}

class _DateSetNumericVectorFrame implements IType, ITable {

    public array $value;

    public function __construct(
        array $value
    )
    {
        $this->value = $value;
    }
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

    public static function try_set(
        $value
    ) : _DateSetNumericVectorFrame | Unknown
    {
        if(self::matches_type($value))
        {
            return new _DateSetNumericVectorFrame($value);
        }
        return new Unknown($value);
    }
}



class _DateSeriesNumericVectorFrame implements IType, ITable {

    public array $value;

    public function __construct(
        array $value
    )
    {
        $this->value = $value;
    }
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

    public static function try_set(
        $value
    ) : _DateSeriesNumericVectorFrame | Unknown
    {
        if(self::matches_type($value))
        {
            return new _DateSeriesNumericVectorFrame($value);
        }
        return new Unknown($value);
    }
}

class _DateSeriesSetNumericVectorFrame implements IType, ITable {

    public array $value;

    public function __construct(
        array $value
    )
    {
        $this->value = $value;
    }
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

    public static function try_set(
        $value
    ) : _DateSeriesSetNumericVectorFrame | Unknown
    {
        if(self::matches_type($value))
        {
            return new _DateSeriesSetNumericVectorFrame($value);
        }
        return new Unknown($value);
    }
}






