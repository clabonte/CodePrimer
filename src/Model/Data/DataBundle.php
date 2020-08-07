<?php

namespace CodePrimer\Model\Data;

class DataBundle
{
    /** @var string The name associated with this bundle */
    private $name;

    /** @var string A description of the bundle's purpose */
    private $description;

    /** @var InputData[][] */
    private $inputData = [];

    /** @var ExistingData[][] */
    private $existingData = [];

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
     * @return InputData[][]
     */
    public function getInputData(): array
    {
        return $this->inputData;
    }

    /**
     * @codeCoverageIgnore
     *
     * @return ExistingData[][]
     */
    public function getExistingData(): array
    {
        return $this->existingData;
    }

    public function addInputData(InputData $data): self
    {
        $modelName = $data->getBusinessModel()->getName();
        $fieldName = $data->getField()->getName();

        if (!isset($this->inputData[$modelName])) {
            $this->inputData[$modelName] = [];
        }
        $this->inputData[$modelName][$fieldName] = $data;

        return $this;
    }

    public function addExistingData(ExistingData $data): self
    {
        $modelName = $data->getBusinessModel()->getName();
        $fieldName = $data->getField()->getName();

        if (!isset($this->existingData[$modelName])) {
            $this->existingData[$modelName] = [];
        }
        $this->existingData[$modelName][$fieldName] = $data;

        return $this;
    }

    public function isBusinessModelPresent(string $name): bool
    {
        return isset($this->inputData[$name]) || isset($this->existingData[$name]);
    }

    /**
     * @return string[]
     */
    public function listInputDataBusinessModelNames(): array
    {
        return array_keys($this->inputData);
    }

    /**
     * @return string[]
     */
    public function listExistingDataBusinessModelNames(): array
    {
        return array_keys($this->existingData);
    }

    /**
     * Return the list of input data defined in this bundle for a given business model.
     *
     * @return InputData[]
     */
    public function listInputData(string $businessModelName): array
    {
        if (isset($this->inputData[$businessModelName])) {
            return $this->inputData[$businessModelName];
        }

        return [];
    }

    /**
     * Return the list of existing data defined in this bundle for a given business model.
     *
     * @return ExistingData[]
     */
    public function listExistingData(string $businessModelName): array
    {
        if (isset($this->existingData[$businessModelName])) {
            return $this->existingData[$businessModelName];
        }

        return [];
    }
}
