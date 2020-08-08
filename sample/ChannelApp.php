<?php

use CodePrimer\Adapter\RelationalDatabaseAdapter;
use CodePrimer\Builder\ArtifactBuilderFactory;
use CodePrimer\Helper\BusinessBundleHelper;
use CodePrimer\Helper\DataBundleHelper;
use CodePrimer\Helper\FieldType;
use CodePrimer\Model\BusinessBundle;
use CodePrimer\Model\BusinessModel;
use CodePrimer\Model\BusinessProcess;
use CodePrimer\Model\Constraint;
use CodePrimer\Model\Data\Data;
use CodePrimer\Model\Data\DataBundle;
use CodePrimer\Model\Data\ExistingData;
use CodePrimer\Model\Data\InputData;
use CodePrimer\Model\Derived\Event;
use CodePrimer\Model\Derived\Message;
use CodePrimer\Model\Field;
use CodePrimer\Renderer\TemplateRenderer;
use CodePrimer\Template\Artifact;
use CodePrimer\Template\TemplateRegistry;
use Twig\Loader\FilesystemLoader;

require '../vendor/autoload.php';

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

    /** @var DataBundleHelper */
    private $dataBundleHelper;

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

        // 2. Prime 'Business Model' source code
        $this->templateRenderer->setBaseFolder(self::PROJECT_OUTPUT_PATH);
        $artifact = new Artifact(Artifact::CODE, 'model', 'php');
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
        // 1. Prime 'Data Model' documentation in Markdown
        $this->templateRenderer->setBaseFolder(self::PROJECT_OUTPUT_PATH);
        $artifact = new Artifact(Artifact::DOCUMENTATION, 'model', 'markdown');
        $this->primeArtifact($artifact);

        // 2. Prime 'Processing Model' overview documentation in Markdown
        $this->templateRenderer->setBaseFolder(self::PROJECT_OUTPUT_PATH);
        $artifact = new Artifact(Artifact::DOCUMENTATION, 'process', 'markdown', 'index');
        $this->primeArtifact($artifact);

        // 3. Prime 'Processing Model' detailed documentation in Markdown
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
        $this->dataBundleHelper = new DataBundleHelper();
    }

    /**
     * This method prepares the 'Business Model' to use for generating Artifacts.
     */
    private function initBusinessBundle()
    {
        // Step 1 - Create the Business Bundle that will contain your model
        $this->businessBundle = $this->createBusinessBundle();

        // Step 2 - Add your Business Data Model to your Bundle
        $this->initBusinessDataModel($this->businessBundle);

        // Prepare the relationships for a relational database
        $adapter = new RelationalDatabaseAdapter();
        $adapter->generateRelationalFields($this->businessBundle);

        // Step 3 - Add your Business Process Model to your Bundle
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

    /**
     * Creates the application's Business Data Model for a given 'Business Bundle'.
     *
     * @param BusinessBundle $bundle The 'Business Bundle' used to store the Business Data Model
     */
    private function initBusinessDataModel(BusinessBundle $bundle)
    {
        // Create the BusinessModel objects defining the application's data model
        $bundle->addBusinessModel($this->createUserDataModel());
        $bundle->addBusinessModel($this->createArticleDataModel());
        $bundle->addBusinessModel($this->createArticleViewDataModel());
        $bundle->addBusinessModel($this->createTopicDataModel());
        $bundle->addBusinessModel($this->createLabelDataModel());
        $bundle->addBusinessModel($this->createSuggestedLabelDataModel());
        $bundle->addBusinessModel($this->createAccountDataModel());
        $bundle->addBusinessModel($this->createInterestDataModel());
        $bundle->addBusinessModel($this->createTransactionDataModel());
        $bundle->addBusinessModel($this->createPayoutDataModel());

        // Establish the relationships between the various BusinessModel objects
        $packageHelper = new BusinessBundleHelper();
        $packageHelper->buildRelationships($bundle);
    }

    /** Creates the 'User' BusinessModel for our sample application.
     *  @see https://github.com/clabonte/codeprimer/blob/sample-app/doc/sample/DataModel.md
     */
    private function createUserDataModel()
    {
        $businessModel = new BusinessModel('User', 'A registered used in our application');
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

    private function createArticleDataModel()
    {
        $businessModel = new BusinessModel('Article', 'An article in our application');
        $businessModel->setAudited(true);

        // Step 1: Add business attributes
        $businessModel
            ->addField(
                (new Field('title', FieldType::STRING, 'Article title'))
                    ->setExample('How to go from idea to production-ready solution in a day with CodePrimer')
                    ->setSearchable(true)
            )
            ->addField(
                (new Field('description', FieldType::TEXT, 'Article description'))
                    ->setExample('This article explains how architects can save days/weeks of prepare to get a production-grade application up and running using the technology of their choice.')
                    ->setSearchable(true)
            )
            ->addField(
                (new Field('body', FieldType::TEXT, 'The article main body'))
                    ->setSearchable(true)
            )

            // Step 2: Add business relations
            ->addField(new Field('author', 'User', 'User who created the article'))
            ->addField(new Field('topic', 'Topic', 'Topic to which this article belongs'))
            ->addField(
                (new Field('labels', 'Label', 'List of labels associated with this article by the author'))
                    ->setList(true)
            )
            ->addField(
                (new Field('views', 'ArticleView', 'List of views associated with this article'))
                    ->setList(true)
            )

            // Step 3: Add internal fields
            ->addField(
                (new Field('id', FieldType::UUID, "Article's unique ID in our system"))
                    ->setMandatory(true)
                    ->setManaged(true)
                    ->setExample('22d5a494-ad3d-4032-9fbe-8f5eb0587396')
            )
            ->addField(
                (new Field('created', FieldType::DATETIME, 'The date and time at which this article was created'))
                    ->setManaged(true)
            )
            ->addField(
                (new Field('updated', FieldType::DATETIME, 'The date and time at which this article was updated'))
                    ->setManaged(true)
            );

        return $businessModel;
    }

    private function createArticleViewDataModel()
    {
        $businessModel = new BusinessModel('ArticleView', 'An article view action by a registered user');

        // Step 1: Add business attributes
        $businessModel
            ->addField(new Field('count', FieldType::INTEGER, 'Number of times this user viewed this article', false, 1))

            // Step 2: Add business relations
            ->addField(new Field('user', 'User', 'User who viewed the article', true))
            ->addField(new Field('article', 'Article', 'Article associated with the view', true))

            // Step 3: Add internal fields
            ->addField(
                (new Field('created', FieldType::DATETIME, 'The date and time at which this article was viewed the first time by this user'))
                    ->setManaged(true)
            )
            ->addField(
                (new Field('updated', FieldType::DATETIME, 'The date and time at which this article was viewed the last time by this user'))
                    ->setManaged(true)
            );

        return $businessModel;
    }

    private function createTopicDataModel()
    {
        $businessModel = new BusinessModel('Topic', 'A high level topic that can be used to categorize articles');
        $businessModel->setAudited(true);

        // Step 1: Add business attributes
        $businessModel
            ->addField(
                (new Field('name', FieldType::STRING, 'A topic short description'))
                    ->setMandatory(true)
                    ->setExample('Technology')
                    ->setSearchable(true)
            )
            ->addField(
                (new Field('description', FieldType::STRING, 'A description of what kind of articles should be associated with'))
                    ->setMandatory(true)
                    ->setExample('Articles related to the latest trends in Technology to keep you up to date')
                    ->setSearchable(true)
            )

            // Step 2: Add business relations
            ->addField(
                (new Field('articles', 'Article', 'List of articles associated with this topic'))
                    ->setList(true)
            )
            ->addField(
                (new Field('suggested labels', 'SuggestedLabel', 'List of labels that are often associated with this topic'))
                    ->setList(true)
            );

        // Step 4: Add unique field constraints along with the error message to use when violated
        $businessModel
            ->addUniqueConstraint(
                (new Constraint('uniqueName'))
                    ->addField($businessModel->getField('name'))
                    ->setDescription('The topic name must be unique.')
                    ->setErrorMessage('This topic name is already in use. Please select another one.')
            );

        return $businessModel;
    }

    private function createLabelDataModel()
    {
        $businessModel = new BusinessModel('Label', 'A tag that can be associated with an article by an author to help in its classification');

        // Step 1: Add business attributes
        $businessModel
            ->addField(
                (new Field('tag', FieldType::STRING, 'A unique tag'))
                    ->setMandatory(true)
                    ->setExample('PHP')
                    ->setSearchable(true)
            )

            // Step 2: Add business relations
            ->addField(
                (new Field('articles', 'Article', 'List of articles associated with this tag'))
                    ->setList(true)
            );

        // Step 4: Add unique field constraints along with the error message to use when violated
        $businessModel
            ->addUniqueConstraint(
                (new Constraint('uniqueTag'))
                    ->addField($businessModel->getField('tag'))
                    ->setDescription('The tag must be unique.')
                    ->setErrorMessage('This tag is already in use. Please select another one.')
            );

        return $businessModel;
    }

    private function createSuggestedLabelDataModel()
    {
        $businessModel = new BusinessModel('SuggestedLabel', 'Labels often associated with a given topic');

        // Step 1: Add business attributes
        $businessModel
            ->addField(new Field('count', FieldType::INTEGER, 'Number of times this label has been associated with this topic', false, 1))

            // Step 2: Add business relations
            ->addField(new Field('label', 'Label', 'Label associated with this suggestion', true))
            ->addField(new Field('topic', 'Topic', 'Topic associated with this suggestion', true));

        return $businessModel;
    }

    private function createAccountDataModel()
    {
        $businessModel = new BusinessModel('Account', 'Author account to track earnings');
        $businessModel->setAudited(true);

        // Step 1: Add business attributes
        $businessModel
            ->addField(
                (new Field('balance', FieldType::PRICE, 'Current amount owed to the author'))
                    ->setExample('9.90$')
                    ->setSearchable(true)
            )
            ->addField(
                (new Field('lifetime', FieldType::PRICE, 'Lifetime earnings associated with this account'))
                    ->setExample('200$')
                    ->setSearchable(true)
            )

            // Step 2: Add business relations
            ->addField(new Field('member', 'User', 'Member associated with this account', true))
            ->addField(new Field('topic', 'Topic', 'Topic to which this article belongs'))
            ->addField(
                (new Field('payouts', 'Payout', 'List of payouts already made to the user'))
                    ->setList(true)
            )
            ->addField(
                (new Field('transactions', 'Transaction', 'List of transactions used to track earnings details'))
                    ->setList(true)
            )

            // Step 3: Add internal fields
            ->addField(
                (new Field('id', FieldType::UUID, "Account's unique ID in our system"))
                    ->setMandatory(true)
                    ->setManaged(true)
                    ->setExample('b34d38eb-1164-4289-98b4-65706837c4d7')
            )
            ->addField(
                (new Field('created', FieldType::DATETIME, 'The date and time at which this account was created'))
                    ->setManaged(true)
            )
            ->addField(
                (new Field('updated', FieldType::DATETIME, 'The date and time at which this account was updated last'))
                    ->setManaged(true)
            );

        return $businessModel;
    }

    private function createInterestDataModel()
    {
        $businessModel = new BusinessModel('Interest', 'Interest expressed by a user to be notified of new articles');

        // Step 1: Add business attributes
        $businessModel
            ->addField(new Field('instantNotification', FieldType::BOOLEAN, 'Whether the user wants to be notified ASAP when a new article matching this interest is published', false, false))

            // Step 2: Add business relations
            ->addField(new Field('member', 'User', 'User who expressed the interest', true))
            ->addField(new Field('label', 'Label', 'Label associated with this interest'))
            ->addField(new Field('topic', 'Topic', 'Topic associated with this interest', true));

        return $businessModel;
    }

    private function createTransactionDataModel()
    {
        $businessModel = new BusinessModel('Transaction', 'An article view that is tied with some earnings');

        // Step 1: Add business attributes
        $businessModel
            ->addField(new Field('amount', FieldType::PRICE, 'Earnings associated with this transaction', true))

            // Step 2: Add business relations
            ->addField(new Field('account', 'Account', 'Account associated with this transaction', true))
            ->addField(new Field('articleView', 'ArticleView', 'ArticleView that triggered the transaction', true))
            ->addField(new Field('payout', 'Payout', 'The payout associated with this transaction, set once the payout is issued'))

            // Step 3: Add internal fields
            ->addField(
                (new Field('created', FieldType::DATETIME, 'The date and time at which this transaction was viewed the first time by this user'))
                    ->setManaged(true)
            );

        return $businessModel;
    }

    private function createPayoutDataModel()
    {
        $businessModel = new BusinessModel('Payout', 'Tracks payment made to an author');

        // Step 1: Add business attributes
        $businessModel
            ->addField(new Field('amount', FieldType::PRICE, 'Amount associated with this payout', true))

            // Step 2: Add business relations
            ->addField(new Field('account', 'Account', 'Account associated with this transaction', true))
            ->addField(
                (new Field('transactions', 'Transaction', 'The list of transactions associated with this payout'))
                    ->setList(true)
            )

            // Step 3: Add internal fields
            ->addField(
                (new Field('created', FieldType::DATETIME, 'The date and time at which this payment was issued'))
                    ->setManaged(true)
            )
            ->addField(
                (new Field('updated', FieldType::DATETIME, 'The date and time at which this payment was updated last'))
                    ->setManaged(true)
            );

        return $businessModel;
    }

    /**
     * Creates the application's Business Processing Model for a given 'Business Bundle'.
     *
     * @param BusinessBundle $businessBundle The 'Business Bundle' used to store the Business Data Model
     */
    private function initBusinessProcessingModel(BusinessBundle $businessBundle)
    {
        $businessBundle->addBusinessProcess($this->createCreateArticleProcess());
    }

    private function createCreateArticleProcess(): BusinessProcess
    {
        // 1. Define the data required for this process
        //    - Title, body: mandatory input
        //    - Description: optional input
        //    - Author: existing from context
        $dataBundle = new DataBundle();

        $article = $this->businessBundle->getBusinessModel('Article');
        $this->dataBundleHelper->addFieldsAsMandatoryInput($dataBundle, $article, ['title', 'body', 'topic'], InputData::NEW, Data::REFERENCE);
        $this->dataBundleHelper->addFieldsAsOptionalInput($dataBundle, $article, ['description', 'labels'], InputData::NEW_OR_UPDATED);
        $this->dataBundleHelper->addFieldsAsExisting($dataBundle, $article, ['author'], ExistingData::CONTEXT, Data::REFERENCE);

        // 2. Define the event that will be used as a trigger for this process
        $event = new Event(
            'New Article',
            'Event triggered when a new article is created by an author');
        $event->addDataBundle($dataBundle);

        // 3. Create the Business Process
        $businessProcess = new BusinessProcess(
            'Create Article',
            'Allow an author to create an article in Draft state',
            $event);

        // Set the process attributes:
        //  = Category = Articles
        //  - Type = Create
        //  - Synchronized (default behavior)
        //  - Exposed to untrusted parties
        //  - Restricted to users with the 'Author' role
        $businessProcess
            ->setCategory('Articles')
            ->setType(BusinessProcess::CREATE)
            ->setExternalAccess(true)
            ->addRole(self::AUTHOR);

        // 4. Set the process outcomes
        //  - Insert in the database
        $dbBundle = new DataBundle();
        $this->dataBundleHelper->addBusinessModelAsInput($dbBundle, $article, InputData::NEW);
        $businessProcess->setInternalUpdates([$dbBundle]);

        //  - Publish 'article.new' message
        $msgBundle = new DataBundle();
        $this->dataBundleHelper->addBusinessModelAsExisting($msgBundle, $article, ExistingData::INTERNAL, '', Data::FULL);
        $message = new Message('article.new');
        $message->addDataBundle($msgBundle);
        $businessProcess->setMessages([$message]);

        return $businessProcess;
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
