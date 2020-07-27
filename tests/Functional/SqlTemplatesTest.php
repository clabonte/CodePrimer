<?php

namespace CodePrimer\Tests\Functional;

use CodePrimer\Adapter\RelationalDatabaseAdapter;
use CodePrimer\Template\Artifact;

/**
 * Class SqlTemplatesTest.
 *
 * @group functional
 */
class SqlTemplatesTest extends TemplateTestCase
{
    const MYSQL_EXPECTED_DIR = self::EXPECTED_DIR.'code/mysql/';

    /**
     * @throws \Exception
     */
    public function testMySqlCreateDatabaseTemplate()
    {
        $this->initEntities();
        $artifact = new Artifact(Artifact::CODE, 'Migration', 'mysql', 'CreateDatabase');

        // Extract the template to use for this artifact
        $template = $this->templateRegistry->getTemplateForArtifact($artifact);
        self::assertNotNull($template);

        // Extract the builder to use for this artifact
        $builder = $this->factory->createBuilder($artifact);
        self::assertNotNull($builder);

        // Prepare the entities for Doctrine ORM
        $adapter = new RelationalDatabaseAdapter();
        $adapter->generateRelationalFields($this->package);

        // Build the artifacts
        $builder->build($this->package, $template, $this->renderer);

        // Make sure the right files have been generated
        $this->assertGeneratedFile('migrations/CreateDatabase.sql', self::MYSQL_EXPECTED_DIR);
    }

    /**
     * @throws \Exception
     */
    public function testMySqlRevertDatabaseTemplate()
    {
        $this->initEntities();
        $artifact = new Artifact(Artifact::CODE, 'Migration', 'mysql', 'RevertDatabase');

        // Extract the template to use for this artifact
        $template = $this->templateRegistry->getTemplateForArtifact($artifact);
        self::assertNotNull($template);

        // Extract the builder to use for this artifact
        $builder = $this->factory->createBuilder($artifact);
        self::assertNotNull($builder);

        // Prepare the entities for Doctrine ORM
        $adapter = new RelationalDatabaseAdapter();
        $adapter->generateRelationalFields($this->package);

        // Build the artifacts
        $builder->build($this->package, $template, $this->renderer);

        // Make sure the right files have been generated
        $this->assertGeneratedFile('migrations/RevertDatabase.sql', self::MYSQL_EXPECTED_DIR);
    }

    /**
     * @throws \Exception
     */
    public function testMySqlCreateUserTemplate()
    {
        $this->initEntities();
        $artifact = new Artifact(Artifact::CODE, 'Migration', 'mysql', 'CreateUser');

        // Extract the template to use for this artifact
        $template = $this->templateRegistry->getTemplateForArtifact($artifact);
        self::assertNotNull($template);

        // Extract the builder to use for this artifact
        $builder = $this->factory->createBuilder($artifact);
        self::assertNotNull($builder);

        // Prepare the entities for Doctrine ORM
        $adapter = new RelationalDatabaseAdapter();
        $adapter->generateRelationalFields($this->package);

        // Build the artifacts
        $builder->build($this->package, $template, $this->renderer);

        // Make sure the right files have been generated
        $this->assertGeneratedFile('migrations/CreateUser.sql', self::MYSQL_EXPECTED_DIR);
    }
}
