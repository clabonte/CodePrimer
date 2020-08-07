<?php

namespace CodePrimer\Builder;

use CodePrimer\Helper\BusinessModelHelper;
use CodePrimer\Helper\FieldHelper;
use CodePrimer\Model\BusinessBundle;
use CodePrimer\Model\BusinessModel;
use CodePrimer\Renderer\TemplateRenderer;
use CodePrimer\Template\Template;

class BusinessModelBuilder implements ArtifactBuilder
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
            $files[] = $this->buildBusinessModel($businessBundle, $businessModel, $template, $renderer);
        }

        return $files;
    }

    /**
     * @throws \Exception
     */
    protected function buildBusinessModel(BusinessBundle $businessBundle, BusinessModel $businessModel, Template $template, TemplateRenderer $renderer): string
    {
        $context = [
            'package' => $businessBundle,
            'subpackage' => 'Model',
            'model' => $businessModel,
            'businessModel' => $businessModel,
            'businessModelHelper' => new BusinessModelHelper(),
            'fieldHelper' => new FieldHelper(),
        ];

        return $renderer->renderToFile($businessModel->getName(), $businessBundle, $template, $context);
    }
}
