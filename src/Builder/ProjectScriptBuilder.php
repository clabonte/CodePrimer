<?php

namespace CodePrimer\Builder;

use CodePrimer\Model\Package;
use CodePrimer\Renderer\TemplateRenderer;
use CodePrimer\Template\Template;
use Doctrine\Common\Inflector\Inflector;

class ProjectScriptBuilder implements ArtifactBuilder
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
        $artifact = $template->getArtifact();
        $filename = strtolower($artifact->getVariant());

        $project = array();

        $project['name'] = Inflector::classify($package->getName());

        $context = [
            'project' => $project,
            'package' => $package
        ];

        $file = $renderer->renderToFile($filename, $package, $template, $context);
        if (file_exists($file)) {
            chmod($file, 0755);
        }

        return [$file];
    }
}
