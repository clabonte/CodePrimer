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
 * This factory is used to create the various BusinessProcess instances that are part of your application.
 * Instructions:
 *  - Create a set of public methods starting with the 'create' name prefix (e.g. createRegisterProcess())
 *  - Each 'create' method must return a 'BusinessProcess' instance describing a business model used in your application.
 *  - The bundle.php file will automatically invoke all the 'create' methods to add all BusinessProcess instances to your application business bundle.
 */
class BusinessProcessFactory
{
    /** @var BusinessBundle */
    private $businessBundle;

    /**
     * BusinessProcessFactory constructor.
     */
    public function __construct(BusinessBundle $businessBundle)
    {
        $this->businessBundle = $businessBundle;
    }

    /**
     * This is a sample method to illustrate how to create a simple BusinessProcess object to use by CodePrimer
     * TODO Delete or update this method based on your needs.
     */
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

    /**
     * This is another sample method to illustrate how to create a simple BusinessProcess object to use by CodePrimer
     * TODO Delete or update this method based on your needs.
     */
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

    /**
     * This is another sample method to illustrate how to create a simple BusinessProcess object to use by CodePrimer
     * TODO Delete or update this method based on your needs.
     */
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

    // TODO Add other create methods for each BusinessProcess used by your application
}
