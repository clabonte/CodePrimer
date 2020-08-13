<?php

namespace CodePrimer\Builder;

use CodePrimer\Helper\EventHelper;
use CodePrimer\Helper\FieldHelper;
use CodePrimer\Model\BusinessBundle;
use CodePrimer\Model\Derived\Event;
use CodePrimer\Renderer\TemplateRenderer;
use CodePrimer\Template\Template;
use CodePrimer\Twig\LanguageTwigExtension;

class EventBuilder implements ArtifactBuilder
{
    /**
     * @return string[]
     *
     * @throws \Exception
     */
    public function build(BusinessBundle $businessBundle, Template $template, TemplateRenderer $renderer): array
    {
        $files = [];
        foreach ($businessBundle->getEvents() as $event) {
            $files[] = $this->buildEvent($businessBundle, $event, $template, $renderer);
        }

        return $files;
    }

    /**
     * @throws \Exception
     */
    protected function buildEvent(BusinessBundle $businessBundle, Event $event, Template $template, TemplateRenderer $renderer): string
    {
        $context = [
            'bundle' => $businessBundle,
            'subpackage' => 'Event',
            'model' => $event,
            'event' => $event,
            'eventHelper' => new EventHelper(),
            'fieldHelper' => new FieldHelper(),
        ];
        $languageExtension = new LanguageTwigExtension();
        $filename = $languageExtension->classFilter($event->getName());

        return $renderer->renderToFile($filename, $businessBundle, $template, $context);
    }
}
