<?php

namespace CodePrimer\Tests\Functional;

use CodePrimer\Adapter\RelationalDatabaseAdapter;
use CodePrimer\Template\Artifact;

/**
 * Class PhpMigrationTemplatesTest.
 *
 * @group functional
 */
class PhpMigrationTemplatesTest extends TemplateTestCase
{
    const DOCTRINE_EXPECTED_DIR = self::EXPECTED_DIR.'/code/php/migration/DoctrineMigration/';

    /**
     * @throws \Exception
     */
    public function testDoctrineMigrationTemplate()
    {
        $this->initEntities();

        self::assertCount(6, $this->package->getBusinessModels());

        $artifact = new Artifact(Artifact::CODE, 'Migration', 'php', 'doctrine');

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
        $files = $builder->build($this->package, $template, $this->renderer);

        self::assertCount(1, $files);

        // Make sure the right files have been generated
        $this->assertGeneratedFile('src/Migrations/Version00000000000001.php', self::DOCTRINE_EXPECTED_DIR);
    }
}
