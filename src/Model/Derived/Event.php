<?php
/**
 * Created by PhpStorm.
 * User: cbonte
 * Date: 2019-05-19
 * Time: 3:31 PM.
 */

namespace CodePrimer\Model\Derived;

use CodePrimer\Model\BusinessModel;
use CodePrimer\Model\Field;

class Event
{
    /** @var string */
    private $name;

    /** @var string */
    private $description;

    /** @var string */
    private $code;

    /** @var BusinessModel|null The entity to which this event is associated */
    private $businessModel;

    /** @var Field[] */
    private $fields = [];

    /**
     * Event constructor.
     */
    public function __construct(string $name, string $code, string $description = '', ?BusinessModel $businessModel = null)
    {
        $this->name = $name;
        $this->description = $description;
        $this->code = $code;
        $this->businessModel = $businessModel;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Event
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return Event
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return Event
     */
    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return BusinessModel\null
     */
    public function getBusinessModel(): ?BusinessModel
    {
        return $this->businessModel;
    }

    /**
     * @return Event
     */
    public function setBusinessModel(BusinessModel $businessModel): self
    {
        $this->businessModel = $businessModel;

        return $this;
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
     *
     * @return Event
     */
    public function setFields(array $fields): self
    {
        $this->fields = [];
        foreach ($fields as $field) {
            $this->addField($field);
        }

        return $this;
    }

    /**
     * @return Event
     */
    public function addField(Field $field): self
    {
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
     * Retrieves the list of mandatory fields of this event.
     *
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
}
