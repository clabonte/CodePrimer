<?php
/**
 * Created by PhpStorm.
 * User: cbonte
 * Date: 2019-05-19
 * Time: 3:30 PM.
 */

namespace CodePrimer\Model;

use CodePrimer\Helper\FieldType;

class BusinessModel
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

    /** @var Field|null */
    private $identifier = null;

    /** @var bool */
    private $initIdentifierManaged;

    /** @var bool */
    private $initIdentifierMandatory;

    /** @var bool */
    private $initIdentifierGenerated;

    /** @var Constraint[] */
    private $uniqueConstraints = [];

    /**
     * BusinessModel constructor.
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
     * @return BusinessModel
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
     * @return BusinessModel
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

    public function setAudited(bool $audited): BusinessModel
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
     * @return BusinessModel
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
     * @return BusinessModel
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
     * @return BusinessModel
     * @throws \InvalidArgumentException
     */
    public function addField(Field $field): self
    {
        if ($field->isIdentifier()) {
            if (0 !== strcasecmp($field->getType(), FieldType::ID) && 0 !== strcasecmp($field->getType(), FieldType::UUID)) {
                throw new \InvalidArgumentException('Invalid identifier type provided: ' . $field->getType() .
                    '. Must be either FieldType::UUID or FieldType::ID');
            }

            if ($this->identifier !== null) {
                $currentIdField = $this->getField($this->identifier->getName());
                $currentIdField->setIdentifier(false);
                $currentIdField->setManaged($this->initIdentifierManaged);
                $currentIdField->setMandatory($this->initIdentifierMandatory);
                $currentIdField->setGenerated($this->initIdentifierGenerated);
                $this->identifier = null;

                $this->addField($currentIdField);
            }

            // we backup the initial setup, then override the managed and mandatory properties
            $this->initIdentifierManaged = $field->isManaged();
            $this->initIdentifierMandatory = $field->isMandatory();
            $this->initIdentifierGenerated = $field->isGenerated();
            $field->setMandatory(true);
            $field->setManaged(true);
            $field->setGenerated(true);

            $this->identifier = $field;
        }

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
        return $this->identifier;
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
     * @return BusinessModel
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
     * @return BusinessModel
     */
    public function addUniqueConstraint(Constraint $constraint): self
    {
        $this->uniqueConstraints[] = $constraint;

        return $this;
    }
}
