<?php

namespace CodePrimer\Model\Data;

abstract class DataBundle
{
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
    public function getName(): string
    {
        return $this->name;
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
     *
     * @return Data[][]
     */
    public function getData(): array
    {
        return $this->data;
    }

    protected function addData(Data $data): self
    {
        $modelName = $data->getBusinessModel()->getName();
        $fieldName = $data->getField()->getName();

        if (!isset($this->data[$modelName])) {
            $this->data[$modelName] = [];
        }
        $this->data[$modelName][$fieldName] = $data;

        return $this;
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
