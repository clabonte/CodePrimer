<?php

namespace CodePrimer\Builder;

use CodePrimer\Helper\BusinessModelHelper;
use CodePrimer\Helper\FieldHelper;
use CodePrimer\Model\BusinessBundle;
use CodePrimer\Model\DataSet;
use CodePrimer\Renderer\TemplateRenderer;
use CodePrimer\Template\Template;
use CodePrimer\Twig\LanguageTwigExtension;

class DataSetBuilder implements ArtifactBuilder
{
    /**
     * @return string[]
     *
     * @throws \Exception
     */
    public function build(BusinessBundle $businessBundle, Template $template, TemplateRenderer $renderer): array
    {
        $files = [];
        foreach ($businessBundle->getDataSets() as $dataSet) {
            $files[] = $this->buildDataSet($businessBundle, $dataSet, $template, $renderer);
        }

        return $files;
    }

    /**
     * @throws \Exception
     */
    protected function buildDataSet(BusinessBundle $businessBundle, DataSet $dataSet, Template $template, TemplateRenderer $renderer): string
    {
        $context = [
            'bundle' => $businessBundle,
            'subpackage' => 'Dataset',
            'model' => $dataSet,
            'dataSet' => $dataSet,
            'businessModelHelper' => new BusinessModelHelper(),
            'fieldHelper' => new FieldHelper(),
        ];

        $languageExtension = new LanguageTwigExtension();
        $filename = $languageExtension->classFilter($dataSet->getName());

        return $renderer->renderToFile($filename, $businessBundle, $template, $context);
    }
}
