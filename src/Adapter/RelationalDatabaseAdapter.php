<?php

namespace CodePrimer\Adapter;

use CodePrimer\Helper\FieldHelper;
use CodePrimer\Helper\FieldType;
use CodePrimer\Model\BusinessBundle;
use CodePrimer\Model\BusinessModel;
use CodePrimer\Model\Database\Index;
use CodePrimer\Model\Dataset;
use CodePrimer\Model\Field;
use CodePrimer\Model\Relationship;
use CodePrimer\Model\RelationshipSide;

class RelationalDatabaseAdapter extends DatabaseAdapter
{
    /**
     * This function scans a package and add all the missing fields required to adapt the entities for a
     * relational database.
     *
     * @param string $identifierType The type to use for identifiers, one of FieldType:UUID or FieldType::ID
     */
    public function generateRelationalFields(BusinessBundle $businessBundle, string $identifierType = FieldType::UUID)
    {
        // Start by adding missing identifiers for the various entities
        foreach ($businessBundle->getBusinessModels() as $businessModel) {
            if (null === $businessModel->getIdentifier()) {
                $this->generateIdentifierField($businessModel, $identifierType);
            }
        }

        // Add all missing foreign key fields to allow ORMs to work as expected
        foreach ($businessBundle->getBusinessModels() as $businessModel) {
            foreach ($businessModel->getFields() as $field) {
                $relation = $field->getRelation();
                if (null !== $relation) {
                    if ((Relationship::ONE_TO_MANY == $relation->getRelationship()->getType()) &&
                        (null === $relation->getRemoteSide()->getField())) {
                        $newField = $this->generateForeignKeyField($relation->getRemoteSide()->getBusinessModel(), $businessModel);
                        $relation->getRemoteSide()->setField($newField);
                    }
                }
            }
        }
    }

    public function generateIdentifierField(BusinessModel $businessModel, string $identifierType)
    {
        $name = 'id';
        if (null !== $businessModel->getField($name)) {
            $name = $this->inflector->camelize($businessModel->getName()).'Id';
        }
        if (null !== $businessModel->getField($name)) {
            throw new \RuntimeException('Cannot generate ID field for entity '.$businessModel->getName().': "id" and "'.$name.'" fields are already defined. Did you forget to specify an identifier for this entity?');
        }

        $field = new Field($name, $identifierType, 'DB unique identifier field');
        $field->setMandatory(true)
            ->setManaged(true)
            ->setGenerated(true)
            ->setIdentifier(true);
        $businessModel->addField($field);
    }

    /**
     * Extracts the name of a field and transforms it to its column name equivalent.
     * Converts 'fieldName', 'field-name' and 'field name' to 'field_name'
     * or 'field_name_id' if the field represents a foreign key.
     */
    public function getColumnName(Field $field): string
    {
        $name = parent::getColumnName($field);
        if ((null !== $field->getRelation()) && ($this->isValidForeignKey($field->getRelation()))) {
            if ('id' !== substr($name, -2)) {
                $name .= '_id';
            }
        }

        return $name;
    }

    /**
     * Checks if a relationship side can be mapped to a foreign key.
     */
    public function isValidForeignKey(RelationshipSide $relationshipSide): bool
    {
        $result = true;

        switch ($relationshipSide->getRelationship()->getType()) {
            case Relationship::MANY_TO_MANY:
                $result = false;
                break;
            case Relationship::ONE_TO_MANY:
                if (RelationshipSide::LEFT == $relationshipSide->getSide()) {
                    $result = false;
                }
                break;
        }

        return $result;
    }

    public function generateForeignKeyField(BusinessModel $businessModel, BusinessModel $foreignModel): Field
    {
        $foreignIdField = $foreignModel->getIdentifier();
        if (null === $foreignIdField) {
            throw new \RuntimeException('No identifier available for foreign entity '.$foreignModel->getName());
        }

        $name = $this->inflector->camelize($foreignModel->getName());
        if (null !== $businessModel->getField($name)) {
            throw new \RuntimeException('Cannot generate foreign key field on entity '.$businessModel->getName().': Field "'.$name.'" field is already defined. Did you provide the right type for this field?');
        }
        $field = new Field($name, $foreignModel->getName(), 'Foreign relationship field');
        $field->setGenerated(true);
        $businessModel->addField($field);

        return $field;
    }

    /**
     * Extracts the database indexes to create for a given entity.
     *
     * @return Index[]
     */
    public function getBusinessModelIndexes(BusinessModel $businessModel): array
    {
        $indexes = [];

        $fields = $businessModel->getSearchableFields();
        foreach ($fields as $field) {
            $indexes[] = $this->createIndex($field, 'To optimize search queries');
        }
        foreach ($businessModel->getRelations() as $relation) {
            if ($this->isValidForeignKey($relation)) {
                $indexes[] = $this->createIndex($relation->getField(), $relation->getRemoteSide()->getBusinessModel()->getName().' foreign key');
            }
        }

        return $indexes;
    }

    protected function createIndex(Field $field, string $description): Index
    {
        $name = $this->getColumnName($field).'_idx';

        $index = new Index($name, [$field]);
        $index->setDescription($description);

        return $index;
    }

    /**
     * Retrieves the list of fields from an entity that must be stored in a relational database table associated with
     * this entity.
     *
     * @return Field[]
     */
    public function getDatabaseFields(BusinessModel $businessModel): array
    {
        $fields = [];

        foreach ($businessModel->getFields() as $field) {
            if (!$field->isList()) {
                $fields[] = $field;
            }
        }

        return $fields;
    }

    /**
     * Retrieves the list of fields from an entity that must be stored in a relational database table associated with
     * this dataset.
     *
     * @return Field[]
     */
    public function getDatasetDatabaseFields(Dataset $dataset): array
    {
        $fields = [];

        foreach ($dataset->getFields() as $field) {
            if (!$field->isList()) {
                $fields[] = $field;
            }
        }

        return $fields;
    }

    /**
     * Retrieves the list of fields from an entity that must be checked when an audited entity is modified.
     *
     * @return Field[]
     */
    public function getAuditedFields(BusinessModel $businessModel, bool $includeId = true): array
    {
        $fields = [];

        $fieldHelper = new FieldHelper();

        foreach ($businessModel->getFields() as $field) {
            $audited = true;

            if ($field->isGenerated() && ($field !== $businessModel->getIdentifier())) {
                $audited = false;
            } elseif ($field->isList()) {
                $audited = false;
            } elseif ($fieldHelper->isBusinessModelCreatedTimestamp($field)) {
                $audited = false;
            } elseif ($fieldHelper->isBusinessModelUpdatedTimestamp($field)) {
                $audited = false;
            }

            if (!$includeId && ($field === $businessModel->getIdentifier())) {
                $audited = false;
            }

            if ($audited) {
                $fields[] = $field;
            }
        }

        return $fields;
    }
}
