<?php


namespace CodePrimer\Model\Database;

use CodePrimer\Model\Field;

class Index
{
    const ASCENDING = 'ASC';
    const DESCENDING = 'DESC';

    /** @var string The index name */
    private $name;

    /** @var string Description of what this index is used for */
    private $description;

    /** @var Field[] */
    private $fields;

    /** @var string */
    private $order;

    /**
     * Index constructor.
     * @param string $name
     * @param Field[] $fields
     * @param $order
     */
    public function __construct(string $name, array $fields, string $order = self::ASCENDING)
    {
        $this->name = $name;
        $this->fields = $fields;
        $this->order = $order;
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
     * @return Index
     */
    public function setName(string $name): Index
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Index
     */
    public function setDescription(string $description): Index
    {
        $this->description = $description;
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
     * @return Index
     */
    public function setFields(array $fields): Index
    {
        $this->fields = $fields;
        return $this;
    }

    /**
     * @return string
     */
    public function getOrder(): string
    {
        return $this->order;
    }

    /**
     * @param string $order
     * @return Index
     */
    public function setOrder(string $order): Index
    {
        $this->order = $order;
        return $this;
    }



}
