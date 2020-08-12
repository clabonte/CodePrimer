<?php

namespace CodePrimer\Builder;

use CodePrimer\Helper\BusinessBundleHelper;
use CodePrimer\Helper\BusinessModelHelper;
use CodePrimer\Helper\DataBundleHelper;
use CodePrimer\Helper\FieldHelper;
use CodePrimer\Model\BusinessBundle;
use CodePrimer\Renderer\TemplateRenderer;
use CodePrimer\Template\Template;
use CodePrimer\Twig\LanguageTwigExtension;

class BundleDocumentationBuilder implements ArtifactBuilder
{
    /**
     * {@inheritdoc}
     */
    public function build(BusinessBundle $bundle, Template $template, TemplateRenderer $renderer): array
    {
        $files = [];
        switch ($template->getArtifact()->getVariant()) {
            case 'details':
                $files[] = $this->buildBusinessProcessFiles($bundle, $template, $renderer);
                break;
            default:
                $files[] = $this->buildSingleFile($bundle, $template, $renderer);
        }

        return $files;
    }

    private function buildSingleFile(BusinessBundle $bundle, Template $template, TemplateRenderer $renderer)
    {
        $context = [
            'bundle' => $bundle,
            'businessBundleHelper' => new BusinessBundleHelper(),
            'businessModelHelper' => new BusinessModelHelper(),
            'dataBundleHelper' => new DataBundleHelper($bundle),
            'fieldHelper' => new FieldHelper(),
        ];

        return $renderer->renderToFile($template->getName(), $bundle, $template, $context);
    }

    private function buildBusinessProcessFiles(BusinessBundle $bundle, Template $template, TemplateRenderer $renderer)
    {
        $context = [
            'bundle' => $bundle,
            'businessBundleHelper' => new BusinessBundleHelper(),
            'businessModelHelper' => new BusinessModelHelper(),
            'dataBundleHelper' => new DataBundleHelper($bundle),
            'fieldHelper' => new FieldHelper(),
        ];

        $languageExtension = new LanguageTwigExtension();
        $files = [];
        foreach ($bundle->getBusinessProcesses() as $businessProcess) {
            $context['businessProcess'] = $businessProcess;
            $filename = $languageExtension->classFilter($businessProcess->getName());
            $files[] = $renderer->renderToFile($filename, $bundle, $template, $context);
        }

        return $files;
    }
}
