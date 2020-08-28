<?php

namespace CodePrimer\Twig;

use CodePrimer\Helper\FieldHelper;
use CodePrimer\Helper\FieldType;
use CodePrimer\Model\BusinessBundle;
use CodePrimer\Model\Data\Data;
use CodePrimer\Model\Data\DataBundle;
use CodePrimer\Model\Data\EventData;
use CodePrimer\Model\Dataset;
use CodePrimer\Model\DatasetElement;
use CodePrimer\Model\Field;
use Twig\TwigFilter;

class MarkdownTwigExtension extends LanguageTwigExtension
{
    public function getFilters(): array
    {
        $filters = parent::getFilters();

        $filters[] = new TwigFilter('details', [$this, 'detailsFilter'], ['is_safe' => ['html'], 'needs_context' => true]);
        $filters[] = new TwigFilter('model', [$this, 'modelFilter'], ['is_safe' => ['html'], 'needs_context' => true]);
        $filters[] = new TwigFilter('header', [$this, 'headerFilter'], ['is_safe' => ['html']]);
        $filters[] = new TwigFilter('row', [$this, 'rowFilter'], ['is_safe' => ['html']]);

        return $filters;
    }

    public function typeFilter(array $context, $obj, bool $mandatory = false): string
    {
        $helper = new FieldHelper();

        if ($obj instanceof DataBundle) {
            $type = 'Unknown';

            if ($obj->isSimpleStructure()) {
                $type = 'Structure';
            } elseif ($obj->isListStructure()) {
                $type = 'List';
            }

            return $type;
        } elseif ($obj instanceof Data) {
            $field = $obj->getField();
            if (($obj instanceof EventData) && !$mandatory) {
                $mandatory = $obj->isMandatory();
            }
        } else {
            $field = $obj;
        }

        $type = $field->getType();
        if (!$helper->isNativeType($field)) {
            /** @var BusinessBundle $businessBundle */
            $businessBundle = $context['bundle'];
            if ($helper->isBusinessModel($field, $businessBundle)) {
                $model = $businessBundle->getBusinessModel($field->getType());
                $class = $this->classFilter($model->getName());
                $type = '[`'.$class.'`](../DataModel/Overview.md#'.strtolower($class).')';
            } elseif ($helper->isDataset($field, $businessBundle)) {
                $dataset = $businessBundle->getDataset($field->getType());
                $class = $this->classFilter($dataset->getName());
                $type = '[`'.$class.'`](../Dataset/Overview.md#'.strtolower($class).')';
            }
        } else {
            switch ($field->getType()) {
                case FieldType::BOOL:
                    $type = FieldType::BOOLEAN;
                    break;
                case FieldType::INT:
                    $type = FieldType::INTEGER;
                    break;
            }
        }
        if ($field->isList()) {
            $type = 'List of '.$type;
        }

        return $type;
    }

    public function detailsFilter(array $context, $obj): string
    {
        $helper = new FieldHelper();

        $result = '*N/A*';
        if ($obj instanceof Data) {
            $field = $obj->getField();
            if (!$helper->isNativeType($field)) {
                /** @var BusinessBundle $businessBundle */
                $businessBundle = $context['bundle'];
                if ($helper->isBusinessModel($field, $businessBundle)) {
                    $result = $this->classFilter($obj->getDetails());
                } elseif ($helper->isDataset($field, $businessBundle)) {
                    $result = $this->classFilter($obj->getDetails());
                }
            }
        }

        return $result;
    }

    public function modelFilter(array $context, $obj): string
    {
        $helper = new FieldHelper();

        $result = '*N/A*';

        $class = null;
        if ($obj instanceof Data) {
            $class = $obj->getBusinessModel()->getName();
        } elseif ($obj instanceof Field) {
            if (!$helper->isNativeType($obj)) {
                /** @var BusinessBundle $businessBundle */
                $businessBundle = $context['bundle'];
                $model = $businessBundle->getBusinessModel($obj->getType());
                if (null !== $model) {
                    $class = $this->classFilter($model->getName());
                }
            }
        }
        if (null !== $class) {
            $result = '[`'.$class.'`](../DataModel/Overview.md#'.strtolower($class).')';
        }

        return $result;
    }

    public function headerFilter($obj): string
    {
        $header1 = '';
        $header2 = '';
        if ($obj instanceof Dataset) {
            $name = $obj->getIdentifier()->getName();
            $header1 = '| '.$name.' ';
            $chars = strlen($name);
            // Markdown needs at least 3 '-' for each column to render a table
            if ($chars < 3) {
                $header1 .= ' ';
                $chars = 3;
            }
            $header2 = '| '.str_repeat('-', $chars).' ';
            foreach ($obj->getFields() as $field) {
                if (!$field->isIdentifier()) {
                    $name = $field->getName();
                    $header1 .= '| '.$name.' ';
                    $header2 .= '| '.str_repeat('-', strlen($name)).' ';
                }
            }
        }
        if (!empty($header1)) {
            $header1 .= '|';
            $header2 .= '|';
        }

        return $header1.PHP_EOL.$header2;
    }

    public function rowFilter($obj): string
    {
        $fieldHelper = new FieldHelper();

        $row = '';
        if ($obj instanceof DatasetElement) {
            $value = $obj->getIdentifierValue();
            $row = '| **'.$value.'** ';
            foreach ($obj->getDataset()->getFields() as $field) {
                if (!$field->isIdentifier()) {
                    $value = $obj->getValue($field->getName());
                    if ($fieldHelper->isBoolean($field)) {
                        $value = $this->yesNoFilter($value);
                    }
                    $row .= '| '.$value.' ';
                }
            }
        }
        if (!empty($row)) {
            $row .= '|';
        }

        return $row;
    }
}
