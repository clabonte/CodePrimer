<?php

namespace CodePrimer\Twig;

use CodePrimer\Helper\FieldHelper;
use CodePrimer\Helper\FieldType;
use CodePrimer\Model\BusinessBundle;
use CodePrimer\Model\Data\Data;
use CodePrimer\Model\Data\DataBundle;
use CodePrimer\Model\Data\EventData;
use CodePrimer\Model\Field;
use Twig\TwigFilter;

class MarkdownTwigExtension extends LanguageTwigExtension
{
    public function getFilters(): array
    {
        $filters = parent::getFilters();

        $filters[] = new TwigFilter('details', [$this, 'detailsFilter'], ['is_safe' => ['html'], 'needs_context' => true]);
        $filters[] = new TwigFilter('model', [$this, 'modelFilter'], ['is_safe' => ['html'], 'needs_context' => true]);

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
            $model = $businessBundle->getBusinessModel($field->getType());
            if (null !== $model) {
                $class = $this->classFilter($model->getName());
                $type = '['.$class.'](../DataModel/Overview.md#'.strtolower($class).')';
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
                $model = $businessBundle->getBusinessModel($field->getType());
                if (null !== $model) {
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
            $result = '['.$class.'](../DataModel/Overview.md#'.strtolower($class).')';
        }

        return $result;
    }
}
