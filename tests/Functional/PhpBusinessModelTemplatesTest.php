<?php

namespace CodePrimer\Tests\Functional;

use CodePrimer\Template\Artifact;

/**
 * Class PhpBusinessModelTemplatesTest.
 *
 * @group functional
 */
class PhpBusinessModelTemplatesTest extends TemplateTestCase
{
    const BUSINESS_MODEL_EXPECTED_DIR = self::EXPECTED_DIR.'/code/php/model/BusinessModel/';

    /**
     * @throws \Exception
     */
    public function testBusinessModelTemplate()
    {
        $this->initEntities();

        self::assertCount(6, $this->businessBundle->getBusinessModels());

        $artifact = new Artifact(Artifact::CODE, 'model', 'php');

        // Extract the template to use for this artifact
        $template = $this->templateRegistry->getTemplateForArtifact($artifact);
        self::assertNotNull($template);

        // Extract the builder to use for this artifact
        $builder = $this->factory->createBuilder($artifact);
        self::assertNotNull($builder);

        // Build the artifacts
        $builder->build($this->businessBundle, $template, $this->renderer);

        // Make sure the right files have been generated
        $this->assertGeneratedFile('src/Model/User.php', self::BUSINESS_MODEL_EXPECTED_DIR);
        $this->assertGeneratedFile('src/Model/UserStats.php', self::BUSINESS_MODEL_EXPECTED_DIR);
        $this->assertGeneratedFile('src/Model/Metadata.php', self::BUSINESS_MODEL_EXPECTED_DIR);
        $this->assertGeneratedFile('src/Model/Post.php', self::BUSINESS_MODEL_EXPECTED_DIR);
        $this->assertGeneratedFile('src/Model/Topic.php', self::BUSINESS_MODEL_EXPECTED_DIR);
        $this->assertGeneratedFile('src/Model/Subscription.php', self::BUSINESS_MODEL_EXPECTED_DIR);
    }
}
