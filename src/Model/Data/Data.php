<?php

namespace CodePrimer\Model\Data;

use CodePrimer\Model\BusinessModel;
use CodePrimer\Model\Field;
use InvalidArgumentException;

class Data
{
    // Constants used to specify the level of details associated with a given, non-native field
    /** @var string Only need a reference to the BusinessModel associated with a given field */
    const REFERENCE = 'reference';
    /** @var string Only need the mandatory fields of the BusinessModel associated with a given field */
    const BASIC = 'basic';
    /** @var string Need all the fields of the BusinessModel associated with a given field */
    const FULL = 'full';

    /** @var BusinessModel */
    private $businessModel;

    /** @var Field */
    private $field;

    /** @var string One of the following constants: REFERENCE, BASIC, FULL */
    private $details;

    /**
     * Data constructor.
     *
     * @param Field|string $field
     * @param string       $details The level of details to associate with a non-native field (e.g. BusinessModel)
     *
     * @throws InvalidArgumentException If the details provided is not valid
     */
    public function __construct(BusinessModel $businessModel, $field, string $details = self::BASIC)
    {
        $realField = $this->validate($businessModel, $field, $details);

        $this->businessModel = $businessModel;
        $this->field = $realField;
        $this->details = $details;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getBusinessModel(): BusinessModel
    {
        return $this->businessModel;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getField(): Field
    {
        return $this->field;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDetails(): string
    {
        return $this->details;
    }

    public function isSame(Data $otherData): bool
    {
        $result = true;

        if ($this->businessModel->getName() !== $otherData->getBusinessModel()->getName()) {
            $result = false;
        } elseif ($this->field->getName() !== $otherData->getField()->getName()) {
            $result = false;
        } elseif ($this->details !== $otherData->getDetails()) {
            $result = false;
        }

        return $result;
    }

    /**
     * @param Field|string $requestedField
     *
     * @throws InvalidArgumentException If any field is not valid
     */
    private function validate(BusinessModel $businessModel, $requestedField, string $details): Field
    {
        $name = $requestedField;
        if ($requestedField instanceof Field) {
            $name = $requestedField->getName();
        } elseif (!is_string($requestedField)) {
            throw new InvalidArgumentException('Requested field must be either of type Field or string');
        }
        $field = $businessModel->getField($name);
        if (null == $field) {
            throw new InvalidArgumentException('Requested field '.$name.' is not defined in BusinessModel '.$businessModel->getName());
        }

        switch ($details) {
            case self::BASIC:
            case self::REFERENCE:
            case self::FULL:
                break;
            default:
                throw new InvalidArgumentException('Invalid details provided: '.$details.'. Must be one of: basic, reference or full');
        }

        return $field;
    }
}
