<?php
/**
 * Created by PhpStorm.
 * User: cbonte
 * Date: 2019-05-19
 * Time: 3:54 PM.
 */

namespace CodePrimer\Model;

use CodePrimer\Helper\FieldType;
use InvalidArgumentException;

class DatasetElement
{
    /** @var Dataset */
    private $dataset;

    /** @var array */
    private $values;

    public function __construct(array $values = [])
    {
        $this->setValues($values);
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDataset(): ?Dataset
    {
        return $this->dataset;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setDataset(Dataset $dataset): DatasetElement
    {
        $this->dataset = $dataset;

        return $this;
    }

    /**
     * @return mixed the value assigned to the Dataset's identifier field for this element
     */
    public function getIdentifierValue()
    {
        if ((null !== $this->dataset) && (null !== $this->dataset->getIdentifier())) {
            return $this->getValue($this->dataset->getIdentifier()->getName());
        }
        throw new \LogicException('You must assign a Dataset (with an identifier) to an element before retrieving its identifier value');
    }

    public function getUniqueName(): string
    {
        if (null !== $this->dataset) {
            // If the Dataset identifier is of type String, use it as the unique name
            if (FieldType::STRING == $this->dataset->getIdentifier()->getType()) {
                return $this->getValue($this->dataset->getIdentifier()->getName());
            }
            // Otherwise, if the dataset has a 'name' field with unique values, use it...
            if (null !== $this->dataset->getField('name') && $this->dataset->isUniqueField('name')) {
                return $this->getValue('name');
            }
            // Otherwise, create a unique name based on the dataset name and the identifier's value
            return $this->dataset->getName().'_'.$this->getValue($this->dataset->getIdentifier()->getName());
        }
        throw new \LogicException('You must assign a Dataset to an element before retrieving its unique name');
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
    public function addValue(string $name, $value): DatasetElement
    {
        $this->values[$name] = $value;

        return $this;
    }

    public function getValue(string $name)
    {
        if (isset($this->values[$name])) {
            return $this->values[$name];
        }

        return null;
    }
}
