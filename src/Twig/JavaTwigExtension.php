<?php

namespace CodePrimer\Twig;

use CodePrimer\Helper\FieldHelper;
use CodePrimer\Model\BusinessBundle;
use CodePrimer\Model\Data\Data;
use CodePrimer\Model\Data\EventData;
use CodePrimer\Model\Field;
use Doctrine\Common\Inflector\Inflector;
use Twig\TwigFilter;

/**
 * Class JavaTwigExtension.
 *
 * @author Christian Labonté
 */
class JavaTwigExtension extends LanguageTwigExtension
{
    public function getFilters()
    {
        $filters = parent::getFilters();

        $filters[] = new TwigFilter('package', [$this, 'packageFilter'], ['is_safe' => ['html']]);

        return $filters;
    }

    /**
     * Filters a string to transform it to its member name. Converts 'table_names' to 'this->tableName'.
     *
     * @param mixed $obj
     *
     * @return string
     */
    public function memberFilter($obj)
    {
        $name = $this->getName($obj);
        if (is_string($name) && !empty($name)) {
            return 'this.'.Inflector::singularize(Inflector::camelize($this->getName($obj)));
        }

        return $name;
    }

    /**
     * Filters a string to transform it to a package equivalent. Converts 'Com\Folder\A' or 'Com/Folder/A' to 'com.folder.a'.
     *
     * @param string|BusinessBundle $obj
     *
     * @return string
     */
    public function packageFilter($obj)
    {
        $str = '';

        if ($obj instanceof BusinessBundle) {
            $str = $obj->getNamespace();
        } elseif (is_string($obj)) {
            $str = $obj;
        }

        if (!empty($str)) {
            return str_replace(['\\', '/', ' '], '.', strtolower($str));
        }

        return '';
    }

    /**
     * @param $context
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

        $type = 'Object';
        if ($field instanceof Field) {
            if ($helper->isBoolean($field)) {
                $type = 'boolean';
            } elseif ($helper->isDate($field)) {
                $type = 'Date';
            } elseif ($helper->isTime($field)) {
                $type = 'long';
            } elseif ($helper->isDateTime($field)) {
                $type = 'Date';
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
                $type = 'String';
            } elseif (isset($context['package'])) {
                /** @var BusinessBundle $businessBundle */
                $businessBundle = $context['package'];
                if ($helper->isBusinessModel($field, $businessBundle)) {
                    $type = $field->getType();
                } elseif ($helper->isDataset($field, $businessBundle)) {
                    $type = $field->getType();
                }
            }
        }

        if ($field->isList()) {
            $type = 'List<'.$type.'>';
        }

        return $type;
    }

    /**
     * @param $context
     * @param string|Field|Data $obj
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

        $type = 'Object';
        if ($field instanceof Field) {
            if ($helper->isBoolean($field)) {
                $type = 'Boolean';
            } elseif ($helper->isDate($field)) {
                $type = 'Date';
            } elseif ($helper->isTime($field)) {
                $type = 'Long';
            } elseif ($helper->isDateTime($field)) {
                $type = 'Date';
            } elseif ($helper->isInteger($field)) {
                $type = 'Integer';
            } elseif ($helper->isLong($field)) {
                $type = 'Long';
            } elseif ($helper->isFloat($field)) {
                $type = 'Float';
            } elseif ($helper->isPrice($field)) {
                $type = 'Double';
            } elseif ($helper->isDouble($field)) {
                $type = 'Double';
            } elseif ($helper->isString($field)) {
                $type = 'String';
            } elseif (isset($context['package'])) {
                /** @var BusinessBundle $businessBundle */
                $businessBundle = $context['package'];
                if ($helper->isBusinessModel($field, $businessBundle)) {
                    $type = $field->getType();
                } elseif ($helper->isDataset($field, $businessBundle)) {
                    $type = $field->getType();
                }
            }
        }

        return $type;
    }
}
