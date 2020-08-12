<?php

namespace CodePrimer\Helper;

use CodePrimer\Model\BusinessBundle;
use CodePrimer\Model\BusinessModel;
use CodePrimer\Model\Field;
use Doctrine\Common\Inflector\Inflector;
use InvalidArgumentException;
use RuntimeException;

class BusinessModelHelper
{
    /**
     * Returns the name of the Repository class associated with a given entity.
     */
    public function getRepositoryClass(BusinessModel $businessModel): string
    {
        return Inflector::classify($businessModel->getName()).'Repository';
    }

    public function generateIdentifierField(BusinessModel $businessModel, string $identifierType = FieldType::UUID): Field
    {
        if (FieldType::UUID != $identifierType && FieldType::ID != $identifierType) {
            throw new InvalidArgumentException('Invalid identifier type provided: '.$identifierType.'. Must be either FieldType::UUID or FieldType.ID');
        }
        $name = 'id';
        if (null !== $businessModel->getField($name)) {
            $name = Inflector::camelize($businessModel->getName()).'Id';
        }
        if (null !== $businessModel->getField($name)) {
            throw new RuntimeException('Cannot generate ID field for business model '.$businessModel->getName().': "id" and "'.$name.'" fields are already defined. Did you forget to specify an identifier for this model?');
        }

        $example = 'b34d38eb-1164-4289-98b4-65706837c4d7';
        if (FieldType::ID == $identifierType) {
            $example = '1001';
        }

        $field = new Field($name, $identifierType, 'Business model unique identifier field');
        $field->setMandatory(true)
            ->setManaged(true)
            ->setGenerated(true)
            ->setExample($example);

        $businessModel->addField($field);

        return $field;
    }

    public function generateTimestampFields(BusinessModel $businessModel, bool $created = true, bool $updated = true)
    {
        if ($created) {
            $field = new Field('created', FieldType::DATETIME, 'The date and time at which this '.$businessModel->getName().' was created');
            $field->setManaged(true)
                ->setGenerated(true)
                ->setExample('2021-08-21T15:56:59Z');

            $businessModel->addField($field);
        }

        if ($updated) {
            $field = new Field('updated', FieldType::DATETIME, 'The date and time at which this '.$businessModel->getName().' was last updated');
            $field->setManaged(true)
                ->setGenerated(true)
                ->setExample('2021-08-21T16:57:00Z');

            $businessModel->addField($field);
        }
    }

    /**
     * Retrieves the field used to automatically tracks the entity creation timestamp, if any.
     */
    public function getCreatedTimestampField(BusinessModel $businessModel): ?Field
    {
        $result = null;

        $fieldHelper = new FieldHelper();

        foreach ($businessModel->getManagedFields() as $field) {
            if ($fieldHelper->isBusinessModelCreatedTimestamp($field)) {
                $result = $field;
                break;
            }
        }

        return $result;
    }

    /**
     * Retrieves the field used to automatically tracks the entity last update timestamp, if any.
     */
    public function getUpdatedTimestampField(BusinessModel $businessModel): ?Field
    {
        $result = null;

        $fieldHelper = new FieldHelper();

        foreach ($businessModel->getManagedFields() as $field) {
            if ($fieldHelper->isBusinessModelUpdatedTimestamp($field)) {
                $result = $field;
                break;
            }
        }

        return $result;
    }

    public function isManagedTimestamp(BusinessModel $businessModel): bool
    {
        $result = false;

        $fieldHelper = new FieldHelper();

        foreach ($businessModel->getManagedFields() as $field) {
            if ($fieldHelper->isBusinessModelCreatedTimestamp($field) || $fieldHelper->isBusinessModelUpdatedTimestamp($field)) {
                $result = true;
                break;
            }
        }

        return $result;
    }

    /**
     * Retrieves the list of entities that are linked to a given entity.
     *
     * @return BusinessModel[]
     */
    public function getLinkedBusinessModels(BusinessModel $businessModel): array
    {
        $businessModels = [];

        foreach ($businessModel->getRelations() as $relation) {
            $remoteBusinessModel = $relation->getRemoteSide()->getBusinessModel();
            if (!in_array($remoteBusinessModel, $businessModels)) {
                $businessModels[] = $remoteBusinessModel;
            }
        }

        return $businessModels;
    }

    /**
     * Retrieves the list of business attributes associated with a BusinessModel.
     * A 'business attribute' is a field that is not managed and not another BusinessModel.
     *
     * @return array
     */
    public function listBusinessAttributeFields(BusinessModel $businessModel, BusinessBundle $bundle)
    {
        $fields = [];

        $fieldHelper = new FieldHelper();

        foreach ($businessModel->getFields() as $field) {
            if ($fieldHelper->isNativeType($field)) {
                if (!$field->isManaged()) {
                    $fields[] = $field;
                }
            } elseif (null === $bundle->getBusinessModel($field->getType())) {
                $fields[] = $field;
            }
        }

        return $fields;
    }

    /**
     * Checks if a given field must be unique for a given business model.
     * A field is considered 'unique' if it has a 'unique' constraint with only this single field.
     *
     * @param BusinessModel $businessModel The model to check against
     * @param string        $field         The field to check for uniqueness
     *
     * @return bool whether the field must carry unique values for a given model
     */
    public function isUniqueField(BusinessModel $businessModel, string $field)
    {
        $result = false;

        $constraints = $businessModel->getUniqueConstraints();
        foreach ($constraints as $constraint) {
            $constraintField = $constraint->getField($field);
            if ($constraintField) {
                $result = 1 == count($constraint->getFields());
            }
        }

        return $result;
    }
}
