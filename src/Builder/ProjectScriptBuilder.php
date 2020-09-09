<?php

namespace CodePrimer\Builder;

use CodePrimer\Model\BusinessBundle;
use CodePrimer\Renderer\TemplateRenderer;
use CodePrimer\Template\Template;
use Doctrine\Inflector\InflectorFactory;

class ProjectScriptBuilder implements ArtifactBuilder
{
    const PHP_CS_FIXER_FORMAT = 'PHP CS Fixer';

    /**
     * @return string[]
     *
     * @throws \Exception
     */
    public function build(BusinessBundle $businessBundle, Template $template, TemplateRenderer $renderer): array
    {
        $artifact = $template->getArtifact();
        $filename = strtolower($artifact->getVariant());

        $project = [];

        $inflector = InflectorFactory::create()->build();
        $project['name'] = $inflector->classify($businessBundle->getName());

        $context = [
            'project' => $project,
            'package' => $businessBundle,
            'bundle' => $businessBundle,
            'variant' => $artifact->getVariant(),
        ];

        $file = $renderer->renderToFile($filename, $businessBundle, $template, $context);
        if (file_exists($file)) {
            chmod($file, 0755);
        }

        return [$file];
    }
}
