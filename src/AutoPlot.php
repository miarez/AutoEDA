<?php

class AutoPlot {

    public array $mapping;

    public function __construct()
    {
        $this->mapping = $this->get_plot_mapping();
    }

    private function get_plot_mapping(): array {
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
                    $constructorDataTypes[$paramType][] = $plotClass;
                }
            }
        }

        return $constructorDataTypes;
    }

    public function try_plot(
        Itype $type
    ) : string
    {
        $json = "";
        $from_type = get_class($type);
        if(!isset($this->mapping[$from_type][0]))
        {
            if(isset(Transformations::$KNOWN_TRANSFORMATIONS[$from_type]))
            {
                $type_transformations = Transformations::$KNOWN_TRANSFORMATIONS[$from_type];
                foreach($type_transformations as $to_type=>$via_method)
                {
                    if(array_key_exists($to_type, $this->mapping))
                    {
                        $data = Transformations::$via_method($type->value);
                        pp("## APPLIED TRANSFORMATION ($from_type)->[$via_method]->($to_type)");
                        $type = (new Inference())->get_best_match($data);
                        return $this->try_plot($type);
                    }
                }
                return $json;
            }
            return $json;
        }
        $available_plot = $this->mapping[get_class($type)][0];
        return (new $available_plot($type))->data;
    }

}