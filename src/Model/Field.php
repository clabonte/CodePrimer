<?php

namespace CodePrimer\Model;

class Field
{
    /** @var string */
    private $name;

    /** @var string */
    private $type;

    /** @var string */
    private $description = '';

    /** @var string|null */
    private $default = null;

    /** @var string|null */
    private $example = null;

    /** @var bool */
    private $mandatory = false;

    /** @var bool */
    private $searchable = false;

    /** @var bool Whether this field must be automatically managed by the solution */
    private $managed = false;

    /** @var bool Whether this field is a list of */
    private $list = false;

    /** @var bool Whether is fields has been generated/added by CodePrimer for internal use (e.g. DB id) */
    private $generated = false;

    /** @var RelationshipSide|null Whether this field is related to another entity */
    private $relation = null;

    /**
     * Field constructor.
     */
    public function __construct(string $name, string $type, string $description = '', bool $mandatory = false, ?string $default = null, ?string $example = null)
    {
        $this->name = $name;
        $this->type = $type;
        $this->description = $description;
        $this->mandatory = $mandatory;
        $this->default = $default;
        $this->example = $example;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Field
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return Field
     */
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return Field
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDefault(): ?string
    {
        return $this->default;
    }

    /**
     * @return Field
     */
    public function setDefault(?string $default): self
    {
        $this->default = $default;

        return $this;
    }

    public function getExample(): ?string
    {
        return $this->example;
    }

    /**
     * @return Field
     */
    public function setExample(?string $example): self
    {
        $this->example = $example;

        return $this;
    }

    public function isMandatory(): bool
    {
        return $this->mandatory;
    }

    /**
     * @return Field
     */
    public function setMandatory(bool $mandatory): self
    {
        $this->mandatory = $mandatory;

        return $this;
    }

    public function isSearchable(): bool
    {
        return $this->searchable;
    }

    /**
     * @return Field
     */
    public function setSearchable(bool $searchable): self
    {
        $this->searchable = $searchable;

        return $this;
    }

    public function isManaged(): bool
    {
        return $this->managed;
    }

    /**
     * @return Field
     */
    public function setManaged(bool $managed): self
    {
        $this->managed = $managed;

        return $this;
    }

    public function isList(): bool
    {
        return $this->list;
    }

    /**
     * @return Field
     */
    public function setList(bool $list): self
    {
        $this->list = $list;

        return $this;
    }

    public function isGenerated(): bool
    {
        return $this->generated;
    }

    public function setGenerated(bool $generated): Field
    {
        $this->generated = $generated;

        return $this;
    }

    public function getRelation(): ?RelationshipSide
    {
        return $this->relation;
    }

    public function setRelation(?RelationshipSide $relation): Field
    {
        $this->relation = $relation;

        return $this;
    }
}
