<?php

use CodePrimer\Builder\ArtifactBuilderFactory;
use CodePrimer\Model\Package;
use CodePrimer\Renderer\TemplateRenderer;
use CodePrimer\Template\Artifact;
use CodePrimer\Template\TemplateRegistry;
use Twig\Loader\FilesystemLoader;

require "../vendor/autoload.php";

class ChannelApp
{
    const BASE_PATH = __DIR__.'/../';
    const PROJECT_OUTPUT_PATH = __DIR__.'/output/';
    const CODE_OUTPUT_PATH = __DIR__.'/output/Channel/';

    /** @var TemplateRegistry */
    private $templateRegistry;

    /** @var ArtifactBuilderFactory */
    private $builderFactory;

    /** @var TemplateRenderer */
    private $templateRenderer;

    /** @var Package */
    private $bundle;

    /**
     * ChannelApp constructor.
     */
    public function __construct()
    {
        $this->initCodePrimer();
        $this->initBusinessBundle();
    }

    public function primePhpProject()
    {
        // 1. Prime the basic structure of a Symfony project
        $this->templateRenderer->setBaseFolder(self::PROJECT_OUTPUT_PATH);
        $artifact = new Artifact(Artifact::PROJECT, 'Symfony', 'sh', 'setup');
        $this->primeArtifact($artifact);

        // 2. Prime 'Business Model' sourc
        $this->templateRenderer->setBaseFolder(self::CODE_OUTPUT_PATH);
    }

    /**
     * @param Artifact $artifact Artifact to generate
     * @throws Exception
     */
    private function primeArtifact(Artifact $artifact)
    {
        // Extract the template to use for this artifact
        $template = $this->templateRegistry->getTemplateForArtifact($artifact);

        // Extract the builder to use for this artifact
        $builder = $this->builderFactory->createBuilder($artifact);

        // Build the artifacts
        $builder->build($this->bundle, $template, $this->templateRenderer);
    }

    /**
     * Initializes the CodePrimer components used to generate artifacts
     */
    private function initCodePrimer()
    {
        $this->templateRegistry = new TemplateRegistry();
        $this->builderFactory = new ArtifactBuilderFactory();
        $loader = new FilesystemLoader('templates', self::BASE_PATH);
        $this->templateRenderer = new TemplateRenderer($loader, self::PROJECT_OUTPUT_PATH);
    }

    /**
     * This method prepares the 'Business Model' to use for generating Artifacts
     */
    private function initBusinessBundle()
    {
        // Step 1 - Create the Business Bundle that will contain your model
        $this->bundle = $this->createBusinessBundle();

        // Step 2 - Add your Business Data Model to your Bundle
        $this->initBusinessDataModel($this->bundle);

        // Step 3 - Add your Business Process Model to your Bundle
        $this->initBusinessProcessingModel($this->bundle);
    }

    /**
     * Creates an empty 'Business Bundle'
     * @return Package The Business Bundle to use for modeling your application
     */
    private function createBusinessBundle()
    {
        $bundle = new Package('io.codeprimer.sample', 'Channel');
        $bundle->setDescription('This sample application is used to show how model a business application using CodePrimer');

        return $bundle;
    }

    /**
     * Creates the application's Business Data Model for a given 'Business Bundle'
     * @param Package $bundle The 'Business Bundle' used to store the Business Data Model
     */
    private function initBusinessDataModel(Package $bundle)
    {

    }

    /**
     * Creates the application's Business Processing Model for a given 'Business Bundle'
     * @param Package $bundle The 'Business Bundle' used to store the Business Data Model
     */
    private function initBusinessProcessingModel(Package $bundle)
    {

    }
}

$app = new ChannelApp();
$app->primePhpProject();