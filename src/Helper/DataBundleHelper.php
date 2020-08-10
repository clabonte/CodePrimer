<?php

namespace CodePrimer\Helper;

use CodePrimer\Model\BusinessModel;
use CodePrimer\Model\Data\Data;
use CodePrimer\Model\Data\DataBundle;
use CodePrimer\Model\Data\ExistingData;
use CodePrimer\Model\Data\ExistingDataBundle;
use CodePrimer\Model\Data\InputData;
use CodePrimer\Model\Data\InputDataBundle;
use CodePrimer\Model\Field;

class DataBundleHelper
{
    /**
     * Adds all the unmanaged fields of a BusinessModel as input data to a bundle following the model's mandatory/optional field definition.
     */
    public function addBusinessModelAsInput(InputDataBundle $dataBundle, BusinessModel $businessModel, string $details = Data::BASIC)
    {
        foreach ($businessModel->getFields() as $field) {
            if (!$field->isManaged()) {
                $data = new InputData($businessModel, $field, $field->isMandatory(), $details);
                $dataBundle->add($data);
            }
        }
    }

    /**
     * Adds a specific list of fields of a BusinessModel as mandatory input data to a bundle.
     *
     * @param Field[]|string[] $fields
     */
    public function addFieldsAsMandatoryInput(InputDataBundle $dataBundle, BusinessModel $businessModel, array $fields, string $details = Data::BASIC)
    {
        foreach ($fields as $field) {
            $data = new InputData($businessModel, $field, true, $details);
            $dataBundle->add($data);
        }
    }

    /**
     * Adds a specific list of fields of a BusinessModel as optional input data to a bundle.
     *
     * @param Field[]|string[] $fields
     */
    public function addFieldsAsOptionalInput(InputDataBundle $dataBundle, BusinessModel $businessModel, array $fields, string $details = Data::BASIC)
    {
        foreach ($fields as $field) {
            $data = new InputData($businessModel, $field, false, $details);
            $dataBundle->add($data);
        }
    }

    /**
     * Adds all the fields (including managed ones) of a BusinessModel as existing data to a bundle.
     */
    public function addBusinessModelAsExisting(ExistingDataBundle $dataBundle, BusinessModel $businessModel, string $source = ExistingData::DEFAULT_SOURCE, string $details = Data::BASIC)
    {
        foreach ($businessModel->getFields() as $field) {
            $data = new ExistingData($businessModel, $field, $source, $details);
            $dataBundle->add($data);
        }
    }

    /**
     * Adds a specific list of fields of a BusinessModel as existing data to a bundle.
     *
     * @param Field[]|string[] $fields
     */
    public function addFieldsAsExisting(ExistingDataBundle $dataBundle, BusinessModel $businessModel, array $fields, string $source = ExistingData::DEFAULT_SOURCE, string $details = Data::BASIC)
    {
        foreach ($fields as $field) {
            $data = new ExistingData($businessModel, $field, $source, $details);
            $dataBundle->add($data);
        }
    }
}
