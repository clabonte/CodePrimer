<?php

namespace CodePrimer\Model\Data;

use InvalidArgumentException;

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

    public function addData(Data $data): DataBundle
    {
        if ($data instanceof InputData) {
            return parent::addData($data);
        }
        throw new InvalidArgumentException('InputDataBundle only supports InputData arguments');
    }
}
