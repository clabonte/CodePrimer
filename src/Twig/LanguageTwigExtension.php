<?php

namespace CodePrimer\Twig;

use CodePrimer\Helper\FieldHelper;
use CodePrimer\Model\BusinessBundle;
use CodePrimer\Model\BusinessModel;
use CodePrimer\Model\BusinessProcess;
use CodePrimer\Model\Constraint;
use CodePrimer\Model\Data\Data;
use CodePrimer\Model\Data\DataBundle;
use CodePrimer\Model\Data\EventData;
use CodePrimer\Model\DataSet;
use CodePrimer\Model\Derived\Event;
use CodePrimer\Model\Field;
use CodePrimer\Model\Relationship;
use CodePrimer\Model\RelationshipSide;
use CodePrimer\Model\State;
use CodePrimer\Model\StateMachine;
use CodePrimer\Model\Transition;
use Doctrine\Common\Inflector\Inflector;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigTest;

/**
 * Class LanguageTwigExtension
 * This class defines a set of Twig filter that are made available for coding templates. This class attempts to provide
 * a language independent representation of several concepts. For specific language constructs, a subclass should be
 * created to meet the language needs.
 *
 * @author Christian Labonté
 */
class LanguageTwigExtension extends AbstractExtension
{
    /** @var FieldHelper */
    private $fieldHelper;

    public function __construct()
    {
        $this->fieldHelper = new FieldHelper();
    }

    public function getFilters()
    {
        return [
            new TwigFilter('plural', [$this, 'pluralFilter'], ['is_safe' => ['html']]),
            new TwigFilter('singular', [$this, 'singularFilter'], ['is_safe' => ['html']]),
            new TwigFilter('words', [$this, 'wordsFilter'], ['is_safe' => ['html']]),
            new TwigFilter('camel', [$this, 'camelFilter'], ['is_safe' => ['html']]),
            new TwigFilter('underscore', [$this, 'underscoreFilter'], ['is_safe' => ['html']]),
            new TwigFilter('path', [$this, 'pathFilter'], ['is_safe' => ['html']]),
            new TwigFilter('lastPath', [$this, 'lastPathFilter'], ['is_safe' => ['html']]),
            new TwigFilter('class', [$this, 'classFilter'], ['is_safe' => ['html']]),
            new TwigFilter('constant', [$this, 'constantFilter'], ['is_safe' => ['html']]),
            new TwigFilter('member', [$this, 'memberFilter'], ['is_safe' => ['html']]),
            new TwigFilter('variable', [$this, 'variableFilter'], ['is_safe' => ['html']]),
            new TwigFilter('parameter', [$this, 'parameterFilter'], ['is_safe' => ['html'], 'needs_context' => true]),
            new TwigFilter('type', [$this, 'typeFilter'], ['is_safe' => ['html'], 'needs_context' => true]),
            new TwigFilter('listType', [$this, 'listTypeFilter'], ['is_safe' => ['html'], 'needs_context' => true]),
            new TwigFilter('getter', [$this, 'getterFilter'], ['is_safe' => ['html']]),
            new TwigFilter('setter', [$this, 'setterFilter'], ['is_safe' => ['html']]),
            new TwigFilter('addMethod', [$this, 'addMethodFilter'], ['is_safe' => ['html']]),
            new TwigFilter('removeMethod', [$this, 'removeMethodFilter'], ['is_safe' => ['html']]),
            new TwigFilter('containsMethod', [$this, 'containsMethodFilter'], ['is_safe' => ['html']]),
            new TwigFilter('yesNo', [$this, 'yesNoFilter'], ['is_safe' => ['html']]),
        ];
    }

    public function getTests()
    {
        return [
            new TwigTest('scalar', [$this, 'scalarTest']),
            new TwigTest('double', [$this, 'doubleTest']),
            new TwigTest('float', [$this, 'floatTest']),
            new TwigTest('price', [$this, 'priceTest']),
            new TwigTest('long', [$this, 'longTest']),
            new TwigTest('integer', [$this, 'integerTest']),
            new TwigTest('boolean', [$this, 'booleanTest']),
            new TwigTest('string', [$this, 'stringTest']),
            new TwigTest('entity', [$this, 'entityTest']),
            new TwigTest('uuid', [$this, 'uuidTest']),
            new TwigTest('oneToOne', [$this, 'oneToOneTest']),
            new TwigTest('oneToMany', [$this, 'oneToManyTest']),
            new TwigTest('manyToOne', [$this, 'manyToOneTest']),
            new TwigTest('manyToMany', [$this, 'manyToManyTest']),
        ];
    }

    /**
     * Test if a field is a scalar type.
     *
     * @param mixed $obj The object to test
     *
     * @return bool
     */
    public function scalarTest($obj)
    {
        $result = false;

        $field = null;

        if ($obj instanceof Field) {
            $field = $obj;
        } elseif ($obj instanceof Data) {
            $field = $obj->getField();
        }

        if (null !== $field) {
            if ($this->fieldHelper->isLong($field)) {
                $result = true;
            } elseif ($this->fieldHelper->isInteger($field)) {
                $result = true;
            } elseif ($this->fieldHelper->isBoolean($field)) {
                $result = true;
            } elseif ($this->fieldHelper->isDouble($field)) {
                $result = true;
            } elseif ($this->fieldHelper->isFloat($field)) {
                $result = true;
            } elseif ($this->fieldHelper->isPrice($field)) {
                $result = true;
            }
        }

        return $result;
    }

    /**
     * Test if a field is of type double.
     *
     * @param mixed $obj The object to test
     *
     * @return bool
     */
    public function doubleTest($obj)
    {
        $result = false;

        if ($obj instanceof Field) {
            $result = $this->fieldHelper->isDouble($obj);
        } elseif ($obj instanceof Data) {
            $result = $this->fieldHelper->isDouble($obj->getField());
        }

        return $result;
    }

    /**
     * Test if a field is of type float.
     *
     * @param mixed $obj The object to test
     *
     * @return bool
     */
    public function floatTest($obj)
    {
        $result = false;

        if ($obj instanceof Field) {
            $result = $this->fieldHelper->isFloat($obj);
        } elseif ($obj instanceof Data) {
            $result = $this->fieldHelper->isFloat($obj->getField());
        }

        return $result;
    }

    /**
     * Test if a field is of type price.
     *
     * @param mixed $obj The object to test
     *
     * @return bool
     */
    public function priceTest($obj)
    {
        $result = false;

        if ($obj instanceof Field) {
            $result = $this->fieldHelper->isPrice($obj);
        } elseif ($obj instanceof Data) {
            $result = $this->fieldHelper->isPrice($obj->getField());
        }

        return $result;
    }

    /**
     * Test if a field is of type long.
     *
     * @param mixed $obj The object to test
     *
     * @return bool
     */
    public function longTest($obj)
    {
        $result = false;

        if ($obj instanceof Field) {
            $result = $this->fieldHelper->isLong($obj);
        } elseif ($obj instanceof Data) {
            $result = $this->fieldHelper->isLong($obj->getField());
        }

        return $result;
    }

    /**
     * Test if a field is of type integer.
     *
     * @param mixed $obj The object to test
     *
     * @return bool
     */
    public function integerTest($obj)
    {
        $result = false;

        if ($obj instanceof Field) {
            $result = $this->fieldHelper->isInteger($obj);
        } elseif ($obj instanceof Data) {
            $result = $this->fieldHelper->isInteger($obj->getField());
        }

        return $result;
    }

    /**
     * Test if a field is of type boolean.
     *
     * @param mixed $obj The object to test
     *
     * @return bool
     */
    public function booleanTest($obj)
    {
        $result = false;

        if ($obj instanceof Field) {
            $result = $this->fieldHelper->isBoolean($obj);
        } elseif ($obj instanceof Data) {
            $result = $this->fieldHelper->isBoolean($obj->getField());
        }

        return $result;
    }

    /**
     * Test if a field is of type string.
     *
     * @param mixed $obj The object to test
     *
     * @return bool
     */
    public function stringTest($obj)
    {
        $result = false;

        if ($obj instanceof Field) {
            $result = $this->fieldHelper->isString($obj);
        } elseif ($obj instanceof Data) {
            $result = $this->fieldHelper->isString($obj->getField());
        }

        return $result;
    }

    /**
     * Test if a field is of type UUID.
     *
     * @param mixed $obj The object to test
     *
     * @return bool
     */
    public function uuidTest($obj)
    {
        $result = false;

        if ($obj instanceof Field) {
            $result = $this->fieldHelper->isUuid($obj);
        } elseif ($obj instanceof Data) {
            $result = $this->fieldHelper->isUuid($obj->getField());
        }

        return $result;
    }

    /**
     * Test if a field is an BusinessModel.
     *
     * @param mixed $obj The object to test
     *
     * @return bool
     */
    public function entityTest($obj, BusinessBundle $businessBundle)
    {
        $result = false;

        if ($obj instanceof Field) {
            $result = $this->fieldHelper->isBusinessModel($obj, $businessBundle);
        } elseif ($obj instanceof Data) {
            $result = $this->fieldHelper->isBusinessModel($obj->getField(), $businessBundle);
        }

        return $result;
    }

    /**
     * Test if the object has a One-To-One relationship with another entity. It supports BusinessModel, Field and RelationshipSide
     *  - BusinessModel: Checks if the entity has at least one field with a One-To-One relationship
     *  - Field: Checks it the field has a One-To-One relationship
     *  - RelationshipSide: Checks if it represents a One-To-One relationship.
     *
     * @param mixed $obj The object to test
     */
    public function oneToOneTest($obj): bool
    {
        $result = false;

        /** @var RelationshipSide $relation */
        $relation = null;

        if ($obj instanceof BusinessModel) {
            $relations = $obj->getRelations();
            foreach ($relations as $relation) {
                $result = $this->oneToOneTest($relation);
                if ($result) {
                    return $result;
                }
            }
        } elseif ($obj instanceof Field) {
            $relation = $obj->getRelation();
        } elseif ($obj instanceof RelationshipSide) {
            $relation = $obj;
        } elseif ($obj instanceof Data) {
            $relation = $obj->getField()->getRelation();
        }

        if (null !== $relation) {
            $result = Relationship::ONE_TO_ONE == $relation->getRelationship()->getType();
        }

        return $result;
    }

    /**
     * Test if the object has a One-To-Many relationship with another entity. It supports BusinessModel, Field and RelationshipSide
     *  - BusinessModel: Checks if the entity has at least one field with a One-To-Many relationship
     *  - Field: Checks it the field has a One-To-Many relationship
     *  - RelationshipSide: Checks if it represents a One-To-Many relationship.
     *
     * @param mixed $obj The object to test
     */
    public function oneToManyTest($obj): bool
    {
        $result = false;

        /** @var RelationshipSide $relation */
        $relation = null;

        if ($obj instanceof BusinessModel) {
            $relations = $obj->getRelations();
            foreach ($relations as $relation) {
                $result = $this->oneToManyTest($relation);
                if ($result) {
                    return $result;
                }
            }
        } elseif ($obj instanceof Field) {
            $relation = $obj->getRelation();
        } elseif ($obj instanceof RelationshipSide) {
            $relation = $obj;
        } elseif ($obj instanceof Data) {
            $relation = $obj->getField()->getRelation();
        }

        if (null !== $relation) {
            $result = (RelationshipSide::LEFT == $relation->getSide()) &&
                (Relationship::ONE_TO_MANY == $relation->getRelationship()->getType());
        }

        return $result;
    }

    /**
     * Test if the object has a Many-To-One relationship with another entity. It supports BusinessModel, Field and RelationshipSide
     *  - BusinessModel: Checks if the entity has at least one field with a Many-To-One relationship
     *  - Field: Checks it the field has a Many-To-One relationship
     *  - RelationshipSide: Checks if it represents a Many-To-One relationship.
     *
     * @param mixed $obj The object to test
     */
    public function manyToOneTest($obj): bool
    {
        $result = false;

        /** @var RelationshipSide $relation */
        $relation = null;

        if ($obj instanceof BusinessModel) {
            $relations = $obj->getRelations();
            foreach ($relations as $relation) {
                $result = $this->manyToOneTest($relation);
                if ($result) {
                    return $result;
                }
            }
        } elseif ($obj instanceof Field) {
            $relation = $obj->getRelation();
        } elseif ($obj instanceof RelationshipSide) {
            $relation = $obj;
        } elseif ($obj instanceof Data) {
            $relation = $obj->getField()->getRelation();
        }

        if (null !== $relation) {
            $result = (RelationshipSide::RIGHT == $relation->getSide()) &&
                (Relationship::ONE_TO_MANY == $relation->getRelationship()->getType());
        }

        return $result;
    }

    /**
     * Test if the object has a Many-To-Many relationship with another entity. It supports BusinessModel, Field and RelationshipSide
     *  - BusinessModel: Checks if the entity has at least one field with a Many-To-Many relationship
     *  - Field: Checks it the field has a Many-To-Many relationship
     *  - RelationshipSide: Checks if it represents a Many-To-Many relationship.
     *
     * @param mixed $obj The object to test
     */
    public function manyToManyTest($obj): bool
    {
        $result = false;

        /** @var RelationshipSide $relation */
        $relation = null;

        if ($obj instanceof BusinessModel) {
            $relations = $obj->getRelations();
            foreach ($relations as $relation) {
                $result = $this->manyToManyTest($relation);
                if ($result) {
                    return $result;
                }
            }
        } elseif ($obj instanceof Field) {
            $relation = $obj->getRelation();
        } elseif ($obj instanceof RelationshipSide) {
            $relation = $obj;
        } elseif ($obj instanceof Data) {
            $relation = $obj->getField()->getRelation();
        }

        if (null !== $relation) {
            $result = Relationship::MANY_TO_MANY == $relation->getRelationship()->getType();
        }

        return $result;
    }

    /**
     * Filters a string to transform it to its plural equivalent.
     * Converts 'Table' to 'Tables'.
     *
     * @param mixed $obj
     *
     * @return string
     */
    public function pluralFilter($obj)
    {
        $name = $this->getName($obj);
        if (is_string($name)) {
            return Inflector::pluralize($name);
        }

        return $name;
    }

    /**
     * Filters a string to transform it to its singular equivalent.
     * Converts 'Tables' to 'Table'.
     *
     * @param mixed $obj
     *
     * @return string
     */
    public function singularFilter($obj)
    {
        return Inflector::singularize($this->getName($obj));
    }

    /**
     * Filters a string to transform it to its camel case equivalent.
     * Converts 'table_name' to 'table name'.
     *
     * @param mixed $obj
     *
     * @return string
     */
    public function wordsFilter($obj)
    {
        return str_replace('_', ' ', $this->getName($obj));
    }

    /**
     * Filters a string to transform it to its camel case equivalent.
     * Converts 'table_name' and 'table-name' to 'tableName'.
     *
     * @param mixed $obj
     *
     * @return string
     */
    public function camelFilter($obj)
    {
        return Inflector::camelize($this->getName($obj));
    }

    /**
     * Filters a string to transform it to its underscore equivalent.
     * Converts 'TableName', 'table.name', 'table-name' and 'table name' to 'table_name'.
     *
     * @param mixed $obj
     *
     * @return string
     */
    public function underscoreFilter($obj)
    {
        $name = $this->getName($obj);

        if (is_string($name)) {
            $name = str_replace(['-', ' ', '.'], '_', $name);

            return str_replace('__', '_', Inflector::tableize($name));
        }

        return $name;
    }

    /**
     * Filters a string to transform it to its class name.
     * Converts 'Table Name', 'table_name' to 'TableName' and 'Table Names', 'table_names' to 'TableNames'.
     *
     * @param mixed $obj
     *
     * @return string
     */
    public function classFilter($obj)
    {
        return Inflector::classify($this->getName($obj));
    }

    /**
     * Filters a string to transform it to its constant name.
     * Converts 'constant value' to 'CONSTANT_VALUE'.
     *
     * @param mixed $obj
     *
     * @return string
     */
    public function constantFilter($obj)
    {
        return str_replace([' ', '-'], '_', strtoupper($this->getName($obj)));
    }

    /**
     * Filters a string to transform it to its variable name.
     * Converts 'Table Name', 'table_name' to 'tableName' and 'Table Names', 'table_names' to 'tableNames'.
     *
     * @param mixed $obj
     *
     * @return string
     */
    public function variableFilter($obj)
    {
        return Inflector::camelize($this->getName($obj));
    }

    /**
     * Filters a string to transform it to its member name.
     * Converts 'Table Name', 'table_name' to 'tableName' and 'Table Names', 'table_names' to 'tableNames'.
     *
     * @param mixed $obj
     *
     * @return string
     */
    public function memberFilter($obj)
    {
        return Inflector::camelize($this->getName($obj));
    }

    /**
     * Filters a string to transform it to a file path.
     * Converts 'Com\Folder\A' or 'Com.Folder.A' to 'Com/Folder/A'.
     *
     * @param string|BusinessBundle $obj
     *
     * @return string
     */
    public function pathFilter($obj)
    {
        $str = '';

        if ($obj instanceof BusinessBundle) {
            $str = $obj->getNamespace();
        } elseif (is_string($obj)) {
            $str = $obj;
        }

        if (!empty($str)) {
            return str_replace(['.', '\\', ' '], '/', $str);
        }

        return '';
    }

    /**
     * Filters a string to extract the last part of a path.
     * Converts 'Com\Folder\A' or 'Com/Folder/A' or 'Com.Folder.A' to 'A'.
     *
     * @param string|BusinessBundle $obj
     *
     * @return string
     */
    public function lastPathFilter($obj)
    {
        $str = '';

        if ($obj instanceof BusinessBundle) {
            $str = $obj->getNamespace();
        } elseif (is_string($obj)) {
            $str = $obj;
        }

        if (!empty($str)) {
            $str = str_replace(['/', '\\'], '.', $str);
            $values = explode('.', $str);

            return end($values);
        }

        return '';
    }

    /**
     * @param mixed $obj
     */
    public function getterFilter($obj)
    {
        $name = $this->getName($obj);
        if (is_string($name) && !empty($name)) {
            $prefix = 'get';

            return $prefix.$this->classFilter($name).'()';
        }

        return $name;
    }

    /**
     * @param mixed $obj
     *
     * @return string
     */
    public function setterFilter($obj)
    {
        $name = $this->getName($obj);
        if (is_string($name) && !empty($name)) {
            $prefix = 'set';

            return $prefix.$this->classFilter($name);
        }

        return $name;
    }

    /**
     * @param mixed $obj
     *
     * @return string
     */
    public function addMethodFilter($obj)
    {
        $name = $this->getName($obj);
        if (is_string($name) && !empty($name)) {
            $prefix = 'add';

            return $prefix.Inflector::singularize($this->classFilter($name));
        }

        return $name;
    }

    /**
     * @param mixed $obj
     *
     * @return string
     */
    public function removeMethodFilter($obj)
    {
        $name = $this->getName($obj);
        if (is_string($name) && !empty($name)) {
            $prefix = 'remove';

            return $prefix.Inflector::singularize($this->classFilter($name));
        }

        return $name;
    }

    /**
     * @param mixed $obj
     *
     * @return string
     */
    public function containsMethodFilter($obj)
    {
        $name = $this->getName($obj);
        if (is_string($name) && !empty($name)) {
            $prefix = 'contains';

            return $prefix.Inflector::singularize($this->classFilter($name));
        }

        return $name;
    }

    /**
     * Default language agnostic type filter. This must be overridden by subclasses to return the language's real type.
     *
     * @param string|Field|Data $obj
     * @param bool              $mandatory Whether this field is mandatory in this context
     */
    public function typeFilter(array $context, $obj, bool $mandatory = false): string
    {
        $helper = new FieldHelper();

        if ($obj instanceof Data) {
            $field = $obj->getField();
            if (($obj instanceof EventData) && !$mandatory) {
                $mandatory = $obj->isMandatory();
            }
        } else {
            $field = $obj;
        }

        $type = 'string';
        if ($field instanceof Field) {
            if ($field->isList()) {
                $type = 'list of '.$this->listTypeFilter($context, $field);
            } elseif ($helper->isBoolean($field)) {
                $type = 'bool';
            } elseif ($helper->isDate($field)) {
                $type = 'date';
            } elseif ($helper->isTime($field)) {
                $type = 'time';
            } elseif ($helper->isDateTime($field)) {
                $type = 'datetime';
            } elseif ($helper->isInteger($field)) {
                $type = 'int';
            } elseif ($helper->isLong($field)) {
                $type = 'long';
            } elseif ($helper->isFloat($field)) {
                $type = 'float';
            } elseif ($helper->isPrice($field)) {
                $type = 'double';
            } elseif ($helper->isDouble($field)) {
                $type = 'double';
            } elseif ($helper->isString($field)) {
                $type = 'string';
            } elseif (isset($context['package'])) {
                /** @var BusinessBundle $businessBundle */
                $businessBundle = $context['package'];
                if ($helper->isBusinessModel($field, $businessBundle)) {
                    $type = $field->getType();
                }
            }

            if (!$mandatory && !$field->isMandatory()) {
                $type = $type.' (Optional)';
            }
        }

        return $type;
    }

    /**
     * Default language agnostic list type filter. This must be overridden by subclasses to return the language's real type.
     *
     * @param array             $context
     * @param string|Field|Data $field
     *
     * @return string
     */
    public function listTypeFilter($context, $obj)
    {
        $helper = new FieldHelper();

        if ($obj instanceof Data) {
            $field = $obj->getField();
        } else {
            $field = $obj;
        }

        $type = 'string';
        if ($field instanceof Field) {
            if ($helper->isBoolean($field)) {
                $type = 'bool';
            } elseif ($helper->isDate($field)) {
                $type = 'date';
            } elseif ($helper->isTime($field)) {
                $type = 'time';
            } elseif ($helper->isDateTime($field)) {
                $type = 'datetime';
            } elseif ($helper->isInteger($field)) {
                $type = 'int';
            } elseif ($helper->isLong($field)) {
                $type = 'long';
            } elseif ($helper->isFloat($field)) {
                $type = 'float';
            } elseif ($helper->isPrice($field)) {
                $type = 'double';
            } elseif ($helper->isDouble($field)) {
                $type = 'double';
            } elseif ($helper->isString($field)) {
                $type = 'string';
            } elseif (isset($context['package'])) {
                /** @var BusinessBundle $businessBundle */
                $businessBundle = $context['package'];
                if ($helper->isBusinessModel($field, $businessBundle)) {
                    $type = $field->getType();
                }
            }
        }

        return $type;
    }

    /**
     * @param array|Field|Data $obj
     * @param bool             $mandatory Whether the parameters are mandatory in this context
     */
    public function parameterFilter(array $context, $obj, bool $mandatory = false): string
    {
        $params = '';

        if (is_array($obj)) {
            foreach ($obj as $value) {
                if (!empty($params)) {
                    $params .= ', ';
                }
                $params .= $this->typeFilter($context, $value, $mandatory).' '.$this->variableFilter($value);
            }
        } elseif ($obj instanceof Field) {
            $params = $this->typeFilter($context, $obj, $mandatory).' '.$this->variableFilter($obj);
        } elseif ($obj instanceof Data) {
            $params = $this->typeFilter($context, $obj->getField(), $mandatory).' '.$this->variableFilter($obj);
        }

        return $params;
    }

    /**
     * @param mixed $obj
     *
     * @return string
     */
    public function yesNoFilter($obj)
    {
        $value = 'N/A';

        if (is_bool($obj) || is_int($obj)) {
            if ($obj) {
                $value = 'yes';
            } else {
                $value = 'no';
            }
        } elseif (is_string($obj)) {
            switch (strtolower($obj)) {
                case 'yes':
                case 'true':
                    $value = 'yes';
                    break;
                case 'no':
                case 'false':
                    $value = 'no';
                    break;
            }
        }

        return $value;
    }

    /**
     * Extracts the name of an object.
     *
     * @param mixed $obj
     *
     * @return mixed|string
     */
    protected function getName($obj)
    {
        $name = $obj;

        if (!isset($obj)) {
            $name = '';
        } elseif ($obj instanceof Field) {
            $name = $obj->getName();
        } elseif ($obj instanceof BusinessModel) {
            $name = $obj->getName();
        } elseif ($obj instanceof BusinessProcess) {
            $name = $obj->getName();
        } elseif ($obj instanceof BusinessBundle) {
            $name = $obj->getName();
        } elseif ($obj instanceof DataBundle) {
            $name = $obj->getName();
        } elseif ($obj instanceof Data) {
            $name = $obj->getName();
        } elseif ($obj instanceof Event) {
            $name = $obj->getName();
        } elseif ($obj instanceof DataSet) {
            $name = $obj->getName();
        } elseif ($obj instanceof State) {
            $name = $obj->getName();
        } elseif ($obj instanceof StateMachine) {
            $name = $obj->getName();
        } elseif ($obj instanceof Transition) {
            $name = $obj->getName();
        } elseif ($obj instanceof Constraint) {
            $name = $obj->getName();
        }

        return $name;
    }
}
