<?php

namespace CodePrimer\Tests\Functional;

use CodePrimer\Template\Artifact;
use CodePrimer\Twig\LanguageTwigExtension;

/**
 * Class PhpDataSetTemplatesTest.
 *
 * @group functional
 */
class PhpDatasetTemplatesTest extends TemplateTestCase
{
    const DATASET_EXPECTED_DIR = self::EXPECTED_DIR.'/code/php/dataset/DataSet/';

    /**
     * @throws \Exception
     */
    public function testDataSetTemplate()
    {
        $this->initEntities();
        $languageExtension = new LanguageTwigExtension();

        $dataSets = $this->businessBundle->getDatasets();
        self::assertCount(2, $dataSets);

        $artifact = new Artifact(Artifact::CODE, 'dataset', 'php');

        // Extract the template to use for this artifact
        $template = $this->templateRegistry->getTemplateForArtifact($artifact);
        self::assertNotNull($template);

        // Extract the builder to use for this artifact
        $builder = $this->factory->createBuilder($artifact);
        self::assertNotNull($builder);

        // Build the artifacts
        $builder->build($this->businessBundle, $template, $this->renderer);

        // Make sure the right files have been generated
        foreach ($dataSets as $dataSet) {
            $class = $languageExtension->classFilter($dataSet);
            $this->assertGeneratedFile("src/Dataset/$class.php", self::DATASET_EXPECTED_DIR);
        }
    }
}
