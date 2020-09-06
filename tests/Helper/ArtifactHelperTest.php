<?php

namespace CodePrimer\Tests\Helper;

use CodePrimer\Helper\ArtifactHelper;
use CodePrimer\Model\BusinessBundle;
use CodePrimer\Template\Artifact;
use PHPUnit\Framework\TestCase;

class ArtifactHelperTest extends TestCase
{
    /** @var ArtifactHelper */
    private $helper;

    public function setUp(): void
    {
        parent::setUp();
        $this->helper = new ArtifactHelper();
    }

    /**
     * @dataProvider getDirectoryProvider
     */
    public function testGetDirectory(BusinessBundle $businessBundle, Artifact $artifact, string $expected)
    {
        self::assertEquals($expected, $this->helper->getDirectory($businessBundle, $artifact));
    }

    public function getDirectoryProvider()
    {
        return [
            'PHP BusinessModel' => [
                TestHelper::getSampleBusinessBundle(),
                new Artifact(Artifact::CODE, 'model', 'php'),
                'gen-src/Model',
            ],
            'Plain PHP Entity' => [
                TestHelper::getSampleBusinessBundle(),
                new Artifact(Artifact::CODE, 'entity', 'php'),
                'gen-src/Entity',
            ],
            'Doctrine ORM PHP Entity' => [
                TestHelper::getSampleBusinessBundle(),
                new Artifact(Artifact::CODE, 'entity', 'php', 'doctrineOrm'),
                'gen-src/Entity',
            ],
            'Java BusinessModel' => [
                TestHelper::getSampleBusinessBundle(),
                new Artifact(Artifact::CODE, 'model', 'java'),
                'gen-src/codeprimer/tests/model',
            ],
            'Plain Java Entity' => [
                TestHelper::getSampleBusinessBundle(),
                new Artifact(Artifact::CODE, 'entity', 'java'),
                'gen-src/codeprimer/tests/entity',
            ],
            'PHP Repository' => [
                TestHelper::getSampleBusinessBundle(),
                new Artifact(Artifact::CODE, 'repository', 'php'),
                'gen-src/Repository',
            ],
            'Java Repository' => [
                TestHelper::getSampleBusinessBundle(),
                new Artifact(Artifact::CODE, 'repository', 'java'),
                'gen-src/codeprimer/tests/repository',
            ],
            'PHP Unit Tests' => [
                TestHelper::getSampleBusinessBundle(),
                new Artifact(Artifact::TESTS, 'unit', 'php'),
                'tests',
            ],
            'OpenAPI Documentation' => [
                TestHelper::getSampleBusinessBundle(),
                new Artifact(Artifact::DOCUMENTATION, 'api', 'openapi'),
                'docs',
            ],
            'Symfony Project' => [
                TestHelper::getSampleBusinessBundle(),
                new Artifact(Artifact::PROJECT, 'symfony', 'sh'),
                '.',
            ],
            'MySQL Migration' => [
                TestHelper::getSampleBusinessBundle(),
                new Artifact(Artifact::CODE, 'migration', 'mysql', 'CreateDatabase'),
                'migrations',
            ],
            'Markdown Model Documentation' => [
                TestHelper::getSampleBusinessBundle(),
                new Artifact(Artifact::DOCUMENTATION, 'model', 'markdown'),
                'docs/DataModel',
            ],
            'Markdown Process Documentation' => [
                TestHelper::getSampleBusinessBundle(),
                new Artifact(Artifact::DOCUMENTATION, 'process', 'markdown'),
                'docs/Process',
            ],
        ];
    }

    /**
     * @dataProvider getFilenameExtensionProvider
     */
    public function testGetFilenameExtension(Artifact $artifact, string $expected)
    {
        self::assertEquals($expected, $this->helper->getFilenameExtension($artifact));
    }

    public function getFilenameExtensionProvider()
    {
        return [
            'Plain PHP Entity' => [
                new Artifact(Artifact::CODE, 'entity', 'php'),
                '.php',
            ],
            'Doctrine ORM PHP Entity' => [
                new Artifact(Artifact::CODE, 'entity', 'php', 'doctrineOrm'),
                '.php',
            ],
            'Plain Java Entity' => [
                new Artifact(Artifact::CODE, 'entity', 'java'),
                '.java',
            ],
            'PHP Repository' => [
                new Artifact(Artifact::CODE, 'repository', 'php'),
                '.php',
            ],
            'Java Repository' => [
                new Artifact(Artifact::CODE, 'repository', 'java'),
                '.java',
            ],
            'Shell Script' => [
                new Artifact(Artifact::PROJECT, 'symfony', 'sh'),
                '.sh',
            ],
            'MySQL Migration' => [
                new Artifact(Artifact::CODE, 'migration', 'mysql'),
                '.sql',
            ],
            'Markdown Documentation' => [
                new Artifact(Artifact::DOCUMENTATION, 'model', 'markdown'),
                '.md',
            ],
            'CodePrimer Configuration' => [
                new Artifact(Artifact::CONFIGURATION, 'codeprimer', 'php', 'bundle'),
                '.php',
            ],
            'GitHub Configuration' => [
                new Artifact(Artifact::CONFIGURATION, 'github', 'php', 'validate-pr'),
                '.yml',
            ],
            'PHP CS Fixer Configuration' => [
                new Artifact(Artifact::CONFIGURATION, 'coding standards', 'php', 'PHP CS Fixer'),
                '.dist',
            ],
            'PHPUnit Configuration' => [
                new Artifact(Artifact::CONFIGURATION, 'tests', 'php', 'phpunit'),
                '.xml.dist',
            ],
            'Gitignore Configuration' => [
                new Artifact(Artifact::CONFIGURATION, 'git', 'php', 'gitignore'),
                '',
            ],
            'JSON' => [
                new Artifact(Artifact::CODE, 'any', 'json'),
                '.json',
            ],
            'Default' => [
                new Artifact(Artifact::DOCUMENTATION, 'any', 'any'),
                '.txt',
            ],
        ];
    }
}
