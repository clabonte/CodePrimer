<?php


namespace CodePrimer\Tests\Functional;


use CodePrimer\Template\Artifact;

/**
 * Class JavaEntityTemplatesTest
 * @package App\Tests\Functional
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

        self::assertCount(6, $this->package->getEntities());

        $artifact = new Artifact(Artifact::CODE, 'Entity', 'java');

        // Extract the template to use for this artifact
        $template = $this->templateRegistry->getTemplateForArtifact($artifact);
        self::assertNotNull($template);

        // Extract the builder to use for this artifact
        $builder = $this->factory->createBuilder($artifact);
        self::assertNotNull($builder);

        // Build the artifacts
        $builder->build($this->package, $template, $this->renderer);

        // Make sure the right files have been generated
        $this->assertGeneratedFile('src/CodePrimer/Tests/Entity/User.java', self::PLAIN_ENTITY_EXPECTED_DIR);
        $this->assertGeneratedFile('src/CodePrimer/Tests/Entity/UserStats.java', self::PLAIN_ENTITY_EXPECTED_DIR);
        $this->assertGeneratedFile('src/CodePrimer/Tests/Entity/Metadata.java', self::PLAIN_ENTITY_EXPECTED_DIR);
        $this->assertGeneratedFile('src/CodePrimer/Tests/Entity/Post.java', self::PLAIN_ENTITY_EXPECTED_DIR);
        $this->assertGeneratedFile('src/CodePrimer/Tests/Entity/Topic.java', self::PLAIN_ENTITY_EXPECTED_DIR);
        $this->assertGeneratedFile('src/CodePrimer/Tests/Entity/Subscription.java', self::PLAIN_ENTITY_EXPECTED_DIR);
    }

}