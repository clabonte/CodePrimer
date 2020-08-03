<?php

namespace CodePrimer\Builder;

use CodePrimer\Helper\EntityHelper;
use CodePrimer\Helper\FieldHelper;
use CodePrimer\Model\BusinessModel;
use CodePrimer\Model\Package;
use CodePrimer\Renderer\TemplateRenderer;
use CodePrimer\Template\Template;

class EntityBuilder implements ArtifactBuilder
{
    /**
     * @return string[]
     *
     * @throws \Exception
     */
    public function build(Package $package, Template $template, TemplateRenderer $renderer): array
    {
        $files = [];
        foreach ($package->getEntities() as $businessModel) {
            $files[] = $this->buildEntity($package, $businessModel, $template, $renderer);
        }

        return $files;
    }

    /**
     * @throws \Exception
     */
    protected function buildEntity(Package $package, BusinessModel $businessModel, Template $template, TemplateRenderer $renderer): string
    {
        $context = [
            'package' => $package,
            'subpackage' => 'Entity',
            'model' => $businessModel,
            'entity' => $businessModel,
            'entityHelper' => new EntityHelper(),
            'fieldHelper' => new FieldHelper(),
        ];

        return $renderer->renderToFile($businessModel->getName(), $package, $template, $context);
    }
}
