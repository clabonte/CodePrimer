<?php

namespace CodePrimer\Helper;

use CodePrimer\Model\BusinessBundle;
use CodePrimer\Model\BusinessModel;
use CodePrimer\Model\Data\Data;
use CodePrimer\Model\Data\DataBundle;
use CodePrimer\Model\Data\EventData;
use CodePrimer\Model\Data\EventDataBundle;
use CodePrimer\Model\Field;
use InvalidArgumentException;

class DataBundleHelper
{
    /** @var BusinessBundle */
    private $businessBundle;

    /** @var FieldHelper */
    private $fieldHelper;

    public function __construct(BusinessBundle $businessBundle)
    {
        $this->businessBundle = $businessBundle;
        $this->fieldHelper = new FieldHelper();
    }

    /**
     * Creates a DataBundle object from an existing one by copying the name, description and cloning data elements
     * into a new instance.
     */
    public function createDataBundleFromExisting(DataBundle $existingBundle): DataBundle
    {
        $dataBundle = new DataBundle($existingBundle->getName(), $existingBundle->getDescription());

        foreach ($existingBundle->getData() as $list) {
            foreach ($list as $data) {
                $newData = new Data($data->getBusinessModel(), $data->getField(), $data->getDetails());
                $dataBundle->add($newData);
            }
        }

        return $dataBundle;
    }

    /**
     * Adds a specific list of fields of a BusinessModel as mandatory input data to a bundle.
     *
     * @param Field[]|string[] $fields
     */
    public function addFieldsAsMandatory(EventDataBundle $dataBundle, BusinessModel $businessModel, array $fields, string $fieldDetails = Data::BASIC)
    {
        foreach ($fields as $field) {
            $data = new EventData($businessModel, $field, true, $this->mapDetails($businessModel, $field, $fieldDetails));
            $dataBundle->add($data);
        }
    }

    /**
     * Adds a specific list of fields of a BusinessModel as optional input data to a bundle.
     *
     * @param Field[]|string[] $fields
     */
    public function addFieldsAsOptional(EventDataBundle $dataBundle, BusinessModel $businessModel, array $fields, string $fieldDetails = Data::BASIC)
    {
        foreach ($fields as $field) {
            $data = new EventData($businessModel, $field, false, $this->mapDetails($businessModel, $field, $fieldDetails));
            $dataBundle->add($data);
        }
    }

    /**
     * Adds all fields of a BusinessModel based on the type of bundle provided as input:
     *  - EventDataBundle: Adds only the unmanaged fields of a BusinessModel as input data based on the model's mandatory/optional field definition.
     *  - Others: Adds all the fields (including managed ones) of a BusinessModel.
     */
    public function addBusinessModel(DataBundle $dataBundle, BusinessModel $businessModel, string $fieldDetails = Data::BASIC)
    {
        if ($dataBundle instanceof EventDataBundle) {
            $this->addBusinessModelAsEventData($dataBundle, $businessModel, $fieldDetails);

            return;
        }
        foreach ($businessModel->getFields() as $field) {
            $data = new Data($businessModel, $field, $this->mapDetails($businessModel, $field, $fieldDetails));
            $dataBundle->add($data);
        }
    }

    /**
     * Adds all fields of a BusinessModel that are considered attributes, i.e. not link to other BusinessModels.
     */
    public function addBusinessModelAttributes(DataBundle $dataBundle, BusinessModel $businessModel, bool $includeManagedAttributes = false)
    {
        if ($dataBundle instanceof EventDataBundle) {
            $this->addBusinessModelAttributesAsEventData($dataBundle, $businessModel);

            return;
        }
        foreach ($businessModel->getFields() as $field) {
            if (!$this->fieldHelper->isBusinessModel($field, $this->businessBundle)) {
                if ($includeManagedAttributes || !$field->isManaged()) {
                    $data = new Data($businessModel, $field);
                    $dataBundle->add($data);
                }
            }
        }
    }

    /**
     * Adds all fields of a BusinessModel based on the type of bundle provided as input:
     *  - EventDataBundle: Adds only the unmanaged fields of a BusinessModel as input data based on the model's mandatory/optional field definition.
     *  - Others: Adds all the fields (including managed ones) of a BusinessModel.
     *
     * @param Field[]|string[] $excludeFields
     */
    public function addBusinessModelExceptFields(DataBundle $dataBundle, BusinessModel $businessModel, array $excludeFields, string $fieldDetails = Data::BASIC)
    {
        if ($dataBundle instanceof EventDataBundle) {
            $this->addBusinessModelExceptFieldsAsEventData($dataBundle, $businessModel, $excludeFields, $fieldDetails);

            return;
        }
        foreach ($businessModel->getFields() as $field) {
            if (!$this->isFieldInList($field, $excludeFields)) {
                $data = new Data($businessModel, $field, $this->mapDetails($businessModel, $field, $fieldDetails));
                $dataBundle->add($data);
            }
        }
    }

    /**
     * Adds a specific list of fields of a BusinessModel as existing data to a bundle.
     *
     * @param Field[]|string[] $fields
     */
    public function addFields(DataBundle $dataBundle, BusinessModel $businessModel, array $fields, string $fieldDetails = Data::BASIC)
    {
        foreach ($fields as $field) {
            $data = new Data($businessModel, $field, $this->mapDetails($businessModel, $field, $fieldDetails));
            $dataBundle->add($data);
        }
    }

    /**
     * Adds all the unmanaged fields of a BusinessModel as input data to a bundle following the model's mandatory/optional field definition.
     */
    private function addBusinessModelAsEventData(EventDataBundle $dataBundle, BusinessModel $businessModel, string $fieldDetails = Data::BASIC)
    {
        foreach ($businessModel->getFields() as $field) {
            if (!$field->isManaged()) {
                $data = new EventData($businessModel, $field, $field->isMandatory(), $this->mapDetails($businessModel, $field, $fieldDetails));
                $dataBundle->add($data);
            }
        }
    }

    private function addBusinessModelAttributesAsEventData(DataBundle $dataBundle, BusinessModel $businessModel)
    {
        foreach ($businessModel->getFields() as $field) {
            if (!$field->isManaged() && !$this->fieldHelper->isBusinessModel($field, $this->businessBundle)) {
                $data = new EventData($businessModel, $field, $field->isMandatory());
                $dataBundle->add($data);
            }
        }
    }

    /**
     * Adds all the unmanaged fields of a BusinessModel as input data to a bundle following the model's mandatory/optional field definition.
     */
    private function addBusinessModelExceptFieldsAsEventData(EventDataBundle $dataBundle, BusinessModel $businessModel, array $excludeFields, string $fieldDetails = Data::BASIC)
    {
        foreach ($businessModel->getFields() as $field) {
            if (!$field->isManaged() && !$this->isFieldInList($field, $excludeFields)) {
                $data = new EventData($businessModel, $field, $field->isMandatory(), $this->mapDetails($businessModel, $field, $fieldDetails));
                $dataBundle->add($data);
            }
        }
    }

    /**
     * @param Field|string $desiredField
     */
    private function mapDetails(BusinessModel $businessModel, $desiredField, string $fieldDetails): string
    {
        if (!$desiredField instanceof Field) {
            $field = $businessModel->getField($desiredField);
            if (null === $field) {
                throw new InvalidArgumentException('Requested field '.$desiredField.' is not defined in BusinessModel '.$businessModel->getName());
            }
        } else {
            $field = $desiredField;
        }

        if ($this->fieldHelper->isNativeType($field)) {
            return Data::BASIC;
        }

        return $fieldDetails;
    }

    /**
     * @param Field[]|string[] $excludeFields
     */
    private function isFieldInList(Field $field, array $excludeFields): bool
    {
        foreach ($excludeFields as $excludeField) {
            if ($excludeField instanceof Field) {
                if ($field->getName() == $excludeField->getName()) {
                    return true;
                }
            } elseif ($field->getName() == $excludeField) {
                return true;
            }
        }

        return false;
    }
}
