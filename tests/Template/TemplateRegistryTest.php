<?php

namespace CodePrimer\Tests\Template;

use CodePrimer\Template\Artifact;
use CodePrimer\Template\Template;
use CodePrimer\Template\TemplateRegistry;
use PHPUnit\Framework\TestCase;

class TemplateRegistryTest extends TestCase
{
    /** @var TemplateRegistry */
    private $registry;

    public function setUp(): void
    {
        parent::setUp();
        $this->registry = new TemplateRegistry();
    }

    /**
     * @throws \Exception
     */
    public function testAddTemplate()
    {
        $artifact = new Artifact('unit', 'test', 'test', 'test');

        $template = new Template('Test', $artifact);
        $this->registry->addTemplate($template, 'Test description');

        $newTemplate = $this->registry->getTemplate('unit', 'test', 'test', 'test');
        self::assertNotNull($newTemplate);

        self::assertEquals($template, $newTemplate);
    }

    public function testListTemplatesByCategoryShouldPass()
    {
        $templates = $this->registry->listTemplates(Artifact::CODE);
        self::assertCount(9, $templates);

        $templates = $this->registry->listTemplates(Artifact::CONFIGURATION);
        self::assertEmpty($templates);

        $templates = $this->registry->listTemplates(Artifact::DOCUMENTATION);
        self::assertCount(1, $templates);

        $templates = $this->registry->listTemplates(Artifact::PROJECT);
        self::assertCount(1, $templates);

        $templates = $this->registry->listTemplates(Artifact::TESTS);
        self::assertEmpty($templates);
    }

    public function testListTemplatesByCategoryAndTypeShouldPass()
    {
        $templates = $this->registry->listTemplates(Artifact::CODE, 'model');
        self::assertCount(1, $templates);

        $templates = $this->registry->listTemplates(Artifact::CODE, 'entity');
        self::assertCount(3, $templates);

        $templates = $this->registry->listTemplates(Artifact::CODE, 'Entity');
        self::assertCount(3, $templates);

        $templates = $this->registry->listTemplates(Artifact::CODE, 'Repository');
        self::assertCount(1, $templates);

        $templates = $this->registry->listTemplates(Artifact::CODE, 'Migration');
        self::assertCount(4, $templates);

        $templates = $this->registry->listTemplates(Artifact::PROJECT, 'Symfony');
        self::assertCount(1, $templates);
    }

    public function testListTemplatesByCategoryTypeAndFormatShouldPass()
    {
        $templates = $this->registry->listTemplates(Artifact::CODE, 'model', 'php');
        self::assertCount(1, $templates);

        $templates = $this->registry->listTemplates(Artifact::CODE, 'entity', 'php');
        self::assertCount(2, $templates);

        $templates = $this->registry->listTemplates(Artifact::CODE, 'Entity', 'PHP');
        self::assertCount(2, $templates);

        $templates = $this->registry->listTemplates(Artifact::CODE, 'Entity', 'Java');
        self::assertCount(1, $templates);

        $templates = $this->registry->listTemplates(Artifact::CODE, 'Migration', 'MySQL');
        self::assertCount(3, $templates);

        $templates = $this->registry->listTemplates(Artifact::PROJECT, 'Symfony', 'sh');
        self::assertCount(1, $templates);
    }

    /**
     * @dataProvider templateProvider
     *
     * @throws \Exception
     */
    public function testGetValidTemplateShouldPass(Artifact $artifact)
    {
        $template = $this->registry->getTemplate($artifact->getCategory(), $artifact->getType(), $artifact->getFormat(), $artifact->getVariant());
        self::assertNotNull($template);

        self::assertEquals($artifact, $template->getArtifact());
    }

    /**
     * @dataProvider templateProvider
     *
     * @throws \Exception
     */
    public function testGetValidTemplateForArtifactShouldPass(Artifact $artifact)
    {
        $template = $this->registry->getTemplateForArtifact($artifact);
        self::assertNotNull($template);

        self::assertEquals($artifact, $template->getArtifact());
    }

    public function templateProvider()
    {
        return [
            'BusinessModel' => [new Artifact(Artifact::CODE, 'model', 'php')],
            'DoctrineOrmEntity' => [new Artifact(Artifact::CODE, 'entity', 'php', 'doctrineOrm')],
            'PlainEntity' => [new Artifact(Artifact::CODE, 'entity', 'php')],
            'Symfony' => [new Artifact(Artifact::PROJECT, 'symfony', 'sh', 'setup')],
            'MySQL Migration' => [new Artifact(Artifact::CODE, 'migration', 'mysql', 'createDatabase')],
        ];
    }

    /**
     * @throws \Exception
     */
    public function testGetInvalidTemplateShouldThrowException()
    {
        self::expectException(\Exception::class);
        $this->registry->getTemplate('Unknown', 'Unknown', 'Unknown');
    }

    /**
     * @throws \Exception
     */
    public function testGetInvalidTemplateForArtifactShouldThrowException()
    {
        $artifact = new Artifact('Unknown', 'Unknown', 'Unknown');
        self::expectException(\Exception::class);
        $this->registry->getTemplateForArtifact($artifact);
    }
}
