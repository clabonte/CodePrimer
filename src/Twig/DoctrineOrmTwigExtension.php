<?php

namespace CodePrimer\Twig;

use CodePrimer\Adapter\RelationalDatabaseAdapter;
use CodePrimer\Helper\BusinessModelHelper;
use CodePrimer\Helper\FieldHelper;
use CodePrimer\Helper\FieldType;
use CodePrimer\Model\BusinessModel;
use CodePrimer\Model\Constraint;
use CodePrimer\Model\Database\Index;
use CodePrimer\Model\Field;
use CodePrimer\Model\Package;
use CodePrimer\Model\Relationship;
use CodePrimer\Model\RelationshipSide;
use Twig\TwigFunction;
use Twig\TwigTest;

class DoctrineOrmTwigExtension extends PhpTwigExtension
{
    /** @var BusinessModelHelper */
    private $businessModelHelper;

    /** @var FieldHelper */
    private $fieldHelper;

    /** @var RelationalDatabaseAdapter */
    private $databaseAdapter;

    public function __construct()
    {
        parent::__construct();
        $this->businessModelHelper = new BusinessModelHelper();
        $this->fieldHelper = new FieldHelper();
        $this->databaseAdapter = new RelationalDatabaseAdapter();
    }

    public function getFunctions(): array
    {
        $functions = parent::getFunctions();

        $functions[] = new TwigFunction('annotations', [$this, 'annotationsFunction'], ['is_safe' => ['html'], 'needs_context' => true]);

        return $functions;
    }

    public function getTests(): array
    {
        $tests = parent::getTests();

        $tests[] = new TwigTest('collectionUsed', [$this, 'collectionUsedTest']);

        return $tests;
    }

    /**
     * Checks if the object passed requires the use of a Doctrine Collection.
     *
     * @param $obj
     */
    public function collectionUsedTest($obj): bool
    {
        $result = false;

        if ($obj instanceof BusinessModel) {
            $relations = $obj->getRelations();
            foreach ($relations as $relation) {
                $type = $relation->getRelationship()->getType();
                if (Relationship::MANY_TO_MANY == $type) {
                    $result = true;
                    break;
                } elseif (Relationship::ONE_TO_MANY == $type && RelationshipSide::LEFT == $relation->getSide()) {
                    $result = true;
                    break;
                }
            }
        } elseif ($obj instanceof Field) {
            if ($obj->isList() && null !== $obj->getRelation()) {
                $result = true;
            }
        }

        return $result;
    }

    /**
     * Returns the list of Doctrine ORM annotations to use for a given object.
     *
     * @param array $context
     * @param $obj
     *
     * @return string[]
     */
    public function annotationsFunction($context, $obj): array
    {
        $result = [];

        if ($obj instanceof BusinessModel) {
            $result = $this->getBusinessModelAnnotations($obj);
        } elseif ($obj instanceof Field) {
            $result = $this->getFieldAnnotations($context, $obj);
        }

        return $result;
    }

    /**
     * Returns the list of Doctrine ORM annotations to use for a given Field.
     *
     * @return string[]
     */
    private function getFieldAnnotations(array $context, Field $field): array
    {
        $annotations = [];

        // Build the Id annotation, if needed
        if ($this->fieldHelper->isIdentifier($field)) {
            $annotations[] = '@ORM\Id()';
            if ($this->fieldHelper->isUuid($field)) {
                $annotations[] = '@ORM\GeneratedValue(strategy="UUID")';
            } else {
                $annotations[] = '@ORM\GeneratedValue()';
            }
        }

        // Build the column annotation, if needed
        $columnName = $this->databaseAdapter->getColumnName($field);
        $column = '';
        if ($this->fieldHelper->isBoolean($field)) {
            $column = '@ORM\Column(name="'.$columnName.'", type="boolean"';
        } elseif ($this->fieldHelper->isDate($field)) {
            $column = '@ORM\Column(name="'.$columnName.'", type="date"';
        } elseif ($this->fieldHelper->isTime($field)) {
            $column = '@ORM\Column(name="'.$columnName.'", type="time"';
        } elseif ($this->fieldHelper->isDateTime($field)) {
            $column = '@ORM\Column(name="'.$columnName.'", type="datetime"';
        } elseif ($this->fieldHelper->isInteger($field)) {
            $column = '@ORM\Column(name="'.$columnName.'", type="integer"';
        } elseif ($this->fieldHelper->isLong($field)) {
            $column = '@ORM\Column(name="'.$columnName.'", type="bigint"';
        } elseif ($this->fieldHelper->isFloat($field)) {
            $column = '@ORM\Column(name="'.$columnName.'", type="float"';
        } elseif ($this->fieldHelper->isDouble($field)) {
            switch ($field->getType()) {
                case FieldType::PRICE:
                    $column = '@ORM\Column(name="'.$columnName.'", type="decimal", precision=9, scale=2';
                    break;
                case FieldType::DECIMAL:
                    $column = '@ORM\Column(name="'.$columnName.'", type="decimal"';
                    break;
                default:
                    $column = '@ORM\Column(name="'.$columnName.'", type="float"';
            }
        } elseif ($this->fieldHelper->isString($field)) {
            switch ($field->getType()) {
                case FieldType::TEXT:
                    $column = '@ORM\Column(name="'.$columnName.'", type="text"';
                    break;
                case FieldType::PHONE:
                    $column = '@ORM\Column(name="'.$columnName.'", type="string", length=15';
                    break;
                case FieldType::UUID:
                    $column = '@ORM\Column(name="'.$columnName.'", type="string", length=36';
                    break;
                case FieldType::URL:
                    $column = '@ORM\Column(name="'.$columnName.'", type="string", length=255';
                    break;
                case FieldType::EMAIL:
                    $column = '@ORM\Column(name="'.$columnName.'", type="string", length=255';
                    break;
                default:
                    $column = '@ORM\Column(name="'.$columnName.'", type="string", length=255';
                    break;
            }
        }

        if (!empty($column)) {
            if (!$field->isMandatory()) {
                $column .= ', nullable=true)';
            } else {
                $column .= ')';
            }
            $annotations[] = $column;
        }

        // Build the relationship annotation, if needed
        $relation = $field->getRelation();
        if (isset($relation)) {
            /** @var Package $package */
            $package = $context['package'];
            $side = $relation->getSide();
            $remoteBusinessModel = $relation->getRemoteSide()->getBusinessModel();
            $remoteField = $relation->getRemoteSide()->getField();

            switch ($relation->getRelationship()->getType()) {
                case Relationship::ONE_TO_ONE:
                    $annotation = '@ORM\OneToOne(targetEntity="'.$this->namespaceFilter($context, $package).'\\'.$this->getName($remoteBusinessModel).'"';
                    // If this is a unidirectional link, cascade the operations
                    if (!isset($remoteField)) {
                        $annotation .= ', cascade={"persist", "remove"}';
                    } else {
                        $annotation .= ', inversedBy="'.$remoteField->getName().'"';
                    }
                    $annotation .= ')';
                    $annotations[] = $annotation;
                    break;
                case Relationship::ONE_TO_MANY:
                    if (RelationshipSide::LEFT == $side) {
                        $annotation = '@ORM\OneToMany(targetEntity="'.$this->namespaceFilter($context, $package).'\\'.$this->getName($remoteBusinessModel).'"';
                        $annotation .= ', mappedBy="'.$remoteField->getName().'", cascade={"persist", "remove", "merge"}, orphanRemoval=true';
                    } else {
                        $annotation = '@ORM\ManyToOne(targetEntity="'.$this->namespaceFilter($context, $package).'\\'.$this->getName($remoteBusinessModel).'"';
                        $annotation .= ', inversedBy="'.$remoteField->getName().'"';
                    }
                    $annotation .= ')';
                    $annotations[] = $annotation;
                    break;
                case Relationship::MANY_TO_MANY:
                    $annotation = '@ORM\ManyToMany(targetEntity="'.$this->namespaceFilter($context, $package).'\\'.$this->getName($remoteBusinessModel).'"';
                    if (RelationshipSide::LEFT == $side) {
                        $annotation .= ', mappedBy="'.$remoteField->getName().'"';
                    } else {
                        $annotation .= ', inversedBy="'.$remoteField->getName().'"';
                    }
                    $annotation .= ')';
                    $annotations[] = $annotation;
                    break;
            }
        }

        return $annotations;
    }

    /**
     * Returns the list of Doctrine ORM annotations to use for a given BusinessModel.
     *
     * @return string[]
     */
    private function getBusinessModelAnnotations(BusinessModel $businessModel): array
    {
        $annotations = [];

        // Build the entity annotation
        $annotations[] = '@ORM\Entity(repositoryClass="App\Repository\\'.$this->businessModelHelper->getRepositoryClass($businessModel).'")';

        // Build the table annotation
        $table = '@ORM\Table(name="'.$this->databaseAdapter->getTableName($businessModel).'"';
        // Add unique constraints
        $uniqueConstraints = $businessModel->getUniqueConstraints();
        if (!empty($uniqueConstraints)) {
            $table .= ', uniqueConstraints={';
            $count = 0;
            foreach ($uniqueConstraints as $uniqueConstraint) {
                if ($count > 0) {
                    $table .= ', ';
                }
                $table .= '@ORM\UniqueConstraint(name="'.$uniqueConstraint->getName().'", columns={'.$this->getBusinessModelColumns($uniqueConstraint).'})';
                ++$count;
            }
            $table .= '}';
        }
        // Add indexes
        $indexes = $this->databaseAdapter->getIndexes($businessModel);
        if (!empty($indexes)) {
            $table .= ', indexes={';
            $count = 0;
            foreach ($indexes as $index) {
                if ($count > 0) {
                    $table .= ', ';
                }
                $table .= '@ORM\Index(name="'.$index->getName().'", columns={'.$this->getIndexColumns($index).'})';
                ++$count;
            }
            $table .= '}';
        }
        $table .= ')';

        $annotations[] = $table;

        return $annotations;
    }

    /**
     * Returns the list of columns (comma-separated) associated with a given constraint.
     */
    private function getBusinessModelColumns(Constraint $uniqueConstraint): string
    {
        $names = [];
        foreach ($uniqueConstraint->getFields() as $field) {
            $names[] = '"'.$this->databaseAdapter->getColumnName($field).'"';
        }

        return implode(',', $names);
    }

    /**
     * Returns the list of columns (comma-separated) associated with a given index.
     */
    private function getIndexColumns(Index $index): string
    {
        $names = [];
        foreach ($index->getFields() as $field) {
            $names[] = '"'.$this->databaseAdapter->getColumnName($field).'"';
        }

        return implode(',', $names);
    }

    /**
     * @param Field|string $field
     * @param bool         $mandatory Whether this field is mandatory in the given context
     */
    public function typeFilter(array $context, $field, bool $mandatory = false): string
    {
        if ($field->isList() && null !== $field->getRelation()) {
            return 'Collection';
        }

        return parent::typeFilter($context, $field, $mandatory);
    }

    /**
     * @param Field|string $field
     * @param bool         $mandatory Whether this field is mandatory in the given context
     */
    public function hintFilter(array $context, $field, bool $mandatory = false): string
    {
        $hint = parent::hintFilter($context, $field, $mandatory);
        if ($field->isList() && null !== $field->getRelation()) {
            $hint = 'Collection|'.$hint;
        }

        return $hint;
    }
}
