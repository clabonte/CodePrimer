<?php

namespace CodePrimer\Tests\Functional;

use CodePrimer\Template\Artifact;

/**
 * Class JavaEntityTemplatesTest.
 *
 * @group functional
 */
class JavaEntityTemplatesTest extends TemplateTestCase
{
    const PLAIN_ENTITY_EXPECTED_DIR = self::EXPECTED_DIR.'/code/java/entity/PlainEntity/';

    /**
     * @throws \Exception
     */
    public function testPlainEntityTemplate()
    {
        $this->initEntities();

        self::assertCount(6, $this->businessBundle->getBusinessModels());

        $artifact = new Artifact(Artifact::CODE, 'Entity', 'java');

        // Extract the template to use for this artifact
        $template = $this->templateRegistry->getTemplateForArtifact($artifact);
        self::assertNotNull($template);

        // Extract the builder to use for this artifact
        $builder = $this->factory->createBuilder($artifact);
        self::assertNotNull($builder);

        // Build the artifacts
        $builder->build($this->businessBundle, $template, $this->renderer);

        // Make sure the right files have been generated
        $this->assertGeneratedFile('gen-src/codeprimer/tests/entity/User.java', self::PLAIN_ENTITY_EXPECTED_DIR);
        $this->assertGeneratedFile('gen-src/codeprimer/tests/entity/UserStats.java', self::PLAIN_ENTITY_EXPECTED_DIR);
        $this->assertGeneratedFile('gen-src/codeprimer/tests/entity/Metadata.java', self::PLAIN_ENTITY_EXPECTED_DIR);
        $this->assertGeneratedFile('gen-src/codeprimer/tests/entity/Post.java', self::PLAIN_ENTITY_EXPECTED_DIR);
        $this->assertGeneratedFile('gen-src/codeprimer/tests/entity/Topic.java', self::PLAIN_ENTITY_EXPECTED_DIR);
        $this->assertGeneratedFile('gen-src/codeprimer/tests/entity/Subscription.java', self::PLAIN_ENTITY_EXPECTED_DIR);
    }
}
