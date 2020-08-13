<?php

namespace CodePrimer\Tests\Functional;

use CodePrimer\Template\Artifact;
use CodePrimer\Twig\LanguageTwigExtension;

/**
 * Class MarkdownDocumentationTemplatesTest.
 *
 * @group functional
 */
class MarkdownDocumentationTemplatesTest extends TemplateTestCase
{
    const DOCUMENTATION_EXPECTED_DIR = self::EXPECTED_DIR.'/documentation/markdown/model/';

    /**
     * @throws \Exception
     */
    public function testDataModelOverviewTemplate()
    {
        $this->initEntities();

        self::assertCount(6, $this->businessBundle->getBusinessModels());

        $artifact = new Artifact(Artifact::DOCUMENTATION, 'model', 'markdown');

        // Extract the template to use for this artifact
        $template = $this->templateRegistry->getTemplateForArtifact($artifact);
        self::assertNotNull($template);

        // Extract the builder to use for this artifact
        $builder = $this->factory->createBuilder($artifact);
        self::assertNotNull($builder);

        // Build the artifacts
        $builder->build($this->businessBundle, $template, $this->renderer);

        // Make sure the right files have been generated
        $this->assertGeneratedFile('docs/DataModel/Overview.md', self::DOCUMENTATION_EXPECTED_DIR);
    }

    /**
     * @throws \Exception
     */
    public function testProcessOverviewTemplate()
    {
        $this->initEntities();

        self::assertCount(3, $this->businessBundle->getBusinessProcesses());

        $artifact = new Artifact(Artifact::DOCUMENTATION, 'process', 'markdown', 'index');

        // Extract the template to use for this artifact
        $template = $this->templateRegistry->getTemplateForArtifact($artifact);
        self::assertNotNull($template);

        // Extract the builder to use for this artifact
        $builder = $this->factory->createBuilder($artifact);
        self::assertNotNull($builder);

        // Build the artifacts
        $builder->build($this->businessBundle, $template, $this->renderer);

        // Make sure the right files have been generated
        $this->assertGeneratedFile('docs/Process/Overview.md', self::DOCUMENTATION_EXPECTED_DIR);
    }

    /**
     * @throws \Exception
     */
    public function testProcessDetailsTemplates()
    {
        $this->initEntities();
        $languageExtension = new LanguageTwigExtension();

        $businessProcesses = $this->businessBundle->getBusinessProcesses();
        self::assertCount(3, $businessProcesses);

        $artifact = new Artifact(Artifact::DOCUMENTATION, 'process', 'markdown', 'details');

        // Extract the template to use for this artifact
        $template = $this->templateRegistry->getTemplateForArtifact($artifact);
        self::assertNotNull($template);

        // Extract the builder to use for this artifact
        $builder = $this->factory->createBuilder($artifact);
        self::assertNotNull($builder);

        // Build the artifacts
        $builder->build($this->businessBundle, $template, $this->renderer);

        // Make sure the right files have been generated
        foreach ($businessProcesses as $businessProcess) {
            $class = $languageExtension->classFilter($businessProcess);
            $this->assertGeneratedFile("docs/Process/$class.md", self::DOCUMENTATION_EXPECTED_DIR);
        }
    }
}
