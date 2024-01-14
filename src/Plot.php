<?php

interface Plot {}

class Scatter implements Plot {
    private array $data;
    public function __construct(
        _BiVariateNumericMatrix $data
    ){
        $this->data    = $data->value;
    }
    public function for_google_charts() : string
    {
        return json_encode(MatrixTranspose::transpose($this->data));
    }
}





function getPlotConstructorDataTypes(): array {
    $allClasses = get_declared_classes();
    $plotClasses = array_filter($allClasses, function($className) {
        $reflection = new ReflectionClass($className);
        return in_array(Plot::class, $reflection->getInterfaceNames());
    });

    $constructorDataTypes = [];

    foreach ($plotClasses as $plotClass) {
        $reflection = new ReflectionClass($plotClass);
        $constructor = $reflection->getConstructor();

        if ($constructor) {
            $parameters = $constructor->getParameters();
            if (!empty($parameters)) {
                $paramType = (string) $parameters[0]->getType();
                $constructorDataTypes[$plotClass] = $paramType;
            }
        }
    }

    return $constructorDataTypes;
}
