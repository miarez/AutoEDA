<?php

interface Plot {}
interface Scatter {};
interface Pie {};

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





