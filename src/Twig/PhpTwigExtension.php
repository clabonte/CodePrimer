<?php
namespace CodePrimer\Twig;

use CodePrimer\Helper\FieldHelper;
use CodePrimer\Model\Entity;
use CodePrimer\Model\Field;
use CodePrimer\Model\Package;
use Doctrine\Common\Inflector\Inflector;
use Twig\TwigFilter;
use Twig\TwigTest;

/**
 * Class PhpTwigExtension
 * @package CodePrimer\Twig
 * @author Christian Labonté
 */
class PhpTwigExtension extends LanguageTwigExtension
{
    public function getFilters(): array
    {
        $filters = parent::getFilters();

        $filters[] = new TwigFilter('hint', [$this, 'hintFilter'], ['is_safe' => ['html'], 'needs_context' => true]);
        $filters[] = new TwigFilter('namespace', [$this, 'namespaceFilter'], ['is_safe' => ['html'], 'needs_context' => true]);

        return $filters;
    }

    public function getTests(): array
    {
        $tests = parent::getTests();

        $tests[] = new TwigTest('dateTimeUsed', [$this, 'dateTimeUsed']);

        return $tests;
    }

    /**
     * Checks if the DateTime type is used/required for a given entity
     * @param Entity $entity
     * @return bool
     */
    public function dateTimeUsed(Entity $entity): bool
    {
        $result = false;

        $helper = new FieldHelper();

        foreach ($entity->getFields() as $field) {
            if ($helper->isDateTime($field) || $helper->isDate($field) || $helper->isTime($field)) {
                $result = true;
                break;
            }
        }

        return $result;
    }

    /**
     * Filters a string to transform it to its variable name. Converts 'table_names' to '$tableName'.
     *
     * @param mixed $obj
     *
     * @return string
     */
    public function variableFilter($obj): string
    {
        $name = $this->getName($obj);
        if (is_string($name) && !empty($name)) {
            return '$' . parent::variableFilter($obj);
        }
        return $name;
    }

    /**
     * Filters a string to transform it to its member name. Converts 'table_names' to '$this->tableName'.
     *
     * @param mixed $obj
     *
     * @return string
     */
    public function memberFilter($obj): string
    {
        $name = $this->getName($obj);
        if (is_string($name) && !empty($name)) {
            return '$this->' . parent::memberFilter($obj);
        }
        return $name;
    }

    /**
     * Filters a string to transform it to a namespace path. Converts 'Com.Folder.A' or 'Com/Folder/A' to 'Com\Folder\A'
     *
     * @param array $context
     * @param string|Package $obj
     * @param string|null $subpackage
     *
     * @return string
     */
    public function namespaceFilter($context, $obj, string $subpackage = null): string
    {
        $str = '';

        if ($obj instanceof Package) {
            $str = $obj->getNamespace();
        } elseif (is_string($obj)) {
            $str = $obj;
        }

        if ($subpackage !== null) {
            if (!empty($subpackage)) {
                $str .= '\\' . $subpackage;
            }
        } elseif (!empty($context['subpackage'])) {
            $str .= '\\' . $context['subpackage'];
        }

        if (!empty($str)) {
            return str_replace(['.', '/', ' '], '\\', $str);
        }
        return '';
    }

    /**
     * @param array $context
     * @param string|Field $field
     * @param bool $mandatory Whether the field is mandatory in this context
     * @return string
     */
    public function typeFilter(array $context, $field, bool $mandatory = false): string
    {
        $helper = new FieldHelper();

        $type = 'string';
        if ($field instanceof Field) {
            if ($field->isList()) {
                $type = 'array';
            } elseif ($helper->isBoolean($field)) {
                $type = 'bool';
            } elseif ($helper->isDate($field)) {
                $type = 'DateTimeInterface';
            } elseif ($helper->isTime($field)) {
                $type = 'DateTimeInterface';
            } elseif ($helper->isDateTime($field)) {
                $type = 'DateTimeInterface';
            } elseif ($helper->isInteger($field)) {
                $type = 'int';
            } elseif ($helper->isLong($field)) {
                $type = 'int';
            } elseif ($helper->isFloat($field)) {
                $type = 'float';
            } elseif ($helper->isDouble($field)) {
                $type = 'double';
            } elseif ($helper->isString($field)) {
                $type = 'string';
            } elseif (isset($context['package'])) {
                /** @var Package $package */
                $package = $context['package'];
                if ($helper->isEntity($field, $package)) {
                    $type = $field->getType();
                }
            }

            if (!$mandatory && !$field->isMandatory()) {
                $type = '?' . $type;
            }
        }

        return $type;
    }

    /**
     * @param array $context
     * @param string|Field $field
     * @return string
     */
    public function listTypeFilter($context, $field): string
    {
        $helper = new FieldHelper();

        $type = 'string';
        if ($field instanceof Field) {
            if ($helper->isBoolean($field)) {
                $type = 'bool';
            } elseif ($helper->isDate($field)) {
                $type = 'DateTimeInterface';
            } elseif ($helper->isTime($field)) {
                $type = 'DateTimeInterface';
            } elseif ($helper->isDateTime($field)) {
                $type = 'DateTimeInterface';
            } elseif ($helper->isInteger($field)) {
                $type = 'int';
            } elseif ($helper->isLong($field)) {
                $type = 'int';
            } elseif ($helper->isFloat($field)) {
                $type = 'float';
            } elseif ($helper->isDouble($field)) {
                $type = 'double';
            } elseif ($helper->isString($field)) {
                $type = 'string';
            } elseif (isset($context['package'])) {
                /** @var Package $package */
                $package = $context['package'];
                if ($helper->isEntity($field, $package)) {
                    $type = $field->getType();
                }
            }
        }

        return $type;
    }

    /**
     * @param array $context
     * @param string|Field $field
     * @param bool $mandatory Whether the field is mandatory in this context
     * @return string
     */
    public function hintFilter(array $context, $field, bool $mandatory = false): string
    {
        $helper = new FieldHelper();

        $type = 'string';
        if ($field instanceof Field) {
            if ($helper->isBoolean($field)) {
                $type = 'bool';
            } elseif ($helper->isDate($field)) {
                $type = 'DateTimeInterface';
            } elseif ($helper->isTime($field)) {
                $type = 'DateTimeInterface';
            } elseif ($helper->isDateTime($field)) {
                $type = 'DateTimeInterface';
            } elseif ($helper->isInteger($field)) {
                $type = 'int';
            } elseif ($helper->isLong($field)) {
                $type = 'int';
            } elseif ($helper->isFloat($field)) {
                $type = 'float';
            } elseif ($helper->isDouble($field)) {
                $type = 'double';
            } elseif ($helper->isString($field)) {
                $type = 'string';
            } elseif (isset($context['package'])) {
                /** @var Package $package */
                $package = $context['package'];
                if ($helper->isEntity($field, $package)) {
                    $type = $field->getType();
                }
            }

            if ($field->isList()) {
                $type .= '[]';
            }

            if (!$mandatory && !$field->isMandatory()) {
                $type .= '|null';
            }
        }

        return $type;
    }
}