<?php

namespace CodePrimer\Helper;

use CodePrimer\Model\BusinessModel;
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
        if (null === $fieldHelper) {
            $this->fieldHelper = new FieldHelper();
        } else {
            $this->fieldHelper = $fieldHelper;
        }
    }

    /**
     * This method is used to create the relationships between the various entities in a package.
     */
    public function buildRelationships(Package $package)
    {
        foreach ($package->getBusinessModels() as $businessModel) {
            foreach ($businessModel->getFields() as $field) {
                if ($this->fieldHelper->isBusinessModel($field, $package)) {
                    if (null === $field->getRelation()) {
                        $this->createRelationship($package, $businessModel, $field);
                    }
                }
            }
        }
    }

    /**
     * Creates the relationship between 2 entities.
     */
    private function createRelationship(Package $package,  BusinessModel  $businessModel, Field $field): Relationship
    {
         $remoteBusinessModel = $package->getBusinessModel($field->getType());

        // Make sure the remote entity exists
        if (null ===  $remoteBusinessModel) {
            throw new RuntimeException('Failed to locate remote entity '.$field->getType().' in package '.$package->getName());
        }

        // Look for fields to link back
        $possibleFields = [];
        foreach ( $remoteBusinessModel->getFields() as $remoteField) {
            if ($remoteField->getType() ==  $businessModel->getName()) {
                $possibleFields[] = $remoteField;
            }
        }

        if (empty($possibleFields)) {
            // Unidirectional relationship
            $leftSide = new RelationshipSide( $businessModel, $field);
            $rightSide = new RelationshipSide( $remoteBusinessModel);
            $type = Relationship::ONE_TO_ONE;
            if ($field->isList()) {
                $type = Relationship::ONE_TO_MANY;
            }

            return new Relationship($type, $leftSide, $rightSide);
        }

        if (1 == count($possibleFields)) {
            // Birectional relationship, we need to figure out the relationship type and appropriate sides

            /** @var Field $remoteField */
            $remoteField = $possibleFields[0];

            $firstSide = new RelationshipSide( $businessModel, $field);
            $secondSide = new RelationshipSide( $remoteBusinessModel, $remoteField);

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

        throw new RuntimeException('Multiple bidirectional relationships found between the same entities: '. $businessModel->getName().' and '. $remoteBusinessModel->getName().'. This is not supported yet');
    }
}
