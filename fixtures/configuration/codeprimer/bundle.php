<?php

use CodePrimer\Model\BusinessBundle;


if (!defined('prepareBundle')) {
    require 'DatasetFactory.php';
    require 'BusinessModelFactory.php';
    require 'BusinessProcessFactory.php';

    function prepareBundle(string $name = 'MyProject', string $namespace = 'com.MyProject', string $description = '')
    {
        // Step 1 - Create your BusinessBundle
        $bundle = new BusinessBundle($namespace, $name);
        $bundle->setDescription($description);

        // Step 2 - Add your Datasets to your Bundle by calling all the 'create' methods without parameters defined in the factory
        $datasetFactory = new DatasetFactory();

        $factory = new ReflectionClass($datasetFactory);
        $methods = $factory->getMethods(ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $method) {
            if ((0 === strpos($method->getName(), 'create')) && (0 == $method->getNumberOfParameters())) {
                $bundle->addDataset($method->invoke($datasetFactory));
            }
        }

        // Step 3 - Add your Business Data Model to your Bundle by calling all the 'create' methods without parameters defined in the factory
        $modelFactory = new BusinessModelFactory($bundle);

        $factory = new ReflectionClass($modelFactory);
        $methods = $factory->getMethods(ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $method) {
            if ((0 === strpos($method->getName(), 'create')) && (0 == $method->getNumberOfParameters())) {
                $bundle->addBusinessModel($method->invoke($modelFactory));
            }
        }

        // Step 4 - Add your Business Process Model to your Bundle by calling all the 'create' methods without parameters defined in the factory
        $processFactory = new BusinessProcessFactory($bundle);

        $factory = new ReflectionClass($processFactory);
        $methods = $factory->getMethods(ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $method) {
            if ((0 === strpos($method->getName(), 'create')) && (0 == $method->getNumberOfParameters())) {
                $bundle->addBusinessProcess($method->invoke($processFactory));
            }
        }

        return $bundle;
    }
}
