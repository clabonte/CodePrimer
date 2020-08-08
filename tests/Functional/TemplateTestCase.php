<?php

namespace CodePrimer\Tests\Functional;

use CodePrimer\Builder\ArtifactBuilderFactory;
use CodePrimer\Helper\BusinessBundleHelper;
use CodePrimer\Model\BusinessBundle;
use CodePrimer\Renderer\TemplateRenderer;
use CodePrimer\Template\TemplateRegistry;
use CodePrimer\Tests\Helper\TestHelper;
use PHPUnit\Framework\TestCase;
use Twig\Loader\FilesystemLoader;

class TemplateTestCase extends TestCase
{
    const ROOT = __DIR__.'/../../';
    const ACTUAL_DIR = self::ROOT.'tests/output/actual/';
    const EXPECTED_DIR = self::ROOT.'tests/output/expected/';

    /** @var TemplateRegistry */
    protected $templateRegistry;

    /** @var ArtifactBuilderFactory */
    protected $factory;

    /** @var TemplateRenderer */
    protected $renderer;

    /** @var BusinessBundle */
    protected $businessBundle;

    public function setUp(): void
    {
        parent::setUp();

        $this->templateRegistry = new TemplateRegistry();
        $this->factory = new ArtifactBuilderFactory();
        $this->businessBundle = new BusinessBundle('CodePrimer Tests', 'FunctionalTest');
        $loader = new FilesystemLoader('templates', self::ROOT);
        $this->renderer = new TemplateRenderer($loader, self::ACTUAL_DIR);

        // Cleanup the 'actual' folder
        $this->cleanupDirectory(self::ACTUAL_DIR);
    }

    protected function initEntities()
    {
        TestHelper::addSampleBusinessModels($this->businessBundle);

        $businessBundleHelper = new BusinessBundleHelper();
        $businessBundleHelper->buildRelationships($this->businessBundle);

        TestHelper::addSampleBusinessProcesses($this->businessBundle);
    }

    protected function assertGeneratedFile(string $filename, string $expectedDir)
    {
        // Make sure the file has been generated
        self::assertFileExists(self::ACTUAL_DIR.$filename, 'File not generated: '.$filename);

        // Make sure the generated content matches the expected one
        $expected = file_get_contents($expectedDir.$filename);
        $actual = file_get_contents(self::ACTUAL_DIR.$filename);

        self::assertEquals($expected, $actual, 'The generated file do not match the expected one: '.$filename);
    }

    protected function cleanupDirectory(string $directory)
    {
        if (file_exists($directory) && is_dir($directory)) {
            foreach (scandir($directory) as $file) {
                if (('.' == $file) || ('..' == $file)) {
                    continue;
                }
                if (is_dir($directory.'/'.$file)) {
                    $this->cleanupDirectory($directory.'/'.$file);
                } else {
                    unlink($directory.'/'.$file);
                }
            }

            if (self::ACTUAL_DIR != $directory) {
                rmdir($directory);
            }
        }
    }
}
