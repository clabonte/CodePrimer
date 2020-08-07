<?php

namespace CodePrimer\Builder;

use CodePrimer\Helper\BusinessModelHelper;
use CodePrimer\Helper\FieldHelper;
use CodePrimer\Model\BusinessBundle;
use CodePrimer\Model\BusinessModel;
use CodePrimer\Renderer\TemplateRenderer;
use CodePrimer\Template\Template;

class RepositoryBuilder implements ArtifactBuilder
{
    /**
     * @return string[]
     *
     * @throws \Exception
     */
    public function build(BusinessBundle $businessBundle, Template $template, TemplateRenderer $renderer): array
    {
        $files = [];
        foreach ($businessBundle->getBusinessModels() as $businessModel) {
            $files[] = $this->buildRepository($businessBundle, $businessModel, $template, $renderer);
        }

        return $files;
    }

    /**
     * @throws \Exception
     */
    protected function buildRepository(BusinessBundle $businessBundle, BusinessModel $businessModel, Template $template, TemplateRenderer $renderer): string
    {
        $businessModelHelper = new BusinessModelHelper();
        $model = $businessModelHelper->getRepositoryClass($businessModel);

        $context = [
            'package' => $businessBundle,
            'subpackage' => 'Repository',
            'model' => $model,
            'entity' => $businessModel,
            'entityHelper' => $businessModelHelper,
            'fieldHelper' => new FieldHelper(),
        ];

        return $renderer->renderToFile($model, $businessBundle, $template, $context);
    }
}
