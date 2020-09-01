<?php

namespace CodePrimer\Tests\Functional;

use CodePrimer\Template\Artifact;
use CodePrimer\Twig\LanguageTwigExtension;

/**
 * Class PhpEventTemplatesTest.
 *
 * @group functional
 */
class PhpEventTemplatesTest extends TemplateTestCase
{
    const EVENT_EXPECTED_DIR = self::EXPECTED_DIR.'/code/php/event/Event/';

    /**
     * @throws \Exception
     */
    public function testEventTemplate()
    {
        $this->initEntities();
        $languageExtension = new LanguageTwigExtension();

        $events = $this->businessBundle->getEvents();
        self::assertCount(4, $events);

        $artifact = new Artifact(Artifact::CODE, 'event', 'php');

        // Extract the template to use for this artifact
        $template = $this->templateRegistry->getTemplateForArtifact($artifact);
        self::assertNotNull($template);

        // Extract the builder to use for this artifact
        $builder = $this->factory->createBuilder($artifact);
        self::assertNotNull($builder);

        // Build the artifacts
        $builder->build($this->businessBundle, $template, $this->renderer);

        // Make sure the right files have been generated
        foreach ($events as $event) {
            $class = $languageExtension->classFilter($event);
            $this->assertGeneratedFile("gen-src/Event/$class.php", self::EVENT_EXPECTED_DIR);
        }
    }
}
