<?php

namespace CodePrimer\Helper;

use CodePrimer\Model\Data\Data;
use Doctrine\Inflector\InflectorFactory;
use RuntimeException;

class DataNameHelper
{
    private $inflector;
    /** @var Data[] Data names assigned/attempted so far */
    private $names = [];
    /** @var string[] Name of the bundle associated with a given assigned/attempted data name */
    private $bundleNames = [];

    public function __construct()
    {
        $this->inflector = InflectorFactory::create()->build();
    }

    public function getData(string $name): ?Data
    {
        return $this->names[$name];
    }

    public function getBundleName(string $name): ?string
    {
        return $this->bundleNames[$name];
    }

    public function assignDataName(string $bundleName, Data $data): array
    {
        $conflicts = [];

        $modelName = $data->getBusinessModel()->getName();
        $field = $data->getField();
        $name = $field->getName();
        if (!isset($this->names[$name])) {
            // This field's name has not been assigned yet, reserve it
            $data->setName($name);
            $this->names[$name] = $data;
            $this->bundleNames[$name] = $bundleName;
        } else {
            // This field's name has already been assigned.
            $conflicts[] = $name;

            // Pick a unique name for this data based on the model to which it belongs.
            $newName = $this->inflector->camelize($modelName.'_'.$name);
            if (isset($this->names[$newName])) {
                // Already assigned, try with the bundle + model + field combination
                $conflicts[] = $newName;
                $newName = $this->inflector->camelize($bundleName.'_'.$modelName.'_'.$name);
                if (isset($this->names[$newName])) {
                    throw new RuntimeException("Failed to assign a unique name to data for Bundle: $bundleName, Model: $modelName, Field: $name. Please consider changing the bundle name");
                }
            }
            // Reserve this name
            $data->setName($newName);
            $this->names[$newName] = $data;
            $this->bundleNames[$newName] = $bundleName;
        }

        return $conflicts;
    }
}
