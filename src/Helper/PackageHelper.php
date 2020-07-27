<?php


namespace CodePrimer\Helper;


use CodePrimer\Model\Entity;
use CodePrimer\Model\Field;
use CodePrimer\Model\Package;
use CodePrimer\Model\Relationship;
use CodePrimer\Model\RelationshipSide;
use RuntimeException;

class PackageHelper
{
    /** @var FieldHelper */
    private $fieldHelper;

    public function __construct(FieldHelper $fieldHelper = null)
    {
        if ($fieldHelper === null) {
            $this->fieldHelper = new FieldHelper();
        } else {
            $this->fieldHelper = $fieldHelper;
        }
    }

    /**
     * This method is used to create the relationships between the various entities in a package
     * @param Package $package
     */
    public function buildRelationships(Package $package)
    {
        foreach ($package->getEntities() as $entity) {
            foreach ($entity->getFields() as $field) {
                if ($this->fieldHelper->isEntity($field, $package)) {
                    if ($field->getRelation() === null) {
                        $this->createRelationship($package, $entity, $field);
                    }
                }
            }
        }
    }

    /**
     * Creates the relationship between 2 entities
     * @param Package $package
     * @param Entity $entity
     * @param Field $field
     * @return Relationship
     */
    private function createRelationship(Package $package, Entity $entity, Field $field): Relationship
    {
        $remoteEntity = $package->getEntity($field->getType());

        // Make sure the remote entity exists
        if ($remoteEntity === null) {
            throw new RuntimeException('Failed to locate remote entity '.$field->getType(). ' in package '.$package->getName());
        }

        // Look for fields to link back
        $possibleFields = [];
        foreach ($remoteEntity->getFields() as $remoteField) {
            if ($remoteField->getType() == $entity->getName()) {
                $possibleFields[] = $remoteField;
            }
        }

        if (empty($possibleFields)) {
            // Unidirectional relationship
            $leftSide = new RelationshipSide($entity, $field);
            $rightSide = new RelationshipSide($remoteEntity);
            $type = Relationship::ONE_TO_ONE;
            if ($field->isList()) {
                $type = Relationship::ONE_TO_MANY;
            }
            return new Relationship($type, $leftSide, $rightSide);
        }

        if (count($possibleFields) == 1) {
            // Birectional relationship, we need to figure out the relationship type and appropriate sides

            /** @var Field $remoteField */
            $remoteField = $possibleFields[0];

            $firstSide = new RelationshipSide($entity, $field);
            $secondSide = new RelationshipSide($remoteEntity, $remoteField);

            // In a one to one or many to many, it is hard to figure out which side is the left one (i.e. master)
            // vs the right one (slave)
            // TODO Find a way to make this deterministic, likely via new properties to the entity class
            $type = Relationship::ONE_TO_ONE;
            $leftSide = $firstSide;
            $rightSide = $secondSide;

            if ($field->isList() && $remoteField->isList()) {
                $type = Relationship::MANY_TO_MANY;
            } elseif ($field->isList()) {
                $type = Relationship::ONE_TO_MANY;
            } elseif ($remoteField->isList()) {
                $type = Relationship::ONE_TO_MANY;
                $leftSide = $secondSide;
                $rightSide = $firstSide;
            }

            return new Relationship($type, $leftSide, $rightSide);
        }

        throw new RuntimeException('Multiple bidirectional relationships found between the same entities: '. $entity->getName() .' and '. $remoteEntity->getName(). '. This is not supported yet');
    }
}
