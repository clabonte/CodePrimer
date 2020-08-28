<?php

use CodePrimer\Adapter\RelationalDatabaseAdapter;
use CodePrimer\Builder\ArtifactBuilderFactory;
use CodePrimer\Helper\BusinessBundleHelper;
use CodePrimer\Model\BusinessBundle;
use CodePrimer\Renderer\TemplateRenderer;
use CodePrimer\Template\Artifact;
use CodePrimer\Template\TemplateRegistry;
use Twig\Loader\FilesystemLoader;

require '../vendor/autoload.php';
require 'DatasetFactory.php';
require 'BusinessModelFactory.php';
require 'BusinessProcessFactory.php';

class ChannelApp
{
    // PATH CONSTANTS
    const BASE_PATH = __DIR__.'/../';
    const SCRIPT_OUTPUT_PATH = __DIR__.'/output/';
    const PROJECT_OUTPUT_PATH = __DIR__.'/output/Channel/';

    // ROLE CONSTANTS
    const REGULAR_MEMBER = 'member';
    const PREMIUM_MEMBER = 'premium';
    const AUTHOR = 'author';
    const ADMIN = 'admin';

    /** @var TemplateRegistry */
    private $templateRegistry;

    /** @var ArtifactBuilderFactory */
    private $builderFactory;

    /** @var TemplateRenderer */
    private $templateRenderer;

    /** @var BusinessBundle */
    private $businessBundle;

    /**
     * ChannelApp constructor.
     */
    public function __construct()
    {
        $this->initCodePrimer();
        $this->initBusinessBundle();
    }

    /**
     * @throws Exception
     */
    public function primePhpArtifacts()
    {
        // 1. Prime the basic structure of a Symfony project
        $this->templateRenderer->setBaseFolder(self::SCRIPT_OUTPUT_PATH);
        $artifact = new Artifact(Artifact::PROJECT, 'Symfony', 'sh', 'setup');
        $this->primeArtifact($artifact);

        // 2. Prime 'Dataset' source code
        $this->templateRenderer->setBaseFolder(self::PROJECT_OUTPUT_PATH);
        $artifact = new Artifact(Artifact::CODE, 'dataset', 'php');
        $this->primeArtifact($artifact);

        // 3. Prime 'Business Model' source code
        $this->templateRenderer->setBaseFolder(self::PROJECT_OUTPUT_PATH);
        $artifact = new Artifact(Artifact::CODE, 'model', 'php');
        $this->primeArtifact($artifact);

        // 4. Prime 'Event' source code
        $this->templateRenderer->setBaseFolder(self::PROJECT_OUTPUT_PATH);
        $artifact = new Artifact(Artifact::CODE, 'event', 'php');
        $this->primeArtifact($artifact);
    }

    /**
     * @throws Exception
     */
    public function primeMySqlArtifacts()
    {
        // 1. Prime the MySQL 'Create DB' script to create the initial database
        $this->templateRenderer->setBaseFolder(self::PROJECT_OUTPUT_PATH);
        $artifact = new Artifact(Artifact::CODE, 'Migration', 'mysql', 'CreateDatabase');
        $this->primeArtifact($artifact);

        // 2. Prime the MySQL 'Revert DB' scripts to revert the initial database setup
        $this->templateRenderer->setBaseFolder(self::PROJECT_OUTPUT_PATH);
        $artifact = new Artifact(Artifact::CODE, 'Migration', 'mysql', 'RevertDatabase');
        $this->primeArtifact($artifact);
    }

    /**
     * @throws Exception
     */
    public function primeMarkdownArtifacts()
    {
        // 1. Prime 'Dataset' documentation in Markdown
        $this->templateRenderer->setBaseFolder(self::PROJECT_OUTPUT_PATH);
        $artifact = new Artifact(Artifact::DOCUMENTATION, 'dataset', 'markdown');
        $this->primeArtifact($artifact);

        // 2. Prime 'Data Model' documentation in Markdown
        $this->templateRenderer->setBaseFolder(self::PROJECT_OUTPUT_PATH);
        $artifact = new Artifact(Artifact::DOCUMENTATION, 'model', 'markdown');
        $this->primeArtifact($artifact);

        // 3. Prime 'Processing Model' overview documentation in Markdown
        $this->templateRenderer->setBaseFolder(self::PROJECT_OUTPUT_PATH);
        $artifact = new Artifact(Artifact::DOCUMENTATION, 'process', 'markdown', 'index');
        $this->primeArtifact($artifact);

        // 4. Prime 'Processing Model' detailed documentation in Markdown
        $this->templateRenderer->setBaseFolder(self::PROJECT_OUTPUT_PATH);
        $artifact = new Artifact(Artifact::DOCUMENTATION, 'process', 'markdown', 'details');
        $this->primeArtifact($artifact);
    }

    /**
     * @param Artifact $artifact Artifact to generate
     *
     * @throws Exception
     */
    private function primeArtifact(Artifact $artifact)
    {
        // Extract the template to use for this artifact
        $template = $this->templateRegistry->getTemplateForArtifact($artifact);

        // Extract the builder to use for this artifact
        $builder = $this->builderFactory->createBuilder($artifact);

        // Build the artifacts
        $builder->build($this->businessBundle, $template, $this->templateRenderer);
    }

    /**
     * Initializes the CodePrimer components used to generate artifacts.
     */
    private function initCodePrimer()
    {
        $this->templateRegistry = new TemplateRegistry();
        $this->builderFactory = new ArtifactBuilderFactory();
        $loader = new FilesystemLoader('templates', self::BASE_PATH);
        $this->templateRenderer = new TemplateRenderer($loader, self::SCRIPT_OUTPUT_PATH);
    }

    /**
     * This method prepares the 'Business Model' to use for generating Artifacts.
     */
    private function initBusinessBundle()
    {
        // Step 1 - Create the Business Bundle that will contain your model
        $this->businessBundle = $this->createBusinessBundle();

        // Step 2 - Add your Datasets to your Bundle
        $this->initDatasets($this->businessBundle);

        // Step 3 - Add your Business Data Model to your Bundle
        $this->initBusinessDataModel($this->businessBundle);

        // Prepare the relationships for a relational database
        $adapter = new RelationalDatabaseAdapter();
        $adapter->generateRelationalFields($this->businessBundle);

        // Step 4 - Add your Business Process Model to your Bundle
        $this->initBusinessProcessingModel($this->businessBundle);
    }

    /**
     * Creates an empty 'Business Bundle'.
     *
     * @return BusinessBundle The Business Bundle to use for modeling your application
     */
    private function createBusinessBundle()
    {
        $bundle = new BusinessBundle('io.codeprimer.sample', 'Channel');
        $bundle->setDescription('This sample application is used to show how model a business application using CodePrimer');

        return $bundle;
    }

    private function initDatasets(BusinessBundle $bundle)
    {
        $factory = new DatasetFactory();

        $bundle->addDataset($factory->createUserRole());
        $bundle->addDataset($factory->createUserStatus());
        $bundle->addDataset($factory->createArticleStatus());
    }

    /**
     * Creates the application's Business Data Model for a given 'Business Bundle'.
     *
     * @param BusinessBundle $bundle The 'Business Bundle' used to store the Business Data Model
     */
    private function initBusinessDataModel(BusinessBundle $bundle)
    {
        // Create the BusinessModel objects defining the application's data model
        $modelFactory = new BusinessModelFactory($bundle);

        $bundle->addBusinessModel($modelFactory->createUserDataModel());
        $bundle->addBusinessModel($modelFactory->createArticleDataModel());
        $bundle->addBusinessModel($modelFactory->createArticleViewDataModel());
        $bundle->addBusinessModel($modelFactory->createTopicDataModel());
        $bundle->addBusinessModel($modelFactory->createLabelDataModel());
        $bundle->addBusinessModel($modelFactory->createSuggestedLabelDataModel());
        $bundle->addBusinessModel($modelFactory->createAccountDataModel());
        $bundle->addBusinessModel($modelFactory->createInterestDataModel());
        $bundle->addBusinessModel($modelFactory->createTransactionDataModel());
        $bundle->addBusinessModel($modelFactory->createPayoutDataModel());

        // Establish the relationships between the various BusinessModel objects
        $packageHelper = new BusinessBundleHelper();
        $packageHelper->buildRelationships($bundle);
    }

    /**
     * Creates the application's Business Processing Model for a given 'Business Bundle'.
     *
     * @param BusinessBundle $businessBundle The 'Business Bundle' used to store the Business Data Model
     */
    private function initBusinessProcessingModel(BusinessBundle $businessBundle)
    {
        $processFactory = new BusinessProcessFactory();

        $businessBundle->addBusinessProcess($processFactory->createLoginProcess($this->businessBundle));
        $businessBundle->addBusinessProcess($processFactory->createLogoutProcess($this->businessBundle));
        $businessBundle->addBusinessProcess($processFactory->createRegisterProcess($this->businessBundle));

        $businessBundle->addBusinessProcess($processFactory->createNewArticleProcess($this->businessBundle));
        $businessBundle->addBusinessProcess($processFactory->createUpdateArticleProcess($this->businessBundle));
        $businessBundle->addBusinessProcess($processFactory->createSubmitArticleProcess($this->businessBundle));
    }
}

$app = new ChannelApp();
try {
    $app->primePhpArtifacts();
    $app->primeMySqlArtifacts();
    $app->primeMarkdownArtifacts();
} catch (Exception $e) {
    echo 'Failed to prime application: '.$e->getMessage();
}
