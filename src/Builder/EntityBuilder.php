<?php


namespace CodePrimer\Builder;

use CodePrimer\Helper\EntityHelper;
use CodePrimer\Helper\FieldHelper;
use CodePrimer\Model\Entity;
use CodePrimer\Model\Package;
use CodePrimer\Renderer\TemplateRenderer;
use CodePrimer\Template\Template;

class EntityBuilder implements ArtifactBuilder
{
    /**
     * @param Package $package
     * @param Template $template
     * @param TemplateRenderer $renderer
     * @return string[]
     * @throws \Exception
     */
    public function build(Package $package, Template $template, TemplateRenderer $renderer): array
    {
        $files = [];
        foreach ($package->getEntities() as $entity) {
            $files[] = $this->buildEntity($package, $entity, $template, $renderer);
        }

        return $files;
    }

    /**
     * @param Package $package
     * @param Entity $entity
     * @param Template $template
     * @param TemplateRenderer $renderer
     * @throws \Exception
     */
    protected function buildEntity(Package $package, Entity $entity, Template $template, TemplateRenderer $renderer): string
    {
        $context = [
            'package' => $package,
            'subpackage' => 'Entity',
            'model' => $entity,
            'entity' => $entity,
            'entityHelper' => new EntityHelper(),
            'fieldHelper' => new FieldHelper()
        ];

        return $renderer->renderToFile($entity->getName(), $package, $template, $context);
    }
}
