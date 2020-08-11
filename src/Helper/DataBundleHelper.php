<?php

namespace CodePrimer\Helper;

use CodePrimer\Model\BusinessModel;
use CodePrimer\Model\Data\Data;
use CodePrimer\Model\Data\DataBundle;
use CodePrimer\Model\Data\EventData;
use CodePrimer\Model\Data\EventDataBundle;
use CodePrimer\Model\Field;

class DataBundleHelper
{
    /** @var FieldHelper */
    private $fieldHelper;

    public function __construct()
    {
        $this->fieldHelper = new FieldHelper();
    }

    /**
     * Adds a specific list of fields of a BusinessModel as mandatory input data to a bundle.
     *
     * @param Field[]|string[] $fields
     */
    public function addFieldsAsMandatory(EventDataBundle $dataBundle, BusinessModel $businessModel, array $fields, string $fieldDetails = Data::BASIC)
    {
        foreach ($fields as $field) {
            $data = new EventData($businessModel, $field, true, $this->mapDetails($field, $fieldDetails));
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
            $data = new EventData($businessModel, $field, false, $this->mapDetails($field, $fieldDetails));
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
            $data = new Data($businessModel, $field, $this->mapDetails($field, $fieldDetails));
            $dataBundle->add($data);
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
            $data = new Data($businessModel, $field, $this->mapDetails($field, $fieldDetails));
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
                $data = new EventData($businessModel, $field, $field->isMandatory(), $this->mapDetails($field, $fieldDetails));
                $dataBundle->add($data);
            }
        }
    }

    private function mapDetails(Field $field, string $fieldDetails): string
    {
        if ($this->fieldHelper->isNativeType($field)) {
            return Data::BASIC;
        }

        return $fieldDetails;
    }
}
