<?php
/**
 * Created by PhpStorm.
 * User: cbonte
 * Date: 2019-05-19
 * Time: 3:54 PM
 */

namespace CodePrimer\Model;


class Element
{
    /** @var array */
    private $values = array();

    /**
     * @return array
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * @param array $values
     */
    public function setValues(array $values): void
    {
        $this->values = $values;
    }

    /**
     * @param string $name
     * @param $value
     */
    public function addValue(string $name, $value): void
    {
        $this->values[$name] = $value;
    }

    /**
     * @param string $name
     * @return null|string
     */
    public function getValue(string $name): ?string
    {
        if (isset($this->values[$name])) {
            return $this->values[$name];
        }

        return null;
    }
}
