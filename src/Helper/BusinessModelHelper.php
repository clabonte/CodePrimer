<?php

namespace CodePrimer\Helper;

use CodePrimer\Model\BusinessModel;
use CodePrimer\Model\Field;
use Doctrine\Common\Inflector\Inflector;

class BusinessModelHelper
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
}
