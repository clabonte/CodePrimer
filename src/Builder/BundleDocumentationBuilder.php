<?php

namespace CodePrimer\Builder;

use CodePrimer\Helper\BusinessModelHelper;
use CodePrimer\Helper\FieldHelper;
use CodePrimer\Model\BusinessBundle;
use CodePrimer\Renderer\TemplateRenderer;
use CodePrimer\Template\Template;

class BundleDocumentationBuilder implements ArtifactBuilder
{
    /**
     * {@inheritdoc}
     */
    public function build(BusinessBundle $bundle, Template $template, TemplateRenderer $renderer): array
    {
        $context = [
            'bundle' => $bundle,
            'businessModelHelper' => new BusinessModelHelper(),
            'fieldHelper' => new FieldHelper(),
        ];

        $files = [];
        $files[] = $renderer->renderToFile($template->getName(), $bundle, $template, $context);

        return $files;
    }
}
