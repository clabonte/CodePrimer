<?php
/**
 * Created by PhpStorm.
 * User: cbonte
 * Date: 2019-05-19
 * Time: 3:54 PM.
 */

namespace CodePrimer\Model;

class Element
{
    /** @var array */
    private $values = [];

    public function getValues(): array
    {
        return $this->values;
    }

    public function setValues(array $values): void
    {
        $this->values = $values;
    }

    /**
     * @param $value
     */
    public function addValue(string $name, $value): void
    {
        $this->values[$name] = $value;
    }

    public function getValue(string $name): ?string
    {
        if (isset($this->values[$name])) {
            return $this->values[$name];
        }

        return null;
    }
}
