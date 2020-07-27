<?php

namespace CodePrimer\Tests\Application;

use CodePrimer\Adapter\RelationalDatabaseAdapter;
use CodePrimer\Template\Artifact;
use CodePrimer\Tests\Functional\TemplateTestCase;

/**
 * Class ApplicationTest.
 *
 * @group application
 */
class ApplicationTest extends TemplateTestCase
{
    const SYMFONY_APP_FOLDER = self::ROOT.'/../FunctionalTest/';

    /**
     * @throws \Exception
     */
    public function testSymfonyApplicationCodeGeneration()
    {
        // ----------------------------------
        // Configure the application settings
        // ----------------------------------
        // Prepare the package
        $this->initEntities();
        $this->package->setNamespace('App');

        // Configure the output folder of the renderer
        $this->renderer->setBaseFolder(self::SYMFONY_APP_FOLDER);

        // Prepare the entities for Doctrine ORM
        $adapter = new RelationalDatabaseAdapter();
        $adapter->generateRelationalFields($this->package);

        // ------------------------------
        // Generate the Doctrine entities
        // ------------------------------
        $artifact = new Artifact(Artifact::CODE, 'Entity', 'php', 'doctrineOrm');

        // Extract the template to use for this artifact
        $template = $this->templateRegistry->getTemplateForArtifact($artifact);
        self::assertNotNull($template);

        // Extract the builder to use for this artifact
        $builder = $this->factory->createBuilder($artifact);
        self::assertNotNull($builder);

        // Build the artifacts
        $builder->build($this->package, $template, $this->renderer);

        // ----------------------------------
        // Generate the Doctrine repositories
        // ----------------------------------
        $artifact = new Artifact(Artifact::CODE, 'Repository', 'php', 'doctrineOrm');

        // Extract the template to use for this artifact
        $template = $this->templateRegistry->getTemplateForArtifact($artifact);
        self::assertNotNull($template);

        // Extract the builder to use for this artifact
        $builder = $this->factory->createBuilder($artifact);
        self::assertNotNull($builder);

        // Build the artifacts
        $builder->build($this->package, $template, $this->renderer);

        // ----------------------------------
        // Generate the SQL migration scripts
        // ----------------------------------
        $artifact = new Artifact(Artifact::CODE, 'Migration', 'mysql', 'CreateDatabase');

        // Extract the template to use for this artifact
        $template = $this->templateRegistry->getTemplateForArtifact($artifact);
        self::assertNotNull($template);

        // Extract the builder to use for this artifact
        $builder = $this->factory->createBuilder($artifact);
        self::assertNotNull($builder);

        // Build the artifacts
        $builder->build($this->package, $template, $this->renderer);

        $artifact = new Artifact(Artifact::CODE, 'Migration', 'mysql', 'RevertDatabase');

        // Extract the template to use for this artifact
        $template = $this->templateRegistry->getTemplateForArtifact($artifact);
        self::assertNotNull($template);

        // Extract the builder to use for this artifact
        $builder = $this->factory->createBuilder($artifact);
        self::assertNotNull($builder);

        // Build the artifacts
        $builder->build($this->package, $template, $this->renderer);

        // ----------------------------------
        // Generate the Doctrine migration
        // ----------------------------------
        $artifact = new Artifact(Artifact::CODE, 'Migration', 'php', 'Doctrine');

        // Extract the template to use for this artifact
        $template = $this->templateRegistry->getTemplateForArtifact($artifact);
        self::assertNotNull($template);

        // Extract the builder to use for this artifact
        $builder = $this->factory->createBuilder($artifact);
        self::assertNotNull($builder);

        // Build the artifacts
        $builder->build($this->package, $template, $this->renderer);
    }
}
