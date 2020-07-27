<?php
/**
 * Created by PhpStorm.
 * User: cbonte
 * Date: 2019-05-19
 * Time: 3:31 PM
 */

namespace CodePrimer\Model;


class Event
{
    /** @var string */
    private $name;

    /** @var string */
    private $description;

    /** @var string */
    private $code;

    /** @var Entity|null The entity to which this event is associated */
    private $entity;

    /** @var Field[] */
    private $fields = array();

    /**
     * EventEntity constructor.
     * @param string $name
     * @param string $code
     * @param Entity|null $entity
     * @param string $description
     */
    public function __construct(string $name, string $code, string $description = '', ?Entity $entity = null)
    {
        $this->name = $name;
        $this->description = $description;
        $this->code = $code;
        $this->entity = $entity;
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
     * @return Event
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
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
     * @return Event
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param string $code
     * @return Event
     */
    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return Entity\null
     */
    public function getEntity(): ?Entity
    {
        return $this->entity;
    }

    /**
     * @param Entity $entity
     * @return Event
     */
    public function setEntity(Entity $entity): self
    {
        $this->entity = $entity;

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
     * @return Event
     */
    public function setFields(array $fields): self
    {
        $this->fields = array();
        foreach ($fields as $field) {
            $this->addField($field);
        }

        return $this;
    }

    /**
     * @param Field $field
     * @return Event
     */
    public function addField(Field $field): self
    {
        $this->fields[$field->getName()] = $field;

        return $this;
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
     * Retrieves the list of mandatory fields of this event
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
