<?php
/**
 * Created by PhpStorm.
 * User: cbonte
 * Date: 2019-05-19
 * Time: 3:54 PM.
 */

namespace CodePrimer\Model;

use InvalidArgumentException;

class DataSetElement
{
    /** @var array */
    private $values;

    public function __construct(array $values = [])
    {
        $this->setValues($values);
    }

    public function setValues(array $values): void
    {
        if (!empty($values)) {
            // Make sure we are dealing with an associative array
            if (count(array_filter(array_keys($values), 'is_string')) !== count($values)) {
                throw new InvalidArgumentException("Invalid array type passed. Must be an associative array of type 'name' (string) => 'value' (mixed)");
            }
        }
        $this->values = $values;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * @param $value
     */
    public function addValue(string $name, $value): DataSetElement
    {
        $this->values[$name] = $value;

        return $this;
    }

    public function getValue(string $name): ?string
    {
        if (isset($this->values[$name])) {
            return $this->values[$name];
        }

        return null;
    }
}
