<?php

namespace CodePrimer\Model\Data;

abstract class DataBundle
{
    /** @var string Constant used to indicate that a data bundle represents a single data structure */
    public const SIMPLE = 'simple';
    /** @var string Constant used to indicate that a data bundle represents a list of structured data */
    public const LIST = 'list';
    /** @var string Constant used to indicate that a data bundle represents a map of structured data */
    public const MAP = 'map';

    /** @var string The name associated with this bundle */
    private $name;

    /** @var string A description of the bundle's purpose */
    private $description;

    /**
     * Keys: [BusinessModel.name][Field.name].
     *
     * @var Data[][]
     */
    private $data = [];

    /** @var string The type of structure to use */
    private $structure = self::SIMPLE;

    /**
     * DataBundle constructor.
     */
    public function __construct(string $name = '', string $description = '')
    {
        $this->name = $name;
        $this->description = $description;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setName(string $name): DataBundle
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setDescription(string $description): DataBundle
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getStructure(): string
    {
        return $this->structure;
    }

    /**
     * @codeCoverageIgnore
     *
     * @return $this
     */
    public function setAsSimpleStructure(): DataBundle
    {
        $this->structure = self::SIMPLE;

        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public function isSimpleStructure(): bool
    {
        return self::SIMPLE == $this->structure;
    }

    /**
     * @codeCoverageIgnore
     *
     * @return $this
     */
    public function setAsListStructure(): DataBundle
    {
        $this->structure = self::LIST;

        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public function isListStructure(): bool
    {
        return self::LIST == $this->structure;
    }

    /**
     * @codeCoverageIgnore
     */
    public function isMapStructure(): bool
    {
        return self::MAP == $this->structure;
    }

    /**
     * @codeCoverageIgnore
     *
     * @return Data[][]
     */
    public function getData(): array
    {
        return $this->data;
    }

    public function add(Data $data): self
    {
        $modelName = $data->getBusinessModel()->getName();
        $fieldName = $data->getField()->getName();

        if (!isset($this->data[$modelName])) {
            $this->data[$modelName] = [];
        }
        $this->data[$modelName][$fieldName] = $data;

        return $this;
    }

    public function isPresent(string $modelName, string $fieldName): bool
    {
        return isset($this->data[$modelName][$fieldName]);
    }

    public function get(string $modelName, string $fieldName): ?Data
    {
        if (isset($this->data[$modelName][$fieldName])) {
            return $this->data[$modelName][$fieldName];
        }

        return null;
    }

    public function remove(string $modelName, string $fieldName): bool
    {
        if (isset($this->data[$modelName][$fieldName])) {
            unset($this->data[$modelName][$fieldName]);

            return true;
        }

        return false;
    }

    public function isBusinessModelPresent(string $name): bool
    {
        return isset($this->data[$name]);
    }

    /**
     * @return string[]
     */
    public function listBusinessModelNames(): array
    {
        return array_keys($this->data);
    }

    /**
     * Return the list of input data defined in this bundle for a given business model.
     *
     * @return Data[]
     */
    public function listData(string $businessModelName): array
    {
        if (isset($this->data[$businessModelName])) {
            return $this->data[$businessModelName];
        }

        return [];
    }
}
