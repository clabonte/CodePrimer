<?php

use CodePrimer\Builder\ArtifactBuilderFactory;
use CodePrimer\Helper\FieldType;
use CodePrimer\Model\BusinessModel;
use CodePrimer\Model\Constraint;
use CodePrimer\Model\Field;
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
        $bundle->addBusinessModel($this->createUserDataModel());
    }

    /** Creates the 'User' BusinessModel for our sample application
     *  @see https://github.com/clabonte/codeprimer/blob/sample-app/doc/sample/DataModel.md
     */
    private function createUserDataModel()
    {
        $businessModel = new  BusinessModel('User', 'A registered used in our application');
        $businessModel->setAudited(true);

        // Step 1: Add business attributes
        $businessModel
            ->addField(
                (new Field('firstName', FieldType::STRING, 'User first name'))
                    ->setExample('John')
                    ->setSearchable(true)
            )
            ->addField(
                (new Field('lastName', FieldType::STRING, 'User last name'))
                    ->setExample('Doe')
                    ->setSearchable(true)
            )
            ->addField(
                (new Field('nickname', FieldType::STRING, 'The name used to identify this user publicly in the application'))
                    ->setExample('JohnDoe')
                    ->setSearchable(true)
            )
            ->addField(
                (new Field('email', FieldType::EMAIL, 'User email address'))
                    ->setExample('john.doe@test.com')
                    ->setMandatory(true)
                    ->setSearchable(true)
            )
            ->addField(
                (new Field('password', FieldType::PASSWORD, 'User password to access our application'))
                    ->setMandatory(true)
            )

            // Step 2: Add business relations
            ->addField(new Field('account', 'Account', "User's account to track earnings"))
            ->addField(
                (new Field('articles', 'Article', 'List of articles owned by this user'))
                    ->setList(true)
            )
            ->addField(
                (new Field('views', 'ArticleView', 'List of articles viewed by this user'))
                    ->setList(true)
            )
            ->addField(
                (new Field('interests', 'Interest', 'List of topics user is interested in'))
                    ->setList(true)
            )

            // Step 3: Add internal fields
            ->addField(
                (new Field('id', FieldType::UUID, "User's unique ID in our system"))
                    ->setMandatory(true)
                    ->setManaged(true)
                    ->setExample('b34d38eb-1164-4289-98b4-65706837c4d7')
            )
            ->addField(
                (new Field('created', FieldType::DATETIME, 'The date and time at which this user was created'))
                    ->setManaged(true)
            )
            ->addField(
                (new Field('updated', FieldType::DATETIME, 'The date and time at which this user was updated'))
                    ->setManaged(true)
            );

        // Step 4: Add unique field constraints along with the error message to use when violated
        $businessModel
            ->addUniqueConstraint(
                (new Constraint('uniqueEmail'))
                    ->addField($businessModel->getField('email'))
                    ->setDescription('The email address must uniquely identify the user for login in')
                    ->setErrorMessage('This email address is already in use. Please select another one or recover your password if you forgot it.')
            )
            ->addUniqueConstraint(
                (new Constraint('uniqueNickname'))
                    ->addField($businessModel->getField('nickname'))
                    ->setDescription("The nickname uniquely identifies the user in the application's public spaces")
                    ->setErrorMessage('This nickname name is already in use. Please select another one.')
            );

        return $businessModel;
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