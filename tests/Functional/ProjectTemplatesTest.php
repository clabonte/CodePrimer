<?php

namespace CodePrimer\Tests\Functional;

use CodePrimer\Template\Artifact;

/**
 * Class ProjectTemplatesTest.
 *
 * @group functional
 */
class ProjectTemplatesTest extends TemplateTestCase
{
    const SYMFONY_EXPECTED_DIR = self::EXPECTED_DIR.'project/symfony/';
    const PHP_EXPECTED_DIR = self::EXPECTED_DIR.'project/php/';

    /**
     * @throws \Exception
     */
    public function testSymfonySetupScriptsTemplate()
    {
        $this->initEntities();

        $artifact = new Artifact(Artifact::PROJECT, 'Symfony', 'sh', 'setup');

        // Extract the template to use for this artifact
        $template = $this->templateRegistry->getTemplateForArtifact($artifact);
        self::assertNotNull($template);

        // Extract the builder to use for this artifact
        $builder = $this->factory->createBuilder($artifact);
        self::assertNotNull($builder);

        // Build the artifacts
        $builder->build($this->businessBundle, $template, $this->renderer);

        // Make sure the right files have been generated
        $this->assertGeneratedFile('setup.sh', self::SYMFONY_EXPECTED_DIR);
    }

    public function testPlainPhpProjectSetupTemplate()
    {
        $this->initEntities();

        $artifact = new Artifact(Artifact::PROJECT, 'PHP', 'json', 'composer');

        // Extract the template to use for this artifact
        $template = $this->templateRegistry->getTemplateForArtifact($artifact);
        self::assertNotNull($template);

        // Extract the builder to use for this artifact
        $builder = $this->factory->createBuilder($artifact);
        self::assertNotNull($builder);

        // Build the artifacts
        $builder->build($this->businessBundle, $template, $this->renderer);

        // Make sure the right files have been generated
        $this->assertGeneratedFile('composer.json', self::PHP_EXPECTED_DIR);
    }
}
