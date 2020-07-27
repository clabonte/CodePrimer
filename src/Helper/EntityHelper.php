<?php


namespace CodePrimer\Helper;

use CodePrimer\Model\Entity;
use CodePrimer\Model\Field;
use Doctrine\Common\Inflector\Inflector;

class EntityHelper
{
    /**
     * Returns the name of the Repository class associated with a given entity
     * @param Entity $entity
     * @return string
     */
    public function getRepositoryClass(Entity $entity): string
    {
        return Inflector::classify($entity->getName()).'Repository';
    }

    /**
     * Retrieves the field used to automatically tracks the entity creation timestamp, if any
     * @param Entity $entity
     * @return Field|null
     */
    public function getEntityCreatedTimestampField(Entity $entity): ?Field
    {
        $result = null;

        $fieldHelper = new FieldHelper();

        foreach ($entity->getManagedFields() as $field) {
            if ($fieldHelper->isEntityCreatedTimestamp($field)) {
                $result = $field;
                break;
            }
        }

        return $result;
    }

    /**
     * Retrieves the field used to automatically tracks the entity last update timestamp, if any
     * @param Entity $entity
     * @return Field|null
     */
    public function getEntityUpdatedTimestampField(Entity $entity): ?Field
    {
        $result = null;

        $fieldHelper = new FieldHelper();

        foreach ($entity->getManagedFields() as $field) {
            if ($fieldHelper->isEntityUpdatedTimestamp($field)) {
                $result = $field;
                break;
            }
        }

        return $result;
    }

    public function isManagedTimestamp(Entity $entity): bool
    {
        $result = false;

        $fieldHelper = new FieldHelper();

        foreach ($entity->getManagedFields() as $field) {
            if ($fieldHelper->isEntityCreatedTimestamp($field) || $fieldHelper->isEntityUpdatedTimestamp($field)) {
                $result = true;
                break;
            }
        }

        return $result;
    }

    /**
     * Retrieves the list of entities that are linked to a given entity.
     * @param Entity $entity
     * @return Entity[]
     */
    public function getLinkedEntities(Entity $entity) : array
    {
        $entities = [];

        foreach ($entity->getRelations() as $relation) {
            $remoteEntity = $relation->getRemoteSide()->getEntity();
            if (!in_array($remoteEntity, $entities)) {
                $entities[] = $remoteEntity;
            }
        }

        return $entities;
    }
}
