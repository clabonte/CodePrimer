<?php

namespace CodePrimer\Builder;

use CodePrimer\Template\Artifact;
use RuntimeException;

class ArtifactBuilderFactory
{
    /** @var array */
    private $builders;

    /**
     * ArtifactBuilderFactory constructor.
     */
    public function __construct()
    {
        $this->builders = [];

        $this->builders[Artifact::CODE] = [
            'databundle' => DataBundleBuilder::class,
            'engine' => EngineBuilder::class,
            'entity' => EntityBuilder::class,
            'event' => EventBuilder::class,
            'model' => BusinessModelBuilder::class,
            'dataset' => DatasetBuilder::class,
            'repository' => RepositoryBuilder::class,
            'migration' => MigrationBuilder::class,
            'revertmigration' => MigrationBuilder::class,
        ];

        $this->builders[Artifact::PROJECT] = ProjectScriptBuilder::class;
        $this->builders[Artifact::DOCUMENTATION] = BundleDocumentationBuilder::class;
        $this->builders[Artifact::CONFIGURATION] = ConfigurationFileBuilder::class;
    }

    /**
     * @return ArtifactBuilder
     *
     * @throws RuntimeException If there is no builder defined for this artifact
     */
    public function createBuilder(Artifact $artifact)
    {
        $category = $artifact->getCategory();
        $type = strtolower($artifact->getType());

        if (!empty($this->builders[$category][$type])) {
            $builder = $this->builders[$category][$type];

            return new $builder();
        }

        if (!empty($this->builders[$category])) {
            $builder = $this->builders[$category];

            if (!is_array($builder)) {
                return new $builder();
            }
        }

        throw new RuntimeException("No builder available for category '$category', type '$type'");
    }
}
