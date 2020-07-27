<?php

namespace CodePrimer\Tests\Builder;

use CodePrimer\Builder\ArtifactBuilderFactory;
use CodePrimer\Template\Artifact;
use PHPUnit\Framework\TestCase;

class ArtifactBuilderFactoryTest extends TestCase
{
    /** @var ArtifactBuilderFactory */
    private $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->factory = new ArtifactBuilderFactory();
    }

    public function testCreateCodeBuilder()
    {
        self::assertNotNull($this->factory->createBuilder(new Artifact(Artifact::CODE, 'entity', 'php')));
        self::assertNotNull($this->factory->createBuilder(new Artifact(Artifact::CODE, 'entity', 'php', 'doctrineOrm')));
        self::assertNotNull($this->factory->createBuilder(new Artifact(Artifact::CODE, 'entity', 'java')));
        self::assertNotNull($this->factory->createBuilder(new Artifact(Artifact::CODE, 'entity', 'unknown')));
    }

    public function testCreateProjectBuilder()
    {
        self::assertNotNull($this->factory->createBuilder(new Artifact(Artifact::PROJECT, 'symfony', 'sh')));
        self::assertNotNull($this->factory->createBuilder(new Artifact(Artifact::PROJECT, 'symfony', 'sh', 'setup')));
        self::assertNotNull($this->factory->createBuilder(new Artifact(Artifact::PROJECT, 'symfony', 'bin')));
    }

    public function testCreateBuilderForUnknownTypeShouldThrowException()
    {
        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage("No builder available for type unknown");

        $this->factory->createBuilder(new Artifact(Artifact::CODE, 'unknown', 'php'));
    }
}
