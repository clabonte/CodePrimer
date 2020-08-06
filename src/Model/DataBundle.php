<?php

namespace CodePrimer\Model;

use InvalidArgumentException;

class DataBundle
{
    /** @var string */
    private $name;

    /** @var string */
    private $description;

    /** @var BusinessModel[] */
    private $businessModels = [];

    /** @var Field[][] */
    private $mandatoryFields = [];

    /** @var Field[][] */
    private $optionalFields = [];

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
     *
     * @return DataBundle
     */
    public function setName(string $name): self
    {
        $this->name = $name;

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
     *
     * @return DataBundle
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @codeCoverageIgnore
     *
     * @return BusinessModel[]
     */
    public function getBusinessModels(): array
    {
        return $this->businessModels;
    }

    public function isBusinessModelPresent(string $name): bool
    {
        return isset($this->businessModels[$name]);
    }

    /**
     * Adds all the fields (including managed ones) as data being carried by this bundle.
     */
    public function addBusinessModel(BusinessModel $businessModel)
    {
        $mFields = [];
        $oFields = [];

        foreach ($businessModel->getFields() as $field) {
            if ($field->isMandatory()) {
                $mFields[$field->getName()] = $field;
            } else {
                $oFields[$field->getName()] = $field;
            }
        }

        $this->businessModels[$businessModel->getName()] = $businessModel;
        $this->mandatoryFields[$businessModel->getName()] = $mFields;
        $this->optionalFields[$businessModel->getName()] = $oFields;
    }

    /**
     * Adds a list of fields from a business model as data being carried by this bundle.
     * NOTE: If both the mandatory and optional field lists are empty, the bundle is filled up with the
     * unmanaged fields defined in the business model based on whether or not they are mandatory for the model.
     *
     * @param BusinessModel $businessModel   The business model to add fields to this bundle
     * @param array         $mandatoryFields The list of mandatory fields to use from this business model. Can be a list of Field or string
     * @param array         $optionalFields  The list of optional fields to use from this business model. Can be a list of Field or string
     *
     * @throws InvalidArgumentException If one of the fields requested does not belong to the business model
     */
    public function addFields(BusinessModel $businessModel, array $mandatoryFields = [], array $optionalFields = [])
    {
        // Make sure the mandatory fields specified are valid for this model...
        $mFields = [];
        foreach ($mandatoryFields as $desired) {
            $name = $desired;
            if ($desired instanceof Field) {
                $name = $desired->getName();
            }
            $field = $businessModel->getField($name);
            if (null == $field) {
                throw new InvalidArgumentException('Requested mandatory field '.$name.' is not defined in BusinessModel '.$businessModel->getName());
            }
            $mFields[$field->getName()] = $field;
        }

        // Make sure the optional fields specified are valid for this model...
        $oFields = [];
        foreach ($optionalFields as $desired) {
            $name = $desired;
            if ($desired instanceof Field) {
                $name = $desired->getName();
            }
            $field = $businessModel->getField($name);
            if (null == $field) {
                throw new InvalidArgumentException('Requested optional field '.$name.' is not defined in BusinessModel '.$businessModel->getName());
            }
            // Make sure the field is not also in the mandatory list...
            if (isset($mFields[$field->getName()])) {
                throw new InvalidArgumentException('Requested field '.$name.' for BusinessModel '.$businessModel->getName().' cannot be both mandatory and optional');
            }
            $oFields[$field->getName()] = $field;
        }

        // If no fields have been specified, let's fill the lists based on the BusinessModel' unmanaged fields
        if (empty($mFields) && empty($oFields)) {
            foreach ($businessModel->getFields() as $field) {
                if (!$field->isManaged()) {
                    if ($field->isMandatory()) {
                        $mFields[$field->getName()] = $field;
                    } else {
                        $oFields[$field->getName()] = $field;
                    }
                }
            }
        }

        $this->businessModels[$businessModel->getName()] = $businessModel;
        $this->mandatoryFields[$businessModel->getName()] = $mFields;
        $this->optionalFields[$businessModel->getName()] = $oFields;
    }

    /**
     * @return Field[]
     */
    public function getBusinessModelMandatoryFields(string $businessModel)
    {
        if (isset($this->mandatoryFields[$businessModel])) {
            return $this->mandatoryFields[$businessModel];
        }

        return [];
    }

    /**
     * @return Field[]
     */
    public function getBusinessModelOptionalFields(string $businessModel)
    {
        if (isset($this->optionalFields[$businessModel])) {
            return $this->optionalFields[$businessModel];
        }

        return [];
    }

    /**
     * @return Field[]
     */
    public function getMandatoryFields(): array
    {
        return $this->mandatoryFields;
    }

    /**
     * @return Field[]
     */
    public function getOptionalFields(): array
    {
        return $this->optionalFields;
    }
}
