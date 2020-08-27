<?php

namespace CodePrimer\Builder;

use CodePrimer\Helper\BusinessModelHelper;
use CodePrimer\Helper\FieldHelper;
use CodePrimer\Model\BusinessBundle;
use CodePrimer\Model\Dataset;
use CodePrimer\Renderer\TemplateRenderer;
use CodePrimer\Template\Template;
use CodePrimer\Twig\LanguageTwigExtension;

class DatasetBuilder implements ArtifactBuilder
{
    /**
     * @return string[]
     *
     * @throws \Exception
     */
    public function build(BusinessBundle $businessBundle, Template $template, TemplateRenderer $renderer): array
    {
        $files = [];
        foreach ($businessBundle->getDatasets() as $dataset) {
            $files[] = $this->buildDataset($businessBundle, $dataset, $template, $renderer);
        }

        return $files;
    }

    /**
     * @throws \Exception
     */
    protected function buildDataset(BusinessBundle $businessBundle, Dataset $dataset, Template $template, TemplateRenderer $renderer): string
    {
        $context = [
            'bundle' => $businessBundle,
            'subpackage' => 'Dataset',
            'model' => $dataset,
            'dataset' => $dataset,
            'businessModelHelper' => new BusinessModelHelper(),
            'fieldHelper' => new FieldHelper(),
        ];

        $languageExtension = new LanguageTwigExtension();
        $filename = $languageExtension->classFilter($dataset->getName());

        return $renderer->renderToFile($filename, $businessBundle, $template, $context);
    }
}
