<?php

namespace CodePrimer\Model\Data;

/**
 * Class InputDataBundle
 * This class carries a set of InputData only.
 */
class InputDataBundle extends DataBundle
{
    public function __construct(string $name = '', string $description = '')
    {
        parent::__construct($name, $description);
    }

    public function add(InputData $data): DataBundle
    {
        return parent::addData($data);
    }
}
