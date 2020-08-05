<?php

namespace CodePrimer\Twig;

use CodePrimer\Helper\FieldType;
use CodePrimer\Model\Database\Index;
use CodePrimer\Model\Field;
use RuntimeException;
use Twig\TwigFilter;

class MySqlTwigExtension extends SqlTwigExtension
{
    public function getFilters(): array
    {
        $filters = parent::getFilters();

        $filters[] = new TwigFilter('attributes', [$this, 'attributesFilter'], ['is_safe' => ['html']]);

        return $filters;
    }

    /**
     * Formats the MySQL attributes of a field or index.
     * Attributes are set based on the format defined at: https://dev.mysql.com/doc/refman/8.0/en/create-table.html.
     *
     * @param $obj
     */
    public function attributesFilter($obj): string
    {
        if ($obj instanceof Field) {
            return $this->fieldAttributesFilter($obj);
        } elseif ($obj instanceof Index) {
            return $this->indexAttributesFilter($obj);
        }
        throw new RuntimeException('Attributes can only generated for Field or Index instances');
    }

    private function indexAttributesFilter(Index $index): string
    {
        $value = '';

        // [COMMENT 'string']
        if (!empty($index->getDescription())) {
            $value .= "COMMENT '".str_replace("'", "''", $index->getDescription())."'";
        }

        return $value;
    }

    /**
     * Formats the MySQL attributes of a field.
     * Attributes are set based on the format defined at: https://dev.mysql.com/doc/refman/8.0/en/create-table.html.
     */
    private function fieldAttributesFilter(Field $field): string
    {
        $value = '';

        // [NOT NULL | NULL]
        if ($field->isMandatory()) {
            $value .= ' NOT NULL';
        } else {
            $value .= ' NULL';
        }

        // [DEFAULT {literal | (expr)} ]
        $default = $field->getDefault();
        if ($this->fieldHelper->isBusinessModelCreatedTimestamp($field)) {
            $value .= ' DEFAULT CURRENT_TIMESTAMP';
        } elseif ($this->fieldHelper->isBusinessModelUpdatedTimestamp($field)) {
            $value .= ' DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP';
        } elseif (!empty($default)) {
            if ($this->fieldHelper->isString($field)) {
                $value .= " DEFAULT '$default'";
            } else {
                $value .= ' DEFAULT '.$default;
            }
        } elseif (!$field->isMandatory()) {
            $value .= ' DEFAULT NULL';
        }

        // [AUTO_INCREMENT]
        if ($this->fieldHelper->isAutoIncrement($field)) {
            $value .= ' AUTO_INCREMENT';
        }

        // [UNIQUE [KEY]] -- DEFINED OUTSIDE OF A COLUMN DEFINITION
        // [[PRIMARY] KEY] -- DEFINED OUTSIDE OF A COLUMN DEFINITION

        // [COMMENT 'string']
        if (!empty($field->getDescription())) {
            $value .= " COMMENT '".str_replace("'", "''", $field->getDescription())."'";
        }

        // [COLLATE collation_name]
        if ($this->fieldHelper->isAsciiString($field)) {
            $value .= ' COLLATE ascii_general_ci';
        } elseif ((null !== $field->getRelation()) && $this->databaseAdapter->isValidForeignKey($field->getRelation())) {
            $primaryKey = $field->getRelation()->getRemoteSide()->getBusinessModel()->getIdentifier();
            if ($this->fieldHelper->isAsciiString($primaryKey)) {
                $value .= ' COLLATE ascii_general_ci';
            }
        }

        // [COLUMN_FORMAT {FIXED|DYNAMIC|DEFAULT}] -- NOT SUPPORTED YET
        // [STORAGE {DISK|MEMORY}] -- NOT SUPPORTED YET
        return trim($value);
    }

    public function typeFilter(array $context, $field, bool $mandatory = false): string
    {
        $helper = $this->fieldHelper;

        $type = '';
        if ($field instanceof Field) {
            if ($field->isList()) {
                throw new RuntimeException('List types are not implemented yet for MySQL');
            }
            if ($helper->isBoolean($field)) {
                $type = 'TINYINT(1)';
            } elseif ($helper->isDate($field)) {
                $type = 'DATE';
            } elseif ($helper->isTime($field)) {
                $type = 'TIME';
            } elseif ($helper->isDateTime($field)) {
                $type = 'DATETIME';
            } elseif ($helper->isInteger($field)) {
                $type = 'INT';
            } elseif ($helper->isLong($field)) {
                $type = 'BIGINT';
            } elseif ($helper->isFloat($field)) {
                $type = 'FLOAT';
            } elseif ($helper->isPrice($field)) {
                $type = 'DECIMAL(12,2)';
            } elseif ($helper->isDouble($field)) {
                switch (strtolower($field->getType())) {
                    case FieldType::DECIMAL:
                        $type = 'DECIMAL(14,4)';
                        break;
                    default:
                        $type = 'DOUBLE';
                }
            } elseif ($helper->isString($field)) {
                switch (strtolower($field->getType())) {
                    case FieldType::PHONE:
                        $type = 'CHAR(15)';
                        break;
                    case FieldType::UUID:
                        $type = 'CHAR(36)';
                        break;
                    case FieldType::TEXT:
                        $type = 'LONGTEXT';
                        break;
                    default:
                        $type = 'VARCHAR(255)';
                        break;
                }
            } elseif ((null !== $field->getRelation()) && $this->databaseAdapter->isValidForeignKey($field->getRelation())) {
                $primaryKey = $field->getRelation()->getRemoteSide()->getBusinessModel()->getIdentifier();
                $type = $this->typeFilter($context, $primaryKey, $mandatory);
            } else {
                throw new RuntimeException("Support for type {$field->getType()} is not implemented yet for MySQL");
            }
        } else {
            throw new RuntimeException('MySqlTwigExtension only map types for Field objects');
        }

        return $type;
    }

    public function columnFilter($obj): string
    {
        if ($obj instanceof Index) {
            // MySQL has some limits on the size of indexes for varchar columns, let's handle it properly
            $columns = [];
            foreach ($obj->getFields() as $field) {
                $size = null;

                if ($this->fieldHelper->isString($field)) {
                    switch (strtolower($field->getType())) {
                        case FieldType::PHONE:
                        case FieldType::UUID:
                        case FieldType::TEXT:
                            break;
                        default:
                            $size = 20; // Limit the index to the first 20 characters
                            break;
                    }
                }
                $column = $this->databaseAdapter->getColumnName($field);
                if (isset($size)) {
                    $columns[] = $column."($size)";
                } else {
                    $columns[] = $column;
                }
            }

            return implode(',', $columns);
        } else {
            return parent::columnFilter($obj);
        }
    }
}
