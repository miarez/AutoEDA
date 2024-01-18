<?php

interface Plot {}
interface Scatter {};
interface Pie {};
interface Geo {};

class Google_Scatter implements Plot,Scatter {
    public string|false $data;
    public function __construct(
        _Nx2_NumericMatrix $data
    ){
        $this->data = json_encode($data->value);
    }
}

class Google_Pie implements Plot,Pie {
    public string|false $data;
    public function __construct(
        _Nx2_CategoryNumericDictionaryFrame $data
    ){
        $this->data = json_encode($data->value);
    }
}
class Google_GeoPlot implements Plot,Geo {
    public string|false $data;
    public function __construct(
        _Nx2_LocationCategoryNumericDictionaryFrame $data
    ){
        $this->data = json_encode($data->value);
    }
}





