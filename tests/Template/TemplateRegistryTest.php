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
        self::assertCount(11, $templates);

        $templates = $this->registry->listTemplates(Artifact::CONFIGURATION);
        self::assertEmpty($templates);

        $templates = $this->registry->listTemplates(Artifact::DOCUMENTATION);
        self::assertCount(4, $templates);

        $templates = $this->registry->listTemplates(Artifact::PROJECT);
        self::assertCount(1, $templates);

        $templates = $this->registry->listTemplates(Artifact::TESTS);
        self::assertEmpty($templates);
    }

    /**
     * @dataProvider categoryAndTypeProvider
     */
    public function testListTemplatesByCategoryAndTypeShouldPass($category, $type, $expectedCount)
    {
        $templates = $this->registry->listTemplates($category, $type);
        self::assertCount($expectedCount, $templates);
    }

    public function categoryAndTypeProvider()
    {
        return [
            'Code model' => [Artifact::CODE, 'model', 1],
            'Code entity' => [Artifact::CODE, 'entity', 3],
            'Code Entity' => [Artifact::CODE, 'Entity', 3],
            'Code Repository' => [Artifact::CODE, 'Repository', 1],
            'Code Migration' => [Artifact::CODE, 'Migration', 4],
            'Code Event' => [Artifact::CODE, 'event', 1],
            'Code DataSet' => [Artifact::CODE, 'DataSet', 1],
            'Project Symfony' => [Artifact::PROJECT, 'Symfony', 1],
            'Documentation Model' => [Artifact::DOCUMENTATION, 'Model', 1],
            'Documentation Process' => [Artifact::DOCUMENTATION, 'Process', 2],
            'Documentation Dataset' => [Artifact::DOCUMENTATION, 'Dataset', 1],
        ];
    }

    /**
     * @dataProvider categoryTypeAndFormatProvider
     */
    public function testListTemplatesByCategoryTypeAndFormatShouldPass($category, $type, $format, $expectedCount)
    {
        $templates = $this->registry->listTemplates($category, $type, $format);
        self::assertCount($expectedCount, $templates);
    }

    public function categoryTypeAndFormatProvider()
    {
        return [
            'Code model - PHP' => [Artifact::CODE, 'model', 'php', 1],
            'Code entity - PHP' => [Artifact::CODE, 'entity', 'php', 2],
            'Code Entity - PHP' => [Artifact::CODE, 'Entity', 'PHP', 2],
            'Code event - PHP' => [Artifact::CODE, 'event', 'PHP', 1],
            'Code DataSet - PHP' => [Artifact::CODE, 'dataset', 'PHP', 1],
            'Code Entity - Java' => [Artifact::CODE, 'Entity', 'Java', 1],
            'Code Migration - MySQL' => [Artifact::CODE, 'Migration', 'MySQL', 3],
            'Project Symfony - sh' => [Artifact::PROJECT, 'Symfony', 'sh', 1],
            'Documentation Model - markdown' => [Artifact::DOCUMENTATION, 'Model', 'markdown', 1],
            'Documentation Process - markdown' => [Artifact::DOCUMENTATION, 'Process', 'markdown', 2],
            'Documentation Dataset - markdown' => [Artifact::DOCUMENTATION, 'Dataset', 'markdown', 1],
        ];
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
            'Event' => [new Artifact(Artifact::CODE, 'event', 'php')],
            'DataSet' => [new Artifact(Artifact::CODE, 'dataset', 'php')],
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
