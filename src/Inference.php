<?php

class Inference {

    public function __construct()
    {
        require_once 'Types.php';
        $this->types = [];
        foreach (get_declared_classes() as $className) {
            if (in_array('IType', class_implements($className))) {
                if($className === "Unknown") continue;
                $this->types[] = $className;
            }
        }
    }

    public function get_best_match(
        $value,
        $debug = false
    ) : IType
    {
        $match = Unknown::set($value);
        foreach($this->types as $type)
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
        $match = (new Inference)->get_best_match($list[0]);
        foreach ($list as $item){
            if(!$match::matches_type($item))
            {
                return false;
            }
        }
        return get_class($match);
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




