<?php

namespace CodePrimer\Twig;

use CodePrimer\Adapter\RelationalDatabaseAdapter;
use CodePrimer\Helper\BusinessModelHelper;
use CodePrimer\Helper\FieldHelper;
use CodePrimer\Helper\PriceHelper;
use CodePrimer\Model\BusinessBundle;
use CodePrimer\Model\BusinessModel;
use CodePrimer\Model\Data\Data;
use CodePrimer\Model\Database\Index;
use CodePrimer\Model\Dataset;
use CodePrimer\Model\DatasetElement;
use CodePrimer\Model\Field;
use CodePrimer\Model\RelationshipSide;
use Exception;
use InvalidArgumentException;
use RuntimeException;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\TwigTest;

/**
 * Class SqlTwigExtension.
 */
class SqlTwigExtension extends LanguageTwigExtension
{
    /** @var BusinessModelHelper */
    protected $businessModelHelper;

    /** @var FieldHelper */
    protected $fieldHelper;

    /** @var RelationalDatabaseAdapter */
    protected $databaseAdapter;

    public function __construct()
    {
        parent::__construct();
        $this->businessModelHelper = new BusinessModelHelper();
        $this->fieldHelper = new FieldHelper();
        $this->databaseAdapter = new RelationalDatabaseAdapter();
    }

    public function getFilters(): array
    {
        $filters = parent::getFilters();

        $filters[] = new TwigFilter('database', [$this, 'databaseFilter'], ['is_safe' => ['html']]);
        $filters[] = new TwigFilter('table', [$this, 'tableFilter'], ['is_safe' => ['html']]);
        $filters[] = new TwigFilter('auditTable', [$this, 'auditTableFilter'], ['is_safe' => ['html']]);
        $filters[] = new TwigFilter('column', [$this, 'columnFilter'], ['is_safe' => ['html']]);
        $filters[] = new TwigFilter('foreignKey', [$this, 'foreignKeyFilter'], ['is_safe' => ['html']]);
        $filters[] = new TwigFilter('user', [$this, 'userFilter'], ['is_safe' => ['html']]);
        $filters[] = new TwigFilter('value', [$this, 'valueFilter'], ['is_safe' => ['html'], 'needs_context' => true]);

        return $filters;
    }

    public function getFunctions()
    {
        $functions = parent::getFunctions();

        $functions[] = new TwigFunction('databaseFields', [$this, 'databaseFieldsFunction'], ['is_safe' => ['html']]);
        $functions[] = new TwigFunction('auditedFields', [$this, 'auditedFieldsFunction'], ['is_safe' => ['html']]);
        $functions[] = new TwigFunction('indexes', [$this, 'indexesFunction'], ['is_safe' => ['html']]);

        return $functions;
    }

    public function getTests()
    {
        $tests = parent::getTests();

        $tests[] = new TwigTest('foreignKey', [$this, 'foreignKeyTest', ['is_safe' => ['html']]]);

        return $tests;
    }

    /**
     * Returns the database name associated with a Package.
     */
    public function databaseFilter(BusinessBundle $businessBundle): string
    {
        return $this->databaseAdapter->getDatabaseName($businessBundle);
    }

    /**
     * Returns the table name associated with an BusinessModel or a RelationshipSide.
     *
     * @param $obj
     */
    public function tableFilter($obj): string
    {
        if ($obj instanceof BusinessModel) {
            return $this->databaseAdapter->getTableName($obj);
        } elseif ($obj instanceof RelationshipSide) {
            return $this->databaseAdapter->getRelationTableName($obj);
        } elseif ($obj instanceof Dataset) {
            return $this->databaseAdapter->getTableName($obj);
        }
        throw new RuntimeException('Table names can only generated for BusinessModel, Dataset or RelationshipSide instances');
    }

    /**
     * Returns the audit table name associated with an BusinessModel.
     */
    public function auditTableFilter(BusinessModel $businessModel): string
    {
        return $this->databaseAdapter->getAuditTableName($businessModel);
    }

    /**
     * Returns the column name associated with a Field.
     *
     * @param $obj
     */
    public function columnFilter($obj): string
    {
        if ($obj instanceof Field) {
            return $this->databaseAdapter->getColumnName($obj);
        } elseif ($obj instanceof BusinessModel) {
            return $this->databaseAdapter->getBusinessModelColumnName($obj);
        } elseif ($obj instanceof Index) {
            $columns = [];
            foreach ($obj->getFields() as $field) {
                $columns[] = $this->databaseAdapter->getColumnName($field);
            }

            return implode(', ', $columns);
        } elseif (is_array($obj)) {
            $columns = [];
            foreach ($obj as $item) {
                $columns[] = '`'.$this->columnFilter($item).'`';
            }

            return implode(', ', $columns);
        }
        throw new RuntimeException('Column names can only generated for Field, BusinessModel or Index instances. Received: '.null === $obj ? 'null' : get_class($obj));
    }

    /**
     * Returns the name to use as a foreign key for a given relationship side.
     *
     * @throws Exception If the Relationship is either a Many-To-Many or the left side of a One-To-Many
     */
    public function foreignKeyFilter($source, Field $sourceField = null, Dataset $destination = null): string
    {
        if ($source instanceof RelationshipSide) {
            if (!$this->databaseAdapter->isValidForeignKey($source)) {
                throw new Exception('foreignKey filter can only be used against a One-To-One or the right side of a One-To-Many relationship');
            }

            // Format: fk_[referencing table name]_[referenced table name](_[referencing field name])
            $remoteSide = $source->getRemoteSide();
            $result = 'fk_'.$this->tableFilter($source->getBusinessModel()).'_'.$this->tableFilter($remoteSide->getBusinessModel()).'_'.$this->columnFilter($source->getField());
        } elseif ($source instanceof BusinessModel) {
            if ((null === $sourceField) || (null === $destination)) {
                throw new Exception('foreignKey filter on a BusinessModel object must provide a valid sourceField and destination values');
            }
            $result = 'fk_'.$this->tableFilter($source).'_'.$this->tableFilter($destination).'_'.$this->columnFilter($sourceField);
        } else {
            throw new Exception('foreignKey filter only accepts RelationshipSide and BusinessModel sources');
        }

        return $result;
    }

    /**
     * Returns the user name to use for connecting to the database based on a given Package.
     */
    public function userFilter(BusinessBundle $businessBundle): string
    {
        $name = str_replace(['-', ' ', '.'], '_', $businessBundle->getName());
        $name = $this->inflector->tableize($name);
        $name = str_replace('__', '_', $name);

        return $name;
    }

    /**
     * Format a value in its PHP representation based on the Field this value is associated with.
     *
     * @param Field|Data|DatasetElement $obj   The object associated with the value
     * @param mixed                     $value The value to format
     */
    public function valueFilter(array $context, $obj, $value = null): string
    {
        $helper = new FieldHelper();

        if ($obj instanceof DatasetElement) {
            $values = [];
            foreach ($obj->getValues() as $name => $value) {
                $field = $obj->getDataset()->getField($name);
                $values[] = $this->valueFilter($context, $field, $value);
            }

            return implode(', ', $values);
        }
        if ($obj instanceof Data) {
            $field = $obj->getField();
        } else {
            $field = $obj;
        }

        if ($field instanceof Field) {
            if ($helper->isBoolean($field)) {
                $bool = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                if ($bool) {
                    $result = 'TRUE';
                } else {
                    $result = 'FALSE';
                }
            } elseif ($helper->isDate($field)) {
                if ($value instanceof \DateTimeInterface) {
                    $result = "'{$value->format('Y-m-d')}'";
                } else {
                    $result = "'$value'";
                }
            } elseif ($helper->isTime($field)) {
                if ($value instanceof \DateTimeInterface) {
                    $result = "'{$value->format('H:i:s')}'";
                } else {
                    $result = "'$value'";
                }
            } elseif ($helper->isDateTime($field)) {
                if ($value instanceof \DateTimeInterface) {
                    $result = "'{$value->format('Y-m-d H:i:s')}'";
                } else {
                    $result = "'$value'";
                }
            } elseif ($helper->isInteger($field)) {
                $result = $value;
            } elseif ($helper->isLong($field)) {
                $result = $value;
            } elseif ($helper->isFloat($field)) {
                $result = $value;
            } elseif ($helper->isPrice($field)) {
                if (is_numeric($value)) {
                    $result = $value;
                } else {
                    $priceHelper = new PriceHelper();
                    $result = $priceHelper->asFloat($value);
                }
            } elseif ($helper->isDouble($field)) {
                $result = $value;
            } elseif ($helper->isString($field)) {
                if (false !== strpos($value, "'")) {
                    $result = '"'.$value.'"';
                } else {
                    $result = "'".$value."'";
                }
            } else {
                /** @var BusinessBundle $businessBundle */
                $businessBundle = $context['package'];
                if ($helper->isBusinessModel($field, $businessBundle)) {
                    throw new InvalidArgumentException('Cannot render a value for Business Model: '.$field->getType());
                } elseif ($helper->isDataset($field, $businessBundle)) {
                    $dataset = $businessBundle->getDataset($field->getType());
                    $element = $dataset->getElement($value);
                    if (null === $element) {
                        throw new InvalidArgumentException("Cannot find element $value in Dataset {$dataset->getName()}");
                    }

                    $result = $this->valueFilter($context, $dataset->getIdentifier(), $element->getIdentifierValue());
                } else {
                    throw new InvalidArgumentException('Cannot render a value for field type: '.$field->getType());
                }
            }

            if ($field->isList()) {
                $result = '['.$result.']';
            }
        } else {
            throw new InvalidArgumentException('Cannot render a value for class: '.get_class($field));
        }

        return $result;
    }

    /**
     * Checks if a relationship side requires a foreign key.
     */
    public function foreignKeyTest(RelationshipSide $obj): bool
    {
        return $this->databaseAdapter->isValidForeignKey($obj);
    }

    /**
     * Retrieves the list of fields from an entity that must be stored in a relational database table associated with
     * this entity.
     *
     * @param $model BusinessModel|Dataset
     *
     * @return Field[]
     */
    public function databaseFieldsFunction($model): array
    {
        if ($model instanceof BusinessModel) {
            return $this->databaseAdapter->getDatabaseFields($model);
        } elseif ($model instanceof Dataset) {
            return $this->databaseAdapter->getDatasetDatabaseFields($model);
        }

        return [];
    }

    /**
     * Retrieves the list of fields from an entity that should audited in a relational database table associated with
     * this entity.
     *
     * @return Field[]
     */
    public function auditedFieldsFunction(BusinessModel $businessModel, bool $includeId = true): array
    {
        return $this->databaseAdapter->getAuditedFields($businessModel, $includeId);
    }

    /**
     * Retrieves the list of indexes that are associated with an entity.
     *
     * @param $model BusinessModel|Dataset
     *
     * @return Index[]
     */
    public function indexesFunction($model): array
    {
        if ($model instanceof BusinessModel) {
            return $this->databaseAdapter->getBusinessModelIndexes($model);
        }

        return [];
    }
}
