<?php
/**
 * Created by PhpStorm.
 * User: cbonte
 * Date: 2019-05-19
 * Time: 5:22 PM
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
    private $fields = array();

    /** @var string A description of what this constraint is for */
    private $description = '';

    /** @var string The default error message to use when this constraint is violated */
    private $errorMessage = '';

    /**
     * Constraint constructor.
     * @param string $name
     * @param int $type
     * @param Field[] $fields
     * @param string $description
     * @param string $errorMessage
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

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Constraint
     */
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
     * @param Field $field
     * @return Constraint
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
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Constraint
     */
    public function setDescription(string $description): Constraint
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    /**
     * @param string $errorMessage
     * @return Constraint
     */
    public function setErrorMessage(string $errorMessage): Constraint
    {
        $this->errorMessage = $errorMessage;
        return $this;
    }
}
