<?php

namespace CodePrimer\Builder;

use CodePrimer\Helper\EntityHelper;
use CodePrimer\Helper\FieldHelper;
use CodePrimer\Model\Entity;
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
        foreach ($package->getEntities() as $entity) {
            $files[] = $this->buildRepository($package, $entity, $template, $renderer);
        }

        return $files;
    }

    /**
     * @throws \Exception
     */
    protected function buildRepository(Package $package, Entity $entity, Template $template, TemplateRenderer $renderer): string
    {
        $entityHelper = new EntityHelper();
        $model = $entityHelper->getRepositoryClass($entity);

        $context = [
            'package' => $package,
            'subpackage' => 'Repository',
            'model' => $model,
            'entity' => $entity,
            'entityHelper' => $entityHelper,
            'fieldHelper' => new FieldHelper(),
        ];

        return $renderer->renderToFile($model, $package, $template, $context);
    }
}
