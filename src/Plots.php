<?php

interface Plot {}
interface Scatter {};

class Google_Scatter implements Plot,Scatter {
    public string|false $data;
    public function __construct(
        _Nx2_NumericMatrix $data
    ){
        $this->data = json_encode($data->value);
    }
}





