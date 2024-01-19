<?php

interface Plot {}
interface Scatter {};
interface Pie {};
interface Geo {};
interface Bar {};

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
class Google_BarChart implements Plot,Bar {
    public string|false $data;
    public function __construct(
        _CategorySetNumericVectorFrame_Transposed $data
    ){
        $this->data = json_encode($data->value);
    }
}
class Google_LineChart implements Plot,Bar {
    public string|false $data;
    public function __construct(
        _DateSeriesSetNumericVectorFrame_Transposed $data
    ){
        $this->data = json_encode($data->value);
    }
}

class Google_TimeLine implements Plot,Bar {
    public string|false $data;
    public function __construct(
        _CategorySetDateRangeFrame_Transposed $data
    ){
        $this->data = json_encode($data->value);
    }
}





