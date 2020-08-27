<?php
/**
 * Created by PhpStorm.
 * User: cbonte
 * Date: 2019-05-19
 * Time: 3:33 PM.
 */

namespace CodePrimer\Model;

use CodePrimer\Helper\FieldHelper;
use InvalidArgumentException;
use LogicException;

class DataSet
{
    /** @var string */
    private $name;

    /** @var string */
    private $description;

    /** @var Field[] */
    private $fields = [];

    /** @var Field|null */
    private $identifier = null;

    /** @var DataSetElement[] */
    private $elements = [];

    /**
     * Set constructor.
     */
    public function __construct(string $name, string $description = '')
    {
        $this->name = $name;
        $this->description = $description;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @codeCoverageIgnore
     *
     * @return Field[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @param Field[] $fields
     */
    public function setFields(array $fields): DataSet
    {
        $this->fields = [];
        foreach ($fields as $field) {
            $this->addField($field);
        }

        return $this;
    }

    public function addField(Field $field): DataSet
    {
        // Make sure this is a valid type. DataSet only supports native types (at this time)
        $helper = new FieldHelper();
        if (!$helper->isNativeType($field)) {
            throw new InvalidArgumentException($field->getName().' has an unsupported field type: '.$field->getType().'. DataSet only support native fields right now.');
        } elseif ($field->isList()) {
            throw new InvalidArgumentException($field->getName().' has an unsupported field type: DataSet does not support list fields.');
        }
        if ($field->isIdentifier()) {
            if (null !== $this->identifier) {
                throw new InvalidArgumentException('There is already an identifier field defined for DataSet '.$this->name.': '.$this->identifier->getName().'. A DataSet cannot have more than 1 identifier field');
            }
            $this->identifier = $field;
        }
        // Each field in a Dataset must be mandatory
        $field->setMandatory(true);
        $this->fields[$field->getName()] = $field;

        return $this;
    }

    /**
     * @param $name
     */
    public function getField($name): ?Field
    {
        if (isset($this->fields[$name])) {
            return $this->fields[$name];
        }

        return null;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getIdentifier(): ?Field
    {
        return $this->identifier;
    }

    /**
     * @codeCoverageIgnore
     *
     * @return DataSetElement[]
     */
    public function getElements(): array
    {
        return $this->elements;
    }

    /**
     * @param DataSetElement[] $elements
     */
    public function setElements(array $elements): DataSet
    {
        $this->elements = [];
        foreach ($elements as $element) {
            $this->addElement($element);
        }

        return $this;
    }

    public function addElement(DataSetElement $element): DataSet
    {
        // Make sure we have an ID field defined for this dataset
        if (null === $this->identifier) {
            throw new LogicException('You must define an Identifier field for DataSet '.$this->name.' before adding elements to it.');
        }
        // Make sure this element has a unique value for its Identifier field
        $elementId = $element->getValue($this->identifier->getName());
        if (null === $elementId) {
            throw new InvalidArgumentException("Invalid element for DataSet {$this->name}. It is missing a value for Identifier '{$this->identifier->getName()}' field.");
        } elseif (isset($this->elements[$elementId])) {
            throw new InvalidArgumentException("Element '$elementId' has already been added to this DataSet. Make sure to assign a unique '{$this->identifier->getName()}' value to each element of your DataSet.");
        }

        // Make sure the element has all the right fields
        $values = $element->getValues();

        // Assume that all fields are missing until proven otherwise
        $missingFields = $this->fields;
        $invalidValues = [];
        $unknownFields = [];
        $helper = new FieldHelper();
        foreach ($values as $name => $value) {
            if (isset($missingFields[$name])) {
                $field = $missingFields[$name];
                unset($missingFields[$name]);
                // Make sure the value is compatible with the field type
                if (!$helper->isValueCompatible($field, $value)) {
                    $invalidValues[] = "$name ($value is not a valid {$field->getType()})";
                }
            } else {
                $unknownFields[] = $name;
            }
        }
        if (!empty($missingFields)) {
            $msg = 'Invalid element for DataSet '.$this->name.'. Missing Fields: '.implode(',', array_keys($missingFields));
            if (!empty($unknownFields)) {
                $msg .= '. Unknown Fields: '.implode(',', $unknownFields);
            }
            throw new InvalidArgumentException($msg);
        } elseif (!empty($unknownFields)) {
            $msg = 'Invalid element for DataSet '.$this->name.'. Unknown Fields: '.implode(',', $unknownFields);
            throw new InvalidArgumentException($msg);
        } elseif (!empty($invalidValues)) {
            $msg = 'Invalid element for DataSet '.$this->name.'. The following values are not compatible with their associated field: ';
            $msg .= implode(',', $invalidValues);
            throw new InvalidArgumentException($msg);
        }
        $element->setDataset($this);
        $this->elements[$elementId] = $element;

        return $this;
    }

    /**
     * Checks if a field contains a list of unique values across all the elements associated with a dataset.
     */
    public function isUniqueField(string $name): bool
    {
        if (!isset($this->fields[$name])) {
            return false;
        }

        $values = [];
        foreach ($this->elements as $element) {
            $value = $element->getValue($name);
            if (in_array($value, $values)) {
                return false;
            }
            $values[] = $value;
        }

        return true;
    }
}
