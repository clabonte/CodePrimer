<?php

namespace CodePrimer\Tests\Functional;

use CodePrimer\Template\Artifact;

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
    public function testDataModelTemplate()
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
        $this->assertGeneratedFile('docs/DataModel.md', self::DOCUMENTATION_EXPECTED_DIR);
    }
}
