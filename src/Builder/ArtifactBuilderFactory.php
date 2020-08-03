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

        $this->builders['entity'] = BusinessModelBuilder::class;
        $this->builders['repository'] = RepositoryBuilder::class;
        $this->builders['migration'] = MigrationBuilder::class;
        $this->builders['revertmigration'] = MigrationBuilder::class;
        $this->builders[Artifact::PROJECT] = [
            'symfony' => ProjectScriptBuilder::class,
        ];
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

        if (!empty($this->builders[$type])) {
            $builder = $this->builders[$type];

            return new $builder();
        }

        throw new RuntimeException("No builder available for type $type");
    }
}
