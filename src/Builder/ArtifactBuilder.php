<?php

namespace CodePrimer\Builder;

use CodePrimer\Model\BusinessBundle;
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
    public function build(BusinessBundle $businessBundle, Template $template, TemplateRenderer $renderer): array;
}
