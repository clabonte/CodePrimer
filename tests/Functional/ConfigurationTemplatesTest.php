<?php

namespace CodePrimer\Tests\Functional;

use CodePrimer\Model\BusinessBundle;
use CodePrimer\Template\Artifact;

/**
 * Class ConfigurationTemplatesTest.
 *
 * @group functional
 */
class ConfigurationTemplatesTest extends TemplateTestCase
{
    const PHP_EXPECTED_DIR = self::EXPECTED_DIR.'configuration/php/';

    /**
     * @dataProvider configurationTemplateProvider
     *
     * @throws \Exception
     */
    public function testPhpConfigurationTemplate(Artifact $artifact, string $expectedFile)
    {
        $this->businessBundle = new BusinessBundle('CodePrimer Tests', 'FunctionalTest');
        $this->businessBundle->setDescription('This is a sample BusinessBundle used to test CodePrimer functionality');

        // Extract the template to use for this artifact
        $template = $this->templateRegistry->getTemplateForArtifact($artifact);
        self::assertNotNull($template);

        // Extract the builder to use for this artifact
        $builder = $this->factory->createBuilder($artifact);
        self::assertNotNull($builder);

        // Build the artifacts
        $builder->build($this->businessBundle, $template, $this->renderer);

        // Make sure the right files have been generated
        $this->assertGeneratedFile($expectedFile, self::PHP_EXPECTED_DIR.$artifact->getType().'/');
    }

    public function configurationTemplateProvider()
    {
        return [
            'composer.json' => [
                new Artifact(Artifact::CONFIGURATION, 'dependency manager', 'PHP', 'composer'),
                'composer.json',
            ],
            '.php_cs' => [
                new Artifact(Artifact::CONFIGURATION, 'coding standards', 'PHP', 'PHP CS Fixer'),
                '.php_cs.dist',
            ],
            'phpunit.xml.dist' => [
                new Artifact(Artifact::CONFIGURATION, 'tests', 'PHP', 'PHPUnit'),
                'phpunit.xml.dist',
            ],
            '.gitignore' => [
                new Artifact(Artifact::CONFIGURATION, 'git', 'php', 'gitignore'),
                '.gitignore',
            ],
            'validate-master.yml' => [
                new Artifact(Artifact::CONFIGURATION, 'github', 'php', 'validate-master'),
                '.github/workflows/validate-master.yml',
            ],
            'validate-pr.yml' => [
                new Artifact(Artifact::CONFIGURATION, 'github', 'php', 'validate-pr'),
                '.github/workflows/validate-pr.yml',
            ],
            'codeprimer/bundle.php' => [
                new Artifact(Artifact::CONFIGURATION, 'codeprimer', 'php', 'bundle'),
                'codeprimer/bundle.php',
            ],
            'codeprimer/BusinessModelFactory.php' => [
                new Artifact(Artifact::CONFIGURATION, 'codeprimer', 'php', 'BusinessModelFactory'),
                'codeprimer/BusinessModelFactory.php',
            ],
            'codeprimer/BusinessProcessFactory.php' => [
                new Artifact(Artifact::CONFIGURATION, 'codeprimer', 'php', 'BusinessProcessFactory'),
                'codeprimer/BusinessProcessFactory.php',
            ],
            'codeprimer/DatasetFactory.php' => [
                new Artifact(Artifact::CONFIGURATION, 'codeprimer', 'php', 'DatasetFactory'),
                'codeprimer/DatasetFactory.php',
            ],
        ];
    }
}
