<?php

namespace CodePrimer\Helper;

use CodePrimer\Model\BusinessModel;
use CodePrimer\Model\Field;
use Doctrine\Common\Inflector\Inflector;

class EntityHelper
{
    /**
     * Returns the name of the Repository class associated with a given entity.
     */
    public function getRepositoryClass(BusinessModel $businessModel): string
    {
        return Inflector::classify($businessModel->getName()).'Repository';
    }

    /**
     * Retrieves the field used to automatically tracks the entity creation timestamp, if any.
     */
    public function getEntityCreatedTimestampField(BusinessModel $businessModel): ?Field
    {
        $result = null;

        $fieldHelper = new FieldHelper();

        foreach ($businessModel->getManagedFields() as $field) {
            if ($fieldHelper->isEntityCreatedTimestamp($field)) {
                $result = $field;
                break;
            }
        }

        return $result;
    }

    /**
     * Retrieves the field used to automatically tracks the entity last update timestamp, if any.
     */
    public function getEntityUpdatedTimestampField(BusinessModel $businessModel): ?Field
    {
        $result = null;

        $fieldHelper = new FieldHelper();

        foreach ($businessModel->getManagedFields() as $field) {
            if ($fieldHelper->isEntityUpdatedTimestamp($field)) {
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
            if ($fieldHelper->isEntityCreatedTimestamp($field) || $fieldHelper->isEntityUpdatedTimestamp($field)) {
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
    public function getLinkedEntities(BusinessModel $businessModel): array
    {
        $entities = [];

        foreach ($businessModel->getRelations() as $relation) {
            $remoteEntity = $relation->getRemoteSide()->getEntity();
            if (!in_array($remoteEntity, $entities)) {
                $entities[] = $remoteEntity;
            }
        }

        return $entities;
    }
}
