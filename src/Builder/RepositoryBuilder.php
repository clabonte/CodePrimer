<?php

namespace CodePrimer\Builder;

use CodePrimer\Helper\BusinessModelHelper;
use CodePrimer\Helper\FieldHelper;
use CodePrimer\Model\BusinessModel;
use CodePrimer\Model\Package;
use CodePrimer\Renderer\TemplateRenderer;
use CodePrimer\Template\Template;

class RepositoryBuilder implements ArtifactBuilder
{
    /**
     * @return string[]
     *
     * @throws \Exception
     */
    public function build(Package $package, Template $template, TemplateRenderer $renderer): array
    {
        $files = [];
        foreach ($package->getBusinessModels() as $businessModel) {
            $files[] = $this->buildRepository($package, $businessModel, $template, $renderer);
        }

        return $files;
    }

    /**
     * @throws \Exception
     */
    protected function buildRepository(Package $package, BusinessModel $businessModel, Template $template, TemplateRenderer $renderer): string
    {
        $businessModelHelper = new BusinessModelHelper();
        $model = $businessModelHelper->getRepositoryClass($businessModel);

        $context = [
            'package' => $package,
            'subpackage' => 'Repository',
            'model' => $model,
            'entity' => $businessModel,
            'entityHelper' => $businessModelHelper,
            'fieldHelper' => new FieldHelper(),
        ];

        return $renderer->renderToFile($model, $package, $template, $context);
    }
}
