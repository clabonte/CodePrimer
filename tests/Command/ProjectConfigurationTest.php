<?php

namespace CodePrimer\Tests\Command;

use CodePrimer\Command\ProjectConfiguration;
use CodePrimer\Model\BusinessBundle;
use CodePrimer\Template\Artifact;
use CodePrimer\Tests\Functional\TemplateTestCase;

class ProjectConfigurationTest extends TemplateTestCase
{
    /** @var ProjectConfiguration */
    private $projectConfiguration;

    public function setUp(): void
    {
        parent::setUp();
        $this->projectConfiguration = new ProjectConfiguration();
    }

    public function testAddArtifact()
    {
        // Make sure the configuration is empty
        self::assertEmpty($this->projectConfiguration->getAllArtifacts());
        self::assertEmpty($this->projectConfiguration->getArtifacts(Artifact::CODE));
        self::assertFalse($this->projectConfiguration->isRelationalDatabaseConfigured());

        $artifact = new Artifact(Artifact::CODE, 'model', 'php');
        $this->projectConfiguration->addArtifact($artifact);

        self::assertCount(1, $this->projectConfiguration->getAllArtifacts());
        self::assertCount(1, $this->projectConfiguration->getArtifacts(Artifact::CODE));
        self::assertFalse($this->projectConfiguration->isRelationalDatabaseConfigured());

        $artifact = new Artifact(Artifact::CODE, 'migration', 'mysql', 'CreateDatabase');
        $this->projectConfiguration->addArtifact($artifact);
        self::assertCount(2, $this->projectConfiguration->getAllArtifacts());
        self::assertCount(2, $this->projectConfiguration->getArtifacts(Artifact::CODE));
        self::assertTrue($this->projectConfiguration->isRelationalDatabaseConfigured());
    }

    public function testDefaultPhpProjectConfiguration()
    {
        $businessBundle = new BusinessBundle('Test Namespace', 'Test Name');
        $businessBundle->setDescription('Test Description');

        $this->projectConfiguration
            ->setPath('codeprimer/bundle.php')
            ->setBusinessBundle($businessBundle)
            ->addArtifact(new Artifact(Artifact::CODE, 'dataset', 'php'))
            ->addArtifact(new Artifact(Artifact::CODE, 'model', 'php'))
            ->addArtifact(new Artifact(Artifact::CODE, 'event', 'php'))
            ->addArtifact(new Artifact(Artifact::CODE, 'Migration', 'mysql', 'CreateDatabase'))
            ->addArtifact(new Artifact(Artifact::CODE, 'Migration', 'mysql', 'RevertDatabase'))
            ->addArtifact(new Artifact(Artifact::DOCUMENTATION, 'dataset', 'markdown'))
            ->addArtifact(new Artifact(Artifact::DOCUMENTATION, 'model', 'markdown'))
            ->addArtifact(new Artifact(Artifact::DOCUMENTATION, 'process', 'markdown', 'index'))
            ->addArtifact(new Artifact(Artifact::DOCUMENTATION, 'process', 'markdown', 'details'));

        self::assertEquals('codeprimer/bundle.php', $this->projectConfiguration->getPath());

        $bundle = $this->projectConfiguration->getBusinessBundle();
        self::assertEquals($businessBundle->getName(), $bundle->getName());
        self::assertEquals($businessBundle->getNamespace(), $bundle->getNamespace());
        self::assertEquals($businessBundle->getDescription(), $bundle->getDescription());

        self::assertCount(9, $this->projectConfiguration->getAllArtifacts());
        self::assertCount(5, $this->projectConfiguration->getArtifacts(Artifact::CODE));
        self::assertCount(4, $this->projectConfiguration->getArtifacts(Artifact::DOCUMENTATION));

        $filename = self::ACTUAL_DIR.'testconfig.yaml';
        $this->projectConfiguration->save($filename);
        self::assertFileExists($filename);
        self::assertIsReadable($filename);
        $projectConfiguration = new ProjectConfiguration();
        $projectConfiguration->load($filename);

        self::assertEquals('codeprimer/bundle.php', $projectConfiguration->getPath());

        $bundle = $projectConfiguration->getBusinessBundle();
        self::assertEquals($businessBundle->getName(), $bundle->getName());
        self::assertEquals($businessBundle->getNamespace(), $bundle->getNamespace());
        self::assertEquals($businessBundle->getDescription(), $bundle->getDescription());

        self::assertCount(9, $projectConfiguration->getAllArtifacts());
        self::assertCount(5, $projectConfiguration->getArtifacts(Artifact::CODE));
        self::assertCount(4, $projectConfiguration->getArtifacts(Artifact::DOCUMENTATION));
    }
}
