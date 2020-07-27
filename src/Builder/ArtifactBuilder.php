<?php

namespace CodePrimer\Builder;

use CodePrimer\Model\Package;
use CodePrimer\Renderer\TemplateRenderer;
use CodePrimer\Template\Template;
use Exception;

interface ArtifactBuilder
{
    /**
     * @return string[] List of files generated
     *
     * @throws Exception
     */
    public function build(Package $package, Template $template, TemplateRenderer $renderer): array;
}
