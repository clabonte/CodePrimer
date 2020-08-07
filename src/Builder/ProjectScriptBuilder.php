<?php

namespace CodePrimer\Builder;

use CodePrimer\Model\BusinessBundle;
use CodePrimer\Renderer\TemplateRenderer;
use CodePrimer\Template\Template;
use Doctrine\Common\Inflector\Inflector;

class ProjectScriptBuilder implements ArtifactBuilder
{
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

        $project['name'] = Inflector::classify($businessBundle->getName());

        $context = [
            'project' => $project,
            'package' => $businessBundle,
        ];

        $file = $renderer->renderToFile($filename, $businessBundle, $template, $context);
        if (file_exists($file)) {
            chmod($file, 0755);
        }

        return [$file];
    }
}
