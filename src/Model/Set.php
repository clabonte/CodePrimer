<?php
/**
 * Created by PhpStorm.
 * User: cbonte
 * Date: 2019-05-19
 * Time: 3:33 PM
 */

namespace CodePrimer\Model;


class Set
{
    /** @var string */
    private $name;

    /** @var string */
    private $description;

    /** @var Field[] */
    private $fields = array();

    /** @var Element[]  */
    private $elements = array();

    /**
     * Set constructor.
     * @param string $name
     * @param string $description
     */
    public function __construct(string $name, string $description = '')
    {
        $this->name = $name;
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return Field[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @param Field[] $fields
     */
    public function setFields(array $fields)
    {
        $this->fields = array();
        foreach ($fields as $field) {
            $this->addField($field);
        }
    }

    /**
     * @param Field $field
     */
    public function addField(Field $field): void
    {
        $this->fields[$field->getName()] = $field;
    }

    /**
     * @param $name
     * @return Field|null
     */
    public function getField($name): ?Field
    {
        if (isset($this->fields[$name])) {
            return $this->fields[$name];
        }

        return null;
    }

    /**
     * Retrieves the list of mandatory fields of this set
     * @return array
     */
    public function listMandatoryFields()
    {
        $fields = [];

        foreach ($this->fields as $field) {
            if ($field->isMandatory()) {
                $fields[] = $field;
            }
        }

        return $fields;
    }

    /**
     * @return Element[]
     */
    public function getElements(): array
    {
        return $this->elements;
    }

    /**
     * @param Element[] $elements
     */
    public function setElements(array $elements): void
    {
        $this->elements = $elements;
    }

    /**
     * @param Element $element
     */
    public function addElement(Element $element): void
    {
        $this->elements[] = $element;
    }

}
