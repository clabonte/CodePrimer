<?php

use CodePrimer\Helper\DataBundleHelper;
use CodePrimer\Helper\ProcessType;
use CodePrimer\Model\BusinessBundle;
use CodePrimer\Model\BusinessProcess;
use CodePrimer\Model\Data\ContextDataBundle;
use CodePrimer\Model\Data\Data;
use CodePrimer\Model\Data\EventDataBundle;
use CodePrimer\Model\Data\InternalDataBundle;
use CodePrimer\Model\Data\MessageDataBundle;
use CodePrimer\Model\Derived\Event;
use CodePrimer\Model\Derived\Message;

/**
 * Factory used to create the various BusinessProcesses instances needed in our Channel sample application.
 */
class BusinessProcessFactory
{
    // ROLE CONSTANTS
    const REGULAR_MEMBER = 'member';
    const PREMIUM_MEMBER = 'premium';
    const AUTHOR = 'author';
    const ADMIN = 'admin';

    /** @var BusinessBundle */
    private $businessBundle;

    /**
     * BusinessProcessFactory constructor.
     */
    public function __construct(BusinessBundle $businessBundle)
    {
        $this->businessBundle = $businessBundle;
    }

    public function createRegisterProcess(): BusinessProcess
    {
        $dataBundleHelper = new DataBundleHelper($this->businessBundle);
        $user = $this->businessBundle->getBusinessModel('User');

        // 1. Define the input data required for this process
        $eventBundle = new EventDataBundle();
        $dataBundleHelper->addFieldsAsMandatory($eventBundle, $user, ['email', 'password']);
        $dataBundleHelper->addFieldsAsOptional($eventBundle, $user, ['firstName', 'lastName', 'nickname']);

        // 2. Define the event that will trigger this process
        $event = new Event(
            'Registration Request',
            'Event triggered when user wants to register with the application');
        $event->addDataBundle($eventBundle);

        // 3. Create the Business Process
        $businessProcess = new BusinessProcess(
            'User Registration',
            'This process is triggered when a user wants to register with our application. Upon success, the user is created internally but is not logged in yet.',
            $event);

        // Set the process attributes
        $businessProcess
            ->setCategory('Users')
            ->setType(ProcessType::REGISTER)
            ->setExternalAccess(true);

        // 4. Define the bundle of data required by this process
        // N/A

        // 5. Define the bundle of data produced by this process
        $internalBundle = new InternalDataBundle();
        $internalBundle->setDescription('User profile created');
        $dataBundleHelper->addBusinessModelAttributes($internalBundle, $user, true);
        $businessProcess->addProducedData($internalBundle);

        // 6. Define the message(s) published by this process
        $msgBundle = $dataBundleHelper->createMessageDataBundleFromExisting($internalBundle);
        $msgBundle->remove($user->getName(), 'password');
        $message = new Message('user.new');
        $message
            ->setDescription('Message published when a new user has been created in our application')
            ->addDataBundle($msgBundle);

        $businessProcess->addMessage($message);

        return $businessProcess;
    }

    public function createLoginProcess(): BusinessProcess
    {
        $dataBundleHelper = new DataBundleHelper($this->businessBundle);
        $user = $this->businessBundle->getBusinessModel('User');

        // 1. Define the input data required for this process
        $eventBundle = new EventDataBundle();
        $dataBundleHelper->addFieldsAsMandatory($eventBundle, $user, ['email', 'password']);

        // 2. Define the event that will trigger this process
        $event = new Event(
            'Login Request',
            'Event triggered when user wants to login with the application');
        $event->addDataBundle($eventBundle);

        // 3. Create the Business Process
        $businessProcess = new BusinessProcess(
            'User Login',
            'This process is triggered when a user wants to login with our application. Upon success, the context is updated with the user information.',
            $event);

        // Set the process attributes
        $businessProcess
            ->setCategory('Users')
            ->setType(ProcessType::LOGIN)
            ->setExternalAccess(true);

        // 4. Define the bundle of data required by this process
        // N/A

        // 5. Define the bundle of data produced by this process
        $contextBundle = new ContextDataBundle();
        $contextBundle->setDescription('User information to add to the context');
        $dataBundleHelper->addFields($contextBundle, $user, ['id', 'firstName', 'lastName', 'nickname', 'email'/*, 'role'*/]);
        $businessProcess->addProducedData($contextBundle);

        // 6. Publish a 'user.login' message with the same info as the one put in the context
        $msgBundle = $dataBundleHelper->createMessageDataBundleFromExisting($contextBundle);
        $message = new Message('user.login');
        $message
            ->setDescription('Message published when a user has successfully authenticated with our application')
            ->addDataBundle($msgBundle);

        $businessProcess->addMessage($message);

        return $businessProcess;
    }

    public function createLogoutProcess(): BusinessProcess
    {
        $dataBundleHelper = new DataBundleHelper($this->businessBundle);
        $user = $this->businessBundle->getBusinessModel('User');

        // 1. Define the input data required for this process
        // N/A

        // 2. Define the event that will trigger this process
        $event = new Event(
            'Logout Request',
            'Event triggered when user wants to leave the application');

        // 3. Create the Business Process
        $businessProcess = new BusinessProcess(
            'User Logout',
            'This process is triggered when a user is leaving the application. Upon success, the context is wiped out to prevent its reuse.',
            $event);

        // Set the process attributes
        $businessProcess
            ->setCategory('Users')
            ->setType(ProcessType::LOGOUT)
            ->setExternalAccess(true);

        // 4. Define the bundle of data required by this process
        $contextBundle = new ContextDataBundle();
        $contextBundle->setDescription('Information from the context about the user to logout');
        $dataBundleHelper->addFields($contextBundle, $user, ['id']);
        $businessProcess->addRequiredData($contextBundle);

        // 5. Define the bundle of data produced by this process
        // N/A

        // 6. Define the message(s) published by this process
        $msgBundle = new MessageDataBundle('', 'Details about the user who just logged out');
        $dataBundleHelper->addFields($msgBundle, $user, ['id', 'firstName', 'lastName', 'nickname', 'email'/*, 'role'*/]);
        $message = new Message('user.logout');
        $message
            ->setDescription('Message published when a user has successfully logged out from our application')
            ->addDataBundle($msgBundle);

        $businessProcess->addMessage($message);

        return $businessProcess;
    }

    public function createNewArticleProcess(): BusinessProcess
    {
        $dataBundleHelper = new DataBundleHelper($this->businessBundle);
        $article = $this->businessBundle->getBusinessModel('Article');
        $user = $this->businessBundle->getBusinessModel('User');

        // 1. Define the input data required for this process
        //    - Mandatory:
        //      - title
        //      - body
        //      - topic: reference
        //    - Optional:
        //      - description
        //      - labels: full
        $eventBundle = new EventDataBundle();
        $dataBundleHelper->addFieldsAsMandatory($eventBundle, $article, ['title', 'body', 'topic'], Data::REFERENCE);
        $dataBundleHelper->addFieldsAsOptional($eventBundle, $article, ['description', 'labels'], Data::FULL);

        // 2. Define the event that will be used as a trigger for this process
        $event = new Event(
            'New Article',
            'Event triggered when a new article is created by an author');
        $event->addDataBundle($eventBundle);

        // 3. Create the Business Process
        $businessProcess = new BusinessProcess(
            'New Article Creation',
            "This process is triggered when an author creates a new article. The article will be in 'Draft' status until the author decides to submit it for approval.",
            $event);

        // Set the process attributes:
        //  = Category = Articles
        //  - Type = Create
        //  - Synchronized (default behavior)
        //  - Exposed to untrusted parties
        //  - Restricted to users with the 'Author' role
        $businessProcess
            ->setCategory('Articles')
            ->setType(ProcessType::CREATE)
            ->setExternalAccess(true)
            ->addRole(self::AUTHOR);

        // 4. Define the bundle of data required by this process
        $contextData = new ContextDataBundle();
        $contextData->setDescription("Set the article's author based on the user who triggered the event.");
        $dataBundleHelper->addFields($contextData, $user, ['id']);
        $businessProcess->addRequiredData($contextData);

        // 5. Define the bundle of data produced by this process
        $internalData = new InternalDataBundle();
        $internalData->setDescription('Save the new article internally');
        $dataBundleHelper->addBusinessModel($internalData, $article, Data::REFERENCE);
        $businessProcess->addProducedData($internalData);

        // 6. Publish 'article.new' message
        $msgBundle = new MessageDataBundle();
        $dataBundleHelper->addBusinessModelExceptFields($msgBundle, $article, ['views'], Data::FULL);
        $message = new Message('article.new');
        $message
            ->setDescription('Message published when a new, draft article has been created by a user')
            ->addDataBundle($msgBundle);
        $businessProcess->addMessage($message);

        return $businessProcess;
    }

    public function createUpdateArticleProcess(): BusinessProcess
    {
        $dataBundleHelper = new DataBundleHelper($this->businessBundle);
        $article = $this->businessBundle->getBusinessModel('Article');
        $user = $this->businessBundle->getBusinessModel('User');

        // 1. Define the input data required for this process
        $eventBundle = new EventDataBundle();
        $dataBundleHelper->addFieldsAsOptional($eventBundle, $article, ['topic'], Data::REFERENCE);
        $dataBundleHelper->addFieldsAsOptional($eventBundle, $article, ['title', 'body', 'description', 'labels'], Data::FULL);

        // 2. Define the event that will be used as a trigger for this process
        $event = new Event(
            'Update Article',
            'Event triggered when an existing article is updated by its author');
        $event->addDataBundle($eventBundle);

        // 3. Create the Business Process
        $businessProcess = new BusinessProcess(
            'Article Editing',
            'This process is triggered when an author wants to modify one of his existing articles.',
            $event);

        // Set the process attributes:
        $businessProcess
            ->setCategory('Articles')
            ->setType(ProcessType::UPDATE)
            ->setExternalAccess(true)
            ->addRole(self::AUTHOR);

        // 4. Define the bundle of data required by this process
        $contextData = new ContextDataBundle();
        $contextData->setDescription("Retrieve the user id from the context to ensure he is the article's author");
        $dataBundleHelper->addFields($contextData, $user, ['id']);
        $businessProcess->addRequiredData($contextData);

        // 5. Define the bundle of data produced by this process
        $internalData = new InternalDataBundle();
        $internalData->setDescription('Update the article internally');
        $dataBundleHelper->addBusinessModel($internalData, $article, Data::REFERENCE);
        $businessProcess->addProducedData($internalData);

        // 6. Publish 'article.updated' message
        $msgBundle = new MessageDataBundle();
        $dataBundleHelper->addBusinessModelExceptFields($msgBundle, $article, ['views'], Data::FULL);
        $message = new Message('article.updated');
        $message
            ->setDescription('Message published when an existing article has been updated by a user')
            ->addDataBundle($msgBundle);
        $businessProcess->addMessage($message);

        return $businessProcess;
    }

    public function createSubmitArticleProcess(): BusinessProcess
    {
        $dataBundleHelper = new DataBundleHelper($this->businessBundle);
        $article = $this->businessBundle->getBusinessModel('Article');
        $user = $this->businessBundle->getBusinessModel('User');

        // 1. Define the input data required for this process
        $eventBundle = new EventDataBundle();
        $dataBundleHelper->addFieldsAsMandatory($eventBundle, $article, ['id']);

        // 2. Define the event that will be used as a trigger for this process
        $event = new Event(
            'Submit Article',
            'Event triggered when an article is submitted for review');
        $event->addDataBundle($eventBundle);

        // 3. Create the Business Process
        $businessProcess = new BusinessProcess(
            'Submit Article for Review',
            'This process is triggered when an author has finished editing an article and is ready to submit it for review.',
            $event);

        // Set the process attributes:
        $businessProcess
            ->setCategory('Articles')
            ->setType(ProcessType::STATUS_UPDATE)
            ->setExternalAccess(true)
            ->addRole(self::AUTHOR);

        // 4. Define the bundle of data required by this process
        $contextData = new ContextDataBundle();
        $contextData->setDescription("Retrieve the user id from the context to ensure he is the article's author");
        $dataBundleHelper->addFields($contextData, $user, ['id']);
        $businessProcess->addRequiredData($contextData);

        // 5. Define the bundle of data produced by this process
        $internalData = new InternalDataBundle();
        $internalData->setDescription('Update the article status');
        $dataBundleHelper->addFields($internalData, $article, ['status']);
        $businessProcess->addProducedData($internalData);

        // 6. Publish message
        // N/A

        return $businessProcess;
    }
}
