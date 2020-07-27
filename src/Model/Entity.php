<?php
/**
 * Created by PhpStorm.
 * User: cbonte
 * Date: 2019-05-19
 * Time: 3:30 PM.
 */

namespace CodePrimer\Model;

use CodePrimer\Helper\FieldHelper;

class Entity
{
    /** @var string */
    private $name;

    /** @var string */
    private $description;

    /** @var bool */
    private $audited = false;

    /** @var StateMachine|null */
    private $stateMachine = null;

    /** @var Field[] */
    private $fields = [];

    /** @var Constraint[] */
    private $uniqueConstraints = [];

    /**
     * DataEntity constructor.
     */
    public function __construct(string $name, string $description = '')
    {
        $this->name = $name;
        $this->description = $description;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Entity
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
     * @return Entity
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function isAudited(): bool
    {
        return $this->audited;
    }

    public function setAudited(bool $audited): Entity
    {
        $this->audited = $audited;

        return $this;
    }

    public function getStateMachine(): ?StateMachine
    {
        return $this->stateMachine;
    }

    /**
     * @param ?StateMachine $stateMachine
     *
     * @return Entity
     */
    public function setStateMachine(?StateMachine $stateMachine): self
    {
        $this->stateMachine = $stateMachine;

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
     * @return Entity
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
     * @return Entity
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
     * Retrieves the list of mandatory fields of this entity.
     *
     * @return Field[]
     */
    public function getMandatoryFields(): array
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
     * Retrieves the list of searchable fields for this entity.
     *
     * @return Field[]
     */
    public function getSearchableFields(): array
    {
        $fields = [];

        foreach ($this->fields as $field) {
            if ($field->isSearchable()) {
                $fields[] = $field;
            }
        }

        return $fields;
    }

    /**
     * Retrieves the list of managed fields for this entity.
     *
     * @return Field[]
     */
    public function getManagedFields(): array
    {
        $fields = [];

        foreach ($this->fields as $field) {
            if ($field->isManaged()) {
                $fields[] = $field;
            }
        }

        return $fields;
    }

    /**
     * Returns the list of relations this entity has with other ones.
     *
     * @return RelationshipSide[]
     */
    public function getRelations(): array
    {
        $relations = [];

        foreach ($this->fields as $field) {
            $relation = $field->getRelation();
            if (null !== $relation) {
                $relations[] = $relation;
            }
        }

        return $relations;
    }

    public function getIdentifier(): ?Field
    {
        $fieldHelper = new FieldHelper();

        foreach ($this->fields as $field) {
            if ($field->isManaged() && $field->isMandatory() && $fieldHelper->isIdentifier($field)) {
                return $field;
            }
        }

        return null;
    }

    /**
     * @return Constraint[]
     */
    public function getUniqueConstraints(): array
    {
        return $this->uniqueConstraints;
    }

    /**
     * @param Constraint[] $uniqueConstraints
     *
     * @return Entity
     */
    public function setUniqueConstraints(array $uniqueConstraints): self
    {
        $this->uniqueConstraints = [];

        foreach ($uniqueConstraints as $constraint) {
            $this->addUniqueConstraint($constraint);
        }

        return $this;
    }

    /**
     * @return Entity
     */
    public function addUniqueConstraint(Constraint $constraint): self
    {
        $this->uniqueConstraints[] = $constraint;

        return $this;
    }
}
