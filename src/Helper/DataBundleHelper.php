<?php

namespace CodePrimer\Helper;

use CodePrimer\Model\BusinessModel;
use CodePrimer\Model\Data\Data;
use CodePrimer\Model\Data\DataBundle;
use CodePrimer\Model\Data\ExistingData;
use CodePrimer\Model\Data\InputData;
use CodePrimer\Model\Field;

class DataBundleHelper
{
    /**
     * Adds all the unmanaged fields of a BusinessModel as input data to a bundle following the model's mandatory/optional field definition.
     */
    public function addBusinessModelAsInput(DataBundle $dataBundle, BusinessModel $businessModel, string $type, string $details = Data::BASIC)
    {
        foreach ($businessModel->getFields() as $field) {
            if (!$field->isManaged()) {
                $data = new InputData($type, $businessModel, $field, $field->isMandatory(), $details);
                $dataBundle->addInputData($data);
            }
        }
    }

    /**
     * Adds a specific list of fields of a BusinessModel as mandatory input data to a bundle.
     *
     * @param Field[]|string[] $fields
     */
    public function addFieldsAsMandatoryInput(DataBundle $dataBundle, BusinessModel $businessModel, array $fields, string $type, string $details = Data::BASIC)
    {
        foreach ($fields as $field) {
            $data = new InputData($type, $businessModel, $field, true, $details);
            $dataBundle->addInputData($data);
        }
    }

    /**
     * Adds a specific list of fields of a BusinessModel as optional input data to a bundle.
     *
     * @param Field[]|string[] $fields
     */
    public function addFieldsAsOptionalInput(DataBundle $dataBundle, BusinessModel $businessModel, array $fields, string $type, string $details = Data::BASIC)
    {
        foreach ($fields as $field) {
            $data = new InputData($type, $businessModel, $field, false, $details);
            $dataBundle->addInputData($data);
        }
    }

    /**
     * Adds all the fields (including managed ones) of a BusinessModel as existing data to a bundle.
     */
    public function addBusinessModelAsExisting(DataBundle $dataBundle, BusinessModel $businessModel, string $origin, string $source = '', string $details = Data::BASIC)
    {
        foreach ($businessModel->getFields() as $field) {
            $data = new ExistingData($origin, $businessModel, $field, $source, $details);
            $dataBundle->addExistingData($data);
        }
    }

    /**
     * Adds a specific list of fields of a BusinessModel as existing data to a bundle.
     *
     * @param Field[]|string[] $fields
     */
    public function addFieldsAsExisting(DataBundle $dataBundle, BusinessModel $businessModel, array $fields, string $origin, string $source = '', string $details = Data::BASIC)
    {
        foreach ($fields as $field) {
            $data = new ExistingData($origin, $businessModel, $field, $source, $details);
            $dataBundle->addExistingData($data);
        }
    }
}
