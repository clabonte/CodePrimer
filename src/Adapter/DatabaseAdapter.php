<?php

namespace CodePrimer\Adapter;

use CodePrimer\Model\BusinessModel;
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
     * @return string
     */
    public function getDatabaseName(Package $package)
    {
        $dbName = $package->getNamespace().' '.$package->getName();
        $name = str_replace(['-', ' ', '.'], '_', Inflector::singularize($dbName));
        $name = Inflector::tableize($name);
        $name = str_replace('__', '_', $name);

        return $name;
    }

    /**
     * Extracts the name of an  business model and transforms it to its table name equivalent.
     * Converts 'tableName', 'table-name' and 'table name' to 'table_names'.
     */
    public function getTableName(BusinessModel $businessModel): string
    {
        $name = str_replace(['-', ' ', '.'], '_', Inflector::pluralize($businessModel->getName()));
        $name = Inflector::tableize($name);
        $name = str_replace('__', '_', $name);

        return $name;
    }

    /**
     * Extracts the name of a relation table based on the entities that are part of the relation.
     */
    public function getRelationTableName(RelationshipSide $relation): string
    {
        if (Relationship::MANY_TO_MANY != $relation->getRelationship()->getType()) {
            throw new RuntimeException('Relation tables can only be created for many-to-many relationships');
        }
        $leftSide = $relation->getRelationship()->getLeftSide();
        $rightSide = $relation->getRelationship()->getRightSide();

        return $this->getTableName($leftSide->getBusinessModel()).'_'.$this->getTableName($rightSide->getBusinessModel());
    }

    /**
     * Extracts the name of an  business model and transforms it to its audit table name equivalent.
     * Converts 'tableName', 'table-name' and 'table name' to 'table_names_logs'.
     */
    public function getAuditTableName(BusinessModel $businessModel): string
    {
        return $this->getTableName($businessModel).'_logs';
    }

    /**
     * Extracts the name of a field and transforms it to its column name equivalent.
     * Converts 'fieldName', 'field-name' and 'field name' to 'field_name'.
     */
    public function getColumnName(Field $field): string
    {
        $name = str_replace(['-', ' ', '.'], '_', $field->getName());
        $name = Inflector::tableize($name);
        $name = str_replace('__', '_', $name);

        return $name;
    }

    /**
     * Extracts the name to use as a column to represent an  business model.
     */
    public function getBusinessModelColumnName(BusinessModel $businessModel): string
    {
        $name = str_replace(['-', ' ', '.'], '_', $businessModel->getName());
        $name = Inflector::tableize($name).'_id';
        $name = str_replace('__', '_', $name);

        return $name;
    }
}
