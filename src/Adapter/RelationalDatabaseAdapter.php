<?php


namespace CodePrimer\Adapter;

use CodePrimer\Helper\FieldHelper;
use CodePrimer\Helper\FieldType;
use CodePrimer\Model\Database\Index;
use CodePrimer\Model\Entity;
use CodePrimer\Model\Field;
use CodePrimer\Model\Package;
use CodePrimer\Model\Relationship;
use CodePrimer\Model\RelationshipSide;
use Doctrine\Common\Inflector\Inflector;

class RelationalDatabaseAdapter extends DatabaseAdapter
{
    /**
     * This function scans a package and add all the missing fields required to adapt the entities for a
     * relational database
     * @param Package $package
     * @param string $identifierType The type to use for identifiers, one of FieldType:UUID or FieldType::ID
     */
    public function generateRelationalFields(Package $package, string $identifierType = FieldType::UUID)
    {
        // Start by adding missing identifiers for the various entities
        foreach ($package->getEntities() as $entity) {
            if ($entity->getIdentifier() === null) {
                $this->generateIdentifierField($entity, $identifierType);
            }
        }

        // Add all missing foreign key fields to allow ORMs to work as expected
        foreach ($package->getEntities() as $entity) {
            foreach ($entity->getFields() as $field) {
                $relation = $field->getRelation();
                if ($relation !== null) {
                    if (($relation->getRelationship()->getType() == Relationship::ONE_TO_MANY) &&
                        ($relation->getRemoteSide()->getField() === null)) {
                        $newField = $this->generateForeignKeyField($relation->getRemoteSide()->getEntity(), $entity);
                        $relation->getRemoteSide()->setField($newField);
                    }
                }
            }
        }
    }

    public function generateIdentifierField(Entity $entity, string $identifierType)
    {
        $name = 'id';
        if ($entity->getField($name) !== null) {
            $name = Inflector::camelize($entity->getName()) . 'Id';
        }
        if ($entity->getField($name) !== null) {
            throw new \RuntimeException('Cannot generate ID field for entity '.$entity->getName().': "id" and "'.$name .'" fields are already defined. Did you forget to specify an identifier for this entity?');
        }

        $field = new Field($name, $identifierType, 'DB unique identifier field');
        $field->setMandatory(true)
            ->setManaged(true)
            ->setGenerated(true);
        $entity->addField($field);
    }

    /**
     * Extracts the name of a field and transforms it to its column name equivalent.
     * Converts 'fieldName', 'field-name' and 'field name' to 'field_name'
     * or 'field_name_id' if the field represents a foreign key.
     *
     * @param Field $field
     * @return string
     */
    public function getColumnName(Field $field): string
    {
        $name = parent::getColumnName($field);
        if (($field->getRelation() !== null) && ($this->isValidForeignKey($field->getRelation()))) {
            if (substr($name, -2) !== 'id') {
                $name .= '_id';
            }
        }

        return $name;
    }

    /**
     * Checks if a relationship side can be mapped to a foreign key
     * @param RelationshipSide $relationshipSide
     * @return bool
     */
    public function isValidForeignKey(RelationshipSide $relationshipSide): bool
    {
        $result = true;

        switch ($relationshipSide->getRelationship()->getType()) {
            case Relationship::MANY_TO_MANY:
                $result = false;
                break;
            case Relationship::ONE_TO_MANY:
                if ($relationshipSide->getSide() == RelationshipSide::LEFT) {
                    $result = false;
                }
                break;
        }

        return $result;
    }

    public function generateForeignKeyField(Entity $entity, Entity $foreignEntity): Field
    {
        $foreignIdField = $foreignEntity->getIdentifier();
        if ($foreignIdField === null) {
            throw new \RuntimeException('No identifier available for foreign entity '.$foreignEntity->getName());
        }

        $name = Inflector::camelize($foreignEntity->getName());
        if ($entity->getField($name) !== null) {
            throw new \RuntimeException('Cannot generate foreign key field on entity '.$entity->getName().': Field "'.$name .'" field is already defined. Did you provide the right type for this field?');
        }
        $field = new Field($name, $foreignEntity->getName(), 'Foreign relationship field');
        $field->setGenerated(true);
        $entity->addField($field);

        return $field;
    }

    /**
     * Extracts the database indexes to create for a given entity
     * @param Entity $entity
     * @return Index[]
     */
    public function getIndexes(Entity $entity): array
    {
        $indexes = [];

        $fields = $entity->getSearchableFields();
        foreach ($fields as $field) {
            $indexes[] = $this->createIndex($field, 'To optimize search queries');
        }
        foreach ($entity->getRelations() as $relation) {
            if ($this->isValidForeignKey($relation)) {
                $indexes[] = $this->createIndex($relation->getField(), $relation->getRemoteSide()->getEntity()->getName() . ' foreign key');
            }
        }
        return $indexes;
    }

    protected function createIndex(Field $field, string $description): Index
    {
        $name = $this->getColumnName($field) . '_idx';

        $index = new Index($name, [$field]);
        $index->setDescription($description);

        return $index;
    }

    /**
     * Retrieves the list of fields from an entity that must be stored in a relational database table associated with
     * this entity.
     * @param Entity $entity
     * @return Field[]
     */
    public function getDatabaseFields(Entity $entity): array
    {
        $fields = [];

        foreach ($entity->getFields() as $field) {
            if (!$field->isList()) {
                $fields[] = $field;
            }
        }

        return $fields;
    }

    /**
     * Retrieves the list of fields from an entity that must be checked when an audited entity is modified.
     * @param Entity $entity
     * @param bool $includeId
     * @return Field[]
     */
    public function getAuditedFields(Entity $entity, bool $includeId = true): array
    {
        $fields = [];

        $fieldHelper = new FieldHelper();

        foreach ($entity->getFields() as $field) {
            $audited = true;

            if ($field->isGenerated() && ($field !== $entity->getIdentifier())) {
                $audited = false;
            } elseif ($field->isList()) {
                $audited = false;
            } elseif ($fieldHelper->isEntityCreatedTimestamp($field)) {
                $audited = false;
            } elseif ($fieldHelper->isEntityUpdatedTimestamp($field)) {
                $audited = false;
            }

            if (!$includeId && ($field === $entity->getIdentifier())) {
                $audited = false;
            }

            if ($audited) {
                $fields[] = $field;
            }
        }

        return $fields;
    }
}
