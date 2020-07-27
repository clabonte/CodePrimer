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

    /** @var bool Whether this field is a list of $type */
    private $list = false;

    /** @var bool Whether is fields has been generated/added by CodePrimer for internal use (e.g. DB id) */
    private $generated = false;

    /** @var RelationshipSide|null Whether this field is related to another entity  */
    private $relation = null;

    /**
     * Field constructor.
     * @param string $name
     * @param string $type
     * @param string $description
     * @param bool $mandatory
     * @param null|string $default
     * @param null|string $example
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


    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Field
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return Field
     */
    public function setType(string $type): self
    {
        $this->type = $type;

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
     * @return Field
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getDefault(): ?string
    {
        return $this->default;
    }

    /**
     * @param null|string $default
     * @return Field
     */
    public function setDefault(?string $default): self
    {
        $this->default = $default;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getExample(): ?string
    {
        return $this->example;
    }

    /**
     * @param null|string $example
     * @return Field
     */
    public function setExample(?string $example): self
    {
        $this->example = $example;

        return $this;
    }

    /**
     * @return bool
     */
    public function isMandatory(): bool
    {
        return $this->mandatory;
    }

    /**
     * @param bool $mandatory
     * @return Field
     */
    public function setMandatory(bool $mandatory): self
    {
        $this->mandatory = $mandatory;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSearchable(): bool
    {
        return $this->searchable;
    }

    /**
     * @param bool $searchable
     * @return Field
     */
    public function setSearchable(bool $searchable): self
    {
        $this->searchable = $searchable;

        return $this;
    }

    /**
     * @return bool
     */
    public function isManaged(): bool
    {
        return $this->managed;
    }

    /**
     * @param bool $managed
     * @return Field
     */
    public function setManaged(bool $managed): self
    {
        $this->managed = $managed;

        return $this;
    }

    /**
     * @return bool
     */
    public function isList(): bool
    {
        return $this->list;
    }

    /**
     * @param bool $list
     * @return Field
     */
    public function setList(bool $list): self
    {
        $this->list = $list;

        return $this;
    }

    /**
     * @return bool
     */
    public function isGenerated(): bool
    {
        return $this->generated;
    }

    /**
     * @param bool $generated
     * @return Field
     */
    public function setGenerated(bool $generated): Field
    {
        $this->generated = $generated;
        return $this;
    }

    /**
     * @return RelationshipSide|null
     */
    public function getRelation(): ?RelationshipSide
    {
        return $this->relation;
    }

    /**
     * @param RelationshipSide|null $relation
     * @return Field
     */
    public function setRelation(?RelationshipSide $relation): Field
    {
        $this->relation = $relation;
        return $this;
    }
}
