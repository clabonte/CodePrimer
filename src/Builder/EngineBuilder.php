<?php

namespace CodePrimer\Builder;

use CodePrimer\Model\BusinessBundle;
use CodePrimer\Renderer\TemplateRenderer;
use CodePrimer\Template\Template;
use CodePrimer\Twig\LanguageTwigExtension;

class EngineBuilder implements ArtifactBuilder
{
    private $interface = true;

    /**
     * {@inheritdoc}
     */
    public function build(BusinessBundle $businessBundle, Template $template, TemplateRenderer $renderer): array
    {
        $files = [];
        $categories = $businessBundle->getBusinessProcessCategories();
        foreach ($categories as $category) {
            $files[] = $this->buildEngine($businessBundle, $category, $template, $renderer);
        }

        return $files;
    }

    private function buildEngine(BusinessBundle $businessBundle, string $category, Template $template, TemplateRenderer $renderer): string
    {
        $languageExtension = new LanguageTwigExtension();

        if (empty($category)) {
            $model = 'Default';
        } else {
            $model = $languageExtension->singularFilter($category);
        }
        $model .= ' Engine';
        if ($this->interface) {
            $model .= ' Interface';
        }

        $processes = $businessBundle->getBusinessProcessesForCategory($category);
        $events = [];
        foreach ($processes as $process) {
            $event = $process->getEvent();
            $events[$event->getName()] = $event;
        }

        $context = [
            'bundle' => $businessBundle,
            'subpackage' => 'Engine',
            'model' => $model,
            'processes' => $processes,
            'events' => $events,
        ];

        $filename = $languageExtension->classFilter($model);

        return $renderer->renderToFile($filename, $businessBundle, $template, $context);
    }
}
