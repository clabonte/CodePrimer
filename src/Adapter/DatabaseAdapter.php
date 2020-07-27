<?php


namespace CodePrimer\Adapter;

use CodePrimer\Model\Database\Index;
use CodePrimer\Model\Entity;
use CodePrimer\Model\Field;
use CodePrimer\Model\Package;
use CodePrimer\Model\Relationship;
use CodePrimer\Model\RelationshipSide;
use Doctrine\Common\Inflector\Inflector;
use RuntimeException;

class DatabaseAdapter
{
    /**
     * Extracts the name of Package and transforms it to its database name equivalent.
     * Converts 'dbName', 'db-name' and 'db name' to 'db_name'.
     *
     * @param Package $package
     * @return string
     */
    public function getDatabaseName(Package $package)
    {
        $dbName = $package->getNamespace() . ' ' . $package->getName();
        $name = str_replace(['-', ' ', '.'], '_', Inflector::singularize($dbName));
        $name = Inflector::tableize($name);
        $name = str_replace('__', '_', $name);

        return $name;
    }

    /**
     * Extracts the name of an entity and transforms it to its table name equivalent.
     * Converts 'tableName', 'table-name' and 'table name' to 'table_names'.
     *
     * @param Entity $entity
     * @return string
     */
    public function getTableName(Entity $entity): string
    {
        $name = str_replace(['-', ' ', '.'], '_', Inflector::pluralize($entity->getName()));
        $name = Inflector::tableize($name);
        $name = str_replace('__', '_', $name);

        return $name;
    }

    /**
     * Extracts the name of a relation table based on the entities that are part of the relation
     *
     * @param RelationshipSide $relation
     * @return string
     */
    public function getRelationTableName(RelationshipSide $relation): string
    {
        if ($relation->getRelationship()->getType() != Relationship::MANY_TO_MANY) {
            throw new RuntimeException("Relation tables can only be created for many-to-many relationships");
        }
        $leftSide = $relation->getRelationship()->getLeftSide();
        $rightSide = $relation->getRelationship()->getRightSide();

        return $this->getTableName($leftSide->getEntity()) . '_' . $this->getTableName($rightSide->getEntity());
    }

    /**
     * Extracts the name of an entity and transforms it to its audit table name equivalent.
     * Converts 'tableName', 'table-name' and 'table name' to 'table_names_logs'.
     *
     * @param Entity $entity
     * @return string
     */
    public function getAuditTableName(Entity $entity): string
    {
        return $this->getTableName($entity) . '_logs';
    }

    /**
     * Extracts the name of a field and transforms it to its column name equivalent.
     * Converts 'fieldName', 'field-name' and 'field name' to 'field_name'.
     *
     * @param Field $field
     * @return string
     */
    public function getColumnName(Field $field): string
    {
        $name = str_replace(['-', ' ', '.'], '_', $field->getName());
        $name = Inflector::tableize($name);
        $name = str_replace('__', '_', $name);

        return $name;
    }

    /**
     * Extracts the name to use as a column to represent an entity
     * @param Entity $entity
     * @return string
     */
    public function getEntityColumnName(Entity $entity): string
    {
        $name = str_replace(['-', ' ', '.'], '_', $entity->getName());
        $name = Inflector::tableize($name) . '_id';
        $name = str_replace('__', '_', $name);

        return $name;
    }
}
