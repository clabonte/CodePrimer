<?php

namespace CodePrimer\Command;

use CodePrimer\Builder\ArtifactBuilderFactory;
use CodePrimer\Model\BusinessBundle;
use CodePrimer\Renderer\TemplateRenderer;
use CodePrimer\Template\Artifact;
use CodePrimer\Template\TemplateRegistry;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Output\OutputInterface;
use Twig\Loader\FilesystemLoader;

abstract class CodePrimerCommand extends Command
{
    /** @var string Path where CodePrimer templates are expected to be located */
    const BASE_PATH = __DIR__.'/../../';

    /** @var string Default path used for generating artifacts */
    const ARTIFACTS_DEFAULT_PATH = '.';

    /** @var TemplateRegistry */
    protected $templateRegistry;

    /** @var ArtifactBuilderFactory */
    protected $builderFactory;

    /** @var TemplateRenderer */
    protected $templateRenderer;

    public function __construct()
    {
        parent::__construct();
        $this->templateRegistry = new TemplateRegistry();
        $this->builderFactory = new ArtifactBuilderFactory();
        $loader = new FilesystemLoader('templates', self::BASE_PATH);
        $this->templateRenderer = new TemplateRenderer($loader, self::ARTIFACTS_DEFAULT_PATH);
    }

    protected function prepareStyles(OutputInterface $output)
    {
        $fileStyle = new OutputFormatterStyle('green', null, ['bold']);
        $output->getFormatter()->setStyle('file', $fileStyle);
    }

    /**
     * @param Artifact $artifact  Artifact to generate
     * @param bool     $overwrite Whether we should overwrite the file if it exists
     *
     * @throws Exception
     */
    protected function primeArtifact(Artifact $artifact, bool $overwrite = true)
    {
        // Extract the template to use for this artifact
        $template = $this->templateRegistry->getTemplateForArtifact($artifact);

        // Extract the builder to use for this artifact
        $builder = $this->builderFactory->createBuilder($artifact);

        // Build the artifacts
        $this->templateRenderer->setOverwriteFiles($overwrite);
        $builder->build($this->getBusinessBundle(), $template, $this->templateRenderer);
    }

    public function errorHandler($errno, $errstr, $errfile, $errline) {
        throw new \ErrorException($errstr, $errno, 0, $errfile, $errline);
    }

    abstract protected function getBusinessBundle(): BusinessBundle;
}
