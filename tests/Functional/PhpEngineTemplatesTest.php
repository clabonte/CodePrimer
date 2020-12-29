<?php

namespace CodePrimer\Tests\Functional;

use CodePrimer\Template\Artifact;
use CodePrimer\Twig\LanguageTwigExtension;

class PhpEngineTemplatesTest extends TemplateTestCase
{
    const ENGINE_EXPECTED_DIR = self::EXPECTED_DIR.'/code/php/engine/Engine/';

    /**
     * @throws \Exception
     */
    public function testEngineInterfaceTemplate()
    {
        $this->initEntities();
        $languageExtension = new LanguageTwigExtension();

        $categories = $this->businessBundle->getBusinessProcessCategories();
        self::assertNotEmpty($categories);

        $artifact = new Artifact(Artifact::CODE, 'engine', 'php', 'interface');

        // Extract the template to use for this artifact
        $template = $this->templateRegistry->getTemplateForArtifact($artifact);
        self::assertNotNull($template);

        // Extract the builder to use for this artifact
        $builder = $this->factory->createBuilder($artifact);
        self::assertNotNull($builder);

        // Build the artifacts
        $builder->build($this->businessBundle, $template, $this->renderer);

        // Make sure the right files have been generated
        foreach ($categories as $category) {
            $languageExtension = new LanguageTwigExtension();

            if (empty($category)) {
                $model = 'Default';
            } else {
                $model = $languageExtension->singularFilter($category);
            }
            $model .= ' Engine Interface';
            $class = $languageExtension->classFilter($model);
            $this->assertGeneratedFile("gen-src/Engine/$class.php", self::ENGINE_EXPECTED_DIR);
        }
    }
}
