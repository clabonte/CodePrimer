<?php

namespace CodePrimer\Helper;

use CodePrimer\Model\Field;
use CodePrimer\Model\Package;

class FieldHelper
{
    /**
     * Checks if a field should contain a UUID value
     * @param Field $field
     * @return bool
     */
    public function isUuid(Field $field): bool
    {
        return strcasecmp($field->getType(), FieldType::UUID) === 0;
    }

    /**
     * Checks if a field should be stored as a string
     * @param Field $field
     * @return bool
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
     * Checks if a field should be stored as a Date only
     * @param Field $field
     * @return bool
     */
    public function isDate(Field $field): bool
    {
        return strcasecmp($field->getType(), FieldType::DATE) === 0;
    }

    /**
     * Checks if a field should be stored as a Time only
     * @param Field $field
     * @return bool
     */
    public function isTime(Field $field): bool
    {
        return strcasecmp($field->getType(), FieldType::TIME) === 0;
    }

    /**
     * Checks if a field should be stored as a Date and Time
     * @param Field $field
     * @return bool
     */
    public function isDateTime(Field $field): bool
    {
        return strcasecmp($field->getType(), FieldType::DATETIME) === 0;
    }

    /**
     * Checks if a field should be stored as a bool
     * @param Field $field
     * @return bool
     */
    public function isBoolean(Field $field): bool
    {
        return strcasecmp($field->getType(), FieldType::BOOL) === 0 || strcasecmp($field->getType(), FieldType::BOOLEAN) === 0;
    }

    /**
     * Checks if a field should be stored as binary blob or byte array
     * @param Field $field
     * @return bool
     */
    public function isBinary(Field $field): bool
    {
        // TODO: Implement isBinary() method.
        return false;
    }

    /**
     * Checks if a field should be stored as an 4-byte integer
     * @param Field $field
     * @return bool
     */
    public function isInteger(Field $field): bool
    {
        $type = strtolower($field->getType());
        return ($type == FieldType::INT) || ($type == FieldType::INTEGER);
    }

    /**
     * Checks if a field should be stored as a 8-byte long
     * @param Field $field
     * @return bool
     */
    public function isLong(Field $field): bool
    {
        $type = strtolower($field->getType());
        return ($type == FieldType::LONG) || ($type == FieldType::ID);
    }

    /**
     * Checks if a field should contain be stored as a 4-byte float
     * @param Field $field
     * @return bool
     */
    public function isFloat(Field $field): bool
    {
        return strcasecmp($field->getType(), FieldType::FLOAT) === 0;
    }

    /**
     * Checks if a field should contain be stored as a 8-byte double
     * @param Field $field
     * @return bool
     */
    public function isDouble(Field $field): bool
    {
        $type = strtolower($field->getType());
        return ($type == FieldType::DOUBLE) || ($type == FieldType::DECIMAL) || ($type == FieldType::PRICE);
    }

    /**
     * Checks if a field represents an identifier (e.g. primary key in a relational database)
     * @param Field $field
     * @return bool
     */
    public function isIdentifier(Field $field): bool
    {
        return (strcasecmp($field->getType(), FieldType::ID) === 0 || strcasecmp($field->getType(), FieldType::UUID) === 0);
    }

    /**
     * Checks if a field represents an auto-incremented value
     * @param Field $field
     * @return bool
     */
    public function isAutoIncrement(Field $field): bool
    {
        return strcasecmp($field->getType(), FieldType::ID) === 0;
    }

    /**
     * Checks if a field represents a known Entity in a given package
     * @param Field $field
     * @param Package $package
     * @return bool
     */
    public function isEntity(Field $field, Package $package): bool
    {
        $result = false;

        $entity = $package->getEntity($field->getType());
        if (isset($entity)) {
            $result = true;
        }

        return $result;
    }

    /**
     * Checks if a field represents a managed datetime field used to automatically track the time at which an entity has
     * been created
     * @param Field $field
     * @return bool
     */
    public function isEntityCreatedTimestamp(Field $field): bool
    {
        $result = false;

        if ($this->isDateTime($field) && $field->isManaged()) {
            $name = strtolower($field->getName());
            if (substr($name, 0, strlen('created')) === 'created') {
                $result = true;
            }
        }
        return $result;
    }

    /**
     * Checks if a field represents a managed datetime field used to automatically track the time at which an entity has
     * been updated last
     * Checks if a field represents a managed datetime field used to track the entity creation timestamp
     * @param Field $field
     * @return bool
     */
    public function isEntityUpdatedTimestamp(Field $field): bool
    {
        $result = false;

        if ($this->isDateTime($field) && $field->isManaged()) {
            $name = strtolower($field->getName());
            if (substr($name, 0, strlen('updated')) === 'updated') {
                $result = true;
            }
        }
        return $result;
    }

    /**
     * Checks if a given field stores only ASCII characters
     * @param Field $field
     * @return bool
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
}
