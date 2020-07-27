<?php
/**
 * Created by PhpStorm.
 * User: cbonte
 * Date: 2019-05-19
 * Time: 5:22 PM.
 */

namespace CodePrimer\Model;

class Constraint
{
    /** @var int Type used to represent a constraint where all the fields' value must be unique in the whole dataset */
    const TYPE_UNIQUE = 1;

    /** @var string The constraint's name */
    private $name;

    /** @var int */
    private $type;

    /** @var Field[] */
    private $fields = [];

    /** @var string A description of what this constraint is for */
    private $description = '';

    /** @var string The default error message to use when this constraint is violated */
    private $errorMessage = '';

    /**
     * Constraint constructor.
     *
     * @param Field[] $fields
     */
    public function __construct(
        string $name,
        int $type = self::TYPE_UNIQUE,
        array $fields = [],
        string $description = '',
        string $errorMessage = ''
    ) {
        $this->name = $name;
        $this->type = $type;
        $this->fields = $fields;
        $this->description = $description;
        $this->errorMessage = $errorMessage;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Constraint
    {
        $this->name = $name;

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
     * @return Constraint
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

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): Constraint
    {
        $this->description = $description;

        return $this;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    public function setErrorMessage(string $errorMessage): Constraint
    {
        $this->errorMessage = $errorMessage;

        return $this;
    }
}
