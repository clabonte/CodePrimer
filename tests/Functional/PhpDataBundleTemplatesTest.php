<?php

namespace CodePrimer\Tests\Functional;

use CodePrimer\Template\Artifact;
use CodePrimer\Twig\LanguageTwigExtension;

/**
 * Class PhpDataBundleTemplatesTest.
 *
 * @group functional
 */
class PhpDataBundleTemplatesTest extends TemplateTestCase
{
    const DATA_EXPECTED_DIR = self::EXPECTED_DIR.'/code/php/databundle/Data/';

    /**
     * @throws \Exception
     */
    public function testDataBundleTemplate()
    {
        $this->initEntities();
        $languageExtension = new LanguageTwigExtension();

        $dataBundles = [];
        foreach ($this->businessBundle->getBusinessProcesses() as $businessProcess) {
            if ($businessProcess->isDataReturned()) {
                $dataBundles[] = $businessProcess->getReturnedData();
            }
        }
        self::assertCount(1, $dataBundles);

        $artifact = new Artifact(Artifact::CODE, 'databundle', 'php');

        // Extract the template to use for this artifact
        $template = $this->templateRegistry->getTemplateForArtifact($artifact);
        self::assertNotNull($template);

        // Extract the builder to use for this artifact
        $builder = $this->factory->createBuilder($artifact);
        self::assertNotNull($builder);

        // Build the artifacts
        $builder->build($this->businessBundle, $template, $this->renderer);

        // Make sure the right files have been generated
        foreach ($dataBundles as $dataBundle) {
            $class = $languageExtension->classFilter($event);
            $this->assertGeneratedFile("gen-src/Event/$class.php", self::DATA_EXPECTED_DIR);
        }
    }
}
