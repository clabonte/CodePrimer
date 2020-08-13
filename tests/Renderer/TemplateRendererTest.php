<?php

namespace CodePrimer\Tests\Renderer;

use CodePrimer\Helper\ArtifactHelper;
use CodePrimer\Renderer\TemplateRenderer;
use CodePrimer\Template\Artifact;
use CodePrimer\Template\Template;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Loader\FilesystemLoader;

class TemplateRendererTest extends TestCase
{
    const ROOT = __DIR__.'/../../';

    /** @var FilesystemLoader */
    private $loader;

    /** @var Environment */
    private $twig;

    /** @var TemplateRenderer */
    private $helper;

    public function setUp(): void
    {
        parent::setUp();
        $this->loader = new FilesystemLoader('fixtures/templates', self::ROOT);
        $this->twig = new Environment($this->loader);
        $this->helper = new TemplateRenderer($this->loader, self::ROOT.'output/actual');
    }

    /**
     * @throws \Exception
     */
    public function testLoadBaseTemplateShouldPass()
    {
        $templateWrapper = $this->helper->loadTemplate($this->twig, 'txt/BaseTemplate.txt.twig');
        self::assertNotNull($templateWrapper);

        self::assertEquals('txt/BaseTemplate.txt.twig', $templateWrapper->getTemplateName());
    }

    /**
     * @throws \Exception
     */
    public function testLoadExtendedTemplateShouldPass()
    {
        $template = $this->helper->loadTemplate($this->twig, 'txt/ExtendedTemplate.txt.twig');
        self::assertNotNull($template);

        self::assertEquals('ext/txt/ExtendedTemplate.txt.twig', $template->getTemplateName());
    }

    /**
     * @throws \Exception
     */
    public function testLoadUnknownTemplateShouldThrowError()
    {
        self::expectException(LoaderError::class);
        $this->helper->loadTemplate($this->twig, 'UnknownTemplate.txt.twig');
    }

    /**
     * @throws \Exception
     */
    public function testRenderBaseTemplateShouldPass()
    {
        $template = new Template('BaseTemplate', new Artifact('', '', 'txt'));

        $expected = file_get_contents(__DIR__.'/../../fixtures/templates/txt/BaseTemplate.txt.twig');

        $content = $this->helper->renderTemplate($template);

        self::assertEquals($expected, $content);
    }

    /**
     * @throws \Exception
     */
    public function testRenderExtendedTemplateShouldPass()
    {
        $template = new Template('ExtendedTemplate', new Artifact('', '', 'txt'));

        $expected = file_get_contents(__DIR__.'/../../fixtures/templates/ext/txt/ExtendedTemplate.txt.twig');

        $content = $this->helper->renderTemplate($template);

        self::assertEquals($expected, $content);
    }

    /**
     * @throws \Exception
     */
    public function testRenderArtifactTemplateShouldPass()
    {
        $template = new Template('ArtifactTemplate', new Artifact('category', 'type', 'txt'));

        $expected = file_get_contents(__DIR__.'/../../fixtures/templates/category/txt/type/ArtifactTemplate.txt.twig');

        $content = $this->helper->renderTemplate($template);

        self::assertEquals($expected, $content);
    }

    public function testConstructorWithCustomerHelperShouldPass()
    {
        $stub = $this->getMockBuilder(ArtifactHelper::class)->getMock();

        $renderer = new TemplateRenderer($this->loader, self::ROOT.'output/actual', $stub);
        self::assertEquals($stub, $renderer->getHelper());
    }
}
