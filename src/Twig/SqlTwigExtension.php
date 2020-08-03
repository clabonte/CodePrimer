<?php

namespace CodePrimer\Twig;

use CodePrimer\Adapter\RelationalDatabaseAdapter;
use CodePrimer\Helper\BusinessModelHelper;
use CodePrimer\Helper\FieldHelper;
use CodePrimer\Model\Database\Index;
use CodePrimer\Model\BusinessModel;
use CodePrimer\Model\Field;
use CodePrimer\Model\Package;
use CodePrimer\Model\RelationshipSide;
use Doctrine\Common\Inflector\Inflector;
use Exception;
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
    protected $entityHelper;

    /** @var FieldHelper */
    protected $fieldHelper;

    /** @var RelationalDatabaseAdapter */
    protected $databaseAdapter;

    public function __construct()
    {
        parent::__construct();
        $this->entityHelper = new BusinessModelHelper();
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
    public function databaseFilter(Package $package): string
    {
        return $this->databaseAdapter->getDatabaseName($package);
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
        }
        throw new RuntimeException('Table names can only generated for BusinessModel or RelationshipSide instances');
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

            return implode(',', $columns);
        }
        throw new RuntimeException('Column names can only generated for Field, BusinessModel or Index instances. Received: '.null === $obj ? 'null' : get_class($obj));
    }

    /**
     * Returns the name to use as a foreign key for a given relationship side.
     *
     * @throws Exception If the Relationship is either a Many-To-Many or the left side of a One-To-Many
     */
    public function foreignKeyFilter(RelationshipSide $obj): string
    {
        if (!$this->databaseAdapter->isValidForeignKey($obj)) {
            throw new Exception('foreignKey filter can only be used against a One-To-One or the right side of a One-To-Many relationship');
        }

        // Format: fk_[referencing table name]_[referenced table name](_[referencing field name])
        $remoteSide = $obj->getRemoteSide();
        $result = 'fk_'.$this->tableFilter($obj->getBusinessModel()).'_'.$this->tableFilter($remoteSide->getBusinessModel()).'_'.$this->columnFilter($obj->getField());

        return $result;
    }

    /**
     * Returns the user name to use for connecting to the database based on a given Package.
     */
    public function userFilter(Package $package): string
    {
        $name = str_replace(['-', ' ', '.'], '_', $package->getName());
        $name = Inflector::tableize($name);
        $name = str_replace('__', '_', $name);

        return $name;
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
     * @return Field[]
     */
    public function databaseFieldsFunction(BusinessModel $businessModel): array
    {
        return $this->databaseAdapter->getDatabaseFields($businessModel);
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
     * @return Index[]
     */
    public function indexesFunction(BusinessModel $businessModel): array
    {
        return $this->databaseAdapter->getIndexes($businessModel);
    }
}
