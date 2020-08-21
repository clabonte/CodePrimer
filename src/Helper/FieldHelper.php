<?php

namespace CodePrimer\Helper;

use CodePrimer\Model\BusinessBundle;
use CodePrimer\Model\Field;

class FieldHelper
{
    /**
     * Checks if a field should contain a UUID value.
     */
    public function isUuid(Field $field): bool
    {
        return 0 === strcasecmp($field->getType(), FieldType::UUID);
    }

    /**
     * Checks if a field should be stored as a string.
     */
    public function isString(Field $field): bool
    {
        $result = false;

        switch (strtolower($field->getType())) {
            case FieldType::STRING:
            case FieldType::TEXT:
            case FieldType::UUID:
            case FieldType::EMAIL:
            case FieldType::URL:
            case FieldType::PASSWORD:
            case FieldType::PHONE:
            case FieldType::RANDOM_STRING:
                $result = true;
                break;
        }

        return $result;
    }

    /**
     * Checks if a field should be stored as a Date only.
     */
    public function isDate(Field $field): bool
    {
        return 0 === strcasecmp($field->getType(), FieldType::DATE);
    }

    /**
     * Checks if a field should be stored as a Time only.
     */
    public function isTime(Field $field): bool
    {
        return 0 === strcasecmp($field->getType(), FieldType::TIME);
    }

    /**
     * Checks if a field should be stored as a Date and Time.
     */
    public function isDateTime(Field $field): bool
    {
        return 0 === strcasecmp($field->getType(), FieldType::DATETIME);
    }

    /**
     * Checks if a field should be stored as a bool.
     */
    public function isBoolean(Field $field): bool
    {
        return 0 === strcasecmp($field->getType(), FieldType::BOOL) || 0 === strcasecmp($field->getType(), FieldType::BOOLEAN);
    }

    /**
     * Checks if a field should be stored as binary blob or byte array.
     */
    public function isBinary(Field $field): bool
    {
        // TODO: Implement isBinary() method.
        return false;
    }

    /**
     * Checks if a field should be stored as an 4-byte integer.
     */
    public function isInteger(Field $field): bool
    {
        $type = strtolower($field->getType());

        return (FieldType::INT == $type) || (FieldType::INTEGER == $type);
    }

    /**
     * Checks if a field should be stored as a 8-byte long.
     */
    public function isLong(Field $field): bool
    {
        $type = strtolower($field->getType());

        return (FieldType::LONG == $type) || (FieldType::ID == $type);
    }

    /**
     * Checks if a field should contain be stored as a 4-byte float.
     */
    public function isFloat(Field $field): bool
    {
        return 0 === strcasecmp($field->getType(), FieldType::FLOAT);
    }

    /**
     * Checks if a field should be stored as a price.
     */
    public function isPrice(Field $field): bool
    {
        $type = strtolower($field->getType());

        return FieldType::PRICE == $type;
    }

    /**
     * Checks if a field should contain be stored as a 8-byte double.
     */
    public function isDouble(Field $field): bool
    {
        $type = strtolower($field->getType());

        return (FieldType::DOUBLE == $type) || (FieldType::DECIMAL == $type);
    }

    /**
     * Checks if a field represents an auto-incremented value.
     */
    public function isAutoIncrement(Field $field): bool
    {
        return 0 === strcasecmp($field->getType(), FieldType::ID);
    }

    /**
     * Checks if a field represents a known BusinessModel in a given package.
     */
    public function isBusinessModel(Field $field, BusinessBundle $businessBundle): bool
    {
        $result = false;

        $businessModel = $businessBundle->getBusinessModel($field->getType());
        if (isset($businessModel)) {
            $result = true;
        }

        return $result;
    }

    /**
     * Checks if a field represents a managed datetime field used to automatically track the time at which a business model has
     * been created.
     */
    public function isBusinessModelCreatedTimestamp(Field $field): bool
    {
        $result = false;

        if ($this->isDateTime($field) && $field->isManaged()) {
            $name = strtolower($field->getName());
            if ('created' === substr($name, 0, strlen('created'))) {
                $result = true;
            }
        }

        return $result;
    }

    /**
     * Checks if a field represents a managed datetime field used to automatically track the time at which a business model has
     * been updated last
     * Checks if a field represents a managed datetime field used to track the business model creation timestamp.
     */
    public function isBusinessModelUpdatedTimestamp(Field $field): bool
    {
        $result = false;

        if ($this->isDateTime($field) && $field->isManaged()) {
            $name = strtolower($field->getName());
            if ('updated' === substr($name, 0, strlen('updated'))) {
                $result = true;
            }
        }

        return $result;
    }

    /**
     * Checks if a given field stores only ASCII characters.
     */
    public function isAsciiString(Field $field): bool
    {
        $result = false;

        switch ($field->getType()) {
            case FieldType::UUID:
            case FieldType::EMAIL:
            case FieldType::URL:
            case FieldType::PHONE:
                $result = true;
                break;
        }

        return $result;
    }

    /**
     * Checks if a given field is a 'native' CodePrimer type, i.e. one defined in FieldType.
     *
     * @see FieldType
     */
    public function isNativeType(Field $field): bool
    {
        $result = false;

        switch ($field->getType()) {
            case FieldType::UUID:
            case FieldType::STRING:
            case FieldType::TEXT:
            case FieldType::EMAIL:
            case FieldType::URL:
            case FieldType::PASSWORD:
            case FieldType::PHONE:
            case FieldType::DATE:
            case FieldType::TIME:
            case FieldType::DATETIME:
            case FieldType::BOOL:
            case FieldType::BOOLEAN:
            case FieldType::INT:
            case FieldType::INTEGER:
            case FieldType::ID:
            case FieldType::LONG:
            case FieldType::FLOAT:
            case FieldType::DOUBLE:
            case FieldType::DECIMAL:
            case FieldType::PRICE:
            case FieldType::RANDOM_STRING:
                $result = true;
                break;
        }

        return $result;
    }
}
