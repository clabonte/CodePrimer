<?php

namespace CodePrimer\Builder;

use CodePrimer\Helper\BusinessModelHelper;
use CodePrimer\Helper\FieldHelper;
use CodePrimer\Model\BusinessModel;
use CodePrimer\Model\Package;
use CodePrimer\Renderer\TemplateRenderer;
use CodePrimer\Template\Template;

class BusinessModelBuilder implements ArtifactBuilder
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
            $files[] = $this->buildBusinessModel($package, $businessModel, $template, $renderer);
        }

        return $files;
    }

    /**
     * @throws \Exception
     */
    protected function buildBusinessModel(Package $package, BusinessModel $businessModel, Template $template, TemplateRenderer $renderer): string
    {
        $context = [
            'package' => $package,
            'subpackage' => 'Entity',
            'model' => $businessModel,
            'entity' => $businessModel,
            'entityHelper' => new BusinessModelHelper(),
            'fieldHelper' => new FieldHelper(),
        ];

        return $renderer->renderToFile($businessModel->getName(), $package, $template, $context);
    }
}
