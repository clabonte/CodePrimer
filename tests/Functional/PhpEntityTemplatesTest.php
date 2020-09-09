<?php

namespace CodePrimer\Tests\Functional;

use CodePrimer\Adapter\RelationalDatabaseAdapter;
use CodePrimer\Template\Artifact;

/**
 * Class PhpEntityTemplatesTest.
 *
 * @group functional
 */
class PhpEntityTemplatesTest extends TemplateTestCase
{
    const PLAIN_ENTITY_EXPECTED_DIR = self::EXPECTED_DIR.'/code/php/entity/PlainEntity/';
    const DOCTRINE_ORM_ENTITY_EXPECTED_DIR = self::EXPECTED_DIR.'/code/php/entity/DoctrineOrmEntity/';

    /**
     * @throws \Exception
     */
    public function testPlainEntityTemplate()
    {
        $this->initEntities();

        self::assertCount(6, $this->businessBundle->getBusinessModels());

        $artifact = new Artifact(Artifact::CODE, 'Entity', 'php');

        // Extract the template to use for this artifact
        $template = $this->templateRegistry->getTemplateForArtifact($artifact);
        self::assertNotNull($template);

        // Extract the builder to use for this artifact
        $builder = $this->factory->createBuilder($artifact);
        self::assertNotNull($builder);

        // Build the artifacts
        $builder->build($this->businessBundle, $template, $this->renderer);

        // Make sure the right files have been generated
        $this->assertGeneratedFile('gen-src/Entity/User.php', self::PLAIN_ENTITY_EXPECTED_DIR);
        $this->assertGeneratedFile('gen-src/Entity/UserStats.php', self::PLAIN_ENTITY_EXPECTED_DIR);
        $this->assertGeneratedFile('gen-src/Entity/Metadata.php', self::PLAIN_ENTITY_EXPECTED_DIR);
        $this->assertGeneratedFile('gen-src/Entity/Post.php', self::PLAIN_ENTITY_EXPECTED_DIR);
        $this->assertGeneratedFile('gen-src/Entity/Topic.php', self::PLAIN_ENTITY_EXPECTED_DIR);
        $this->assertGeneratedFile('gen-src/Entity/Subscription.php', self::PLAIN_ENTITY_EXPECTED_DIR);
    }

    /**
     * @throws \Exception
     */
    public function testDoctrineOrmEntityTemplate()
    {
        $this->initEntities();

        self::assertCount(6, $this->businessBundle->getBusinessModels());

        $artifact = new Artifact(Artifact::CODE, 'Entity', 'php', 'doctrineOrm');

        // Extract the template to use for this artifact
        $template = $this->templateRegistry->getTemplateForArtifact($artifact);
        self::assertNotNull($template);

        // Extract the builder to use for this artifact
        $builder = $this->factory->createBuilder($artifact);
        self::assertNotNull($builder);

        // Prepare the entities for Doctrine ORM
        $adapter = new RelationalDatabaseAdapter();
        $adapter->generateRelationalFields($this->businessBundle);

        // Build the artifacts
        $builder->build($this->businessBundle, $template, $this->renderer);

        // Make sure the right files have been generated
        $this->assertGeneratedFile('gen-src/Entity/User.php', self::DOCTRINE_ORM_ENTITY_EXPECTED_DIR);
        $this->assertGeneratedFile('gen-src/Entity/UserStats.php', self::DOCTRINE_ORM_ENTITY_EXPECTED_DIR);
        $this->assertGeneratedFile('gen-src/Entity/Metadata.php', self::DOCTRINE_ORM_ENTITY_EXPECTED_DIR);
        $this->assertGeneratedFile('gen-src/Entity/Post.php', self::DOCTRINE_ORM_ENTITY_EXPECTED_DIR);
        $this->assertGeneratedFile('gen-src/Entity/Topic.php', self::DOCTRINE_ORM_ENTITY_EXPECTED_DIR);
        $this->assertGeneratedFile('gen-src/Entity/Subscription.php', self::DOCTRINE_ORM_ENTITY_EXPECTED_DIR);
    }
}
