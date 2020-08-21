<?php

namespace CodePrimer\Tests\Helper;

use CodePrimer\Helper\BusinessBundleHelper;
use CodePrimer\Helper\DataBundleHelper;
use CodePrimer\Helper\FieldType;
use CodePrimer\Helper\ProcessType;
use CodePrimer\Model\BusinessBundle;
use CodePrimer\Model\BusinessModel;
use CodePrimer\Model\BusinessProcess;
use CodePrimer\Model\Constraint;
use CodePrimer\Model\Data\ContextDataBundle;
use CodePrimer\Model\Data\EventDataBundle;
use CodePrimer\Model\Data\InternalDataBundle;
use CodePrimer\Model\Derived\Event;
use CodePrimer\Model\Derived\Message;
use CodePrimer\Model\Field;

class TestHelper
{
    /**
     * @param bool $withBusinessModels
     */
    public static function getSampleBusinessBundle($withBusinessModels = true, $withRelationships = true, $withBusinessProcesses = true): BusinessBundle
    {
        $businessBundle = new BusinessBundle('CodePrimer Tests', 'FunctionalTest');

        if ($withBusinessModels) {
            self::addSampleBusinessModels($businessBundle);
            if ($withBusinessProcesses) {
                self::addSampleBusinessProcesses($businessBundle);
            }
        }

        if ($withRelationships) {
            // Build the relationships between the entities
            $businessBundleHelper = new BusinessBundleHelper();
            $businessBundleHelper->buildRelationships($businessBundle);
        }

        return $businessBundle;
    }

    public static function addSampleBusinessModels(BusinessBundle $businessBundle)
    {
        $businessModel = new  BusinessModel('User', 'This entity represents a user');
        $businessModel
            ->setAudited(true)
            ->addField(
                (new Field('id', FieldType::UUID, "The user's unique ID in our system"))
                    ->setMandatory(true)
                    ->setManaged(true)
                    ->setExample('b34d38eb-1164-4289-98b4-65706837c4d7')
                    ->setIdentifier(true)
            )
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
                (new Field('nickname', FieldType::STRING, 'The name used to identify this user publicly on the site'))
                    ->setExample('JohnDoe')
                    ->setSearchable(true)
            )
            ->addField(
                (new Field('email', FieldType::EMAIL, 'User email address'))
                    ->setMandatory(true)
                    ->setSearchable(true)
            )
            ->addField(
                (new Field('password', FieldType::PASSWORD, 'User password'))
                    ->setMandatory(true)
            )
            ->addField(
                (new Field('created', FieldType::DATETIME, 'The date and time at which this user was created'))
                    ->setManaged(true)
            )
            ->addField(
                (new Field('updated', FieldType::DATETIME, 'The date and time at which this user was updated'))
                    ->setManaged(true)
            )
            ->addField(
                (new Field('crmId', FieldType::STRING, 'The ID of this user in our external CRM'))
                    ->setExample('2c3b1c3e-b29c-4564-80c4-e4b95cfbfc81')
            )
            ->addField(
                (new Field('activationCode', FieldType::RANDOM_STRING, 'The code required to validate the user\'s account'))
                    ->setManaged(true)
                    ->setExample('qlcS7L')
            )
            ->addField(new Field('stats', 'UserStats', 'User login statistics'))
            ->addField(new Field('subscription', 'Subscription', 'The plan to which the user is subscribed'))
            ->addField(
                (new Field('metadata', 'Metadata', 'Extra information about the user'))
                    ->setList(true)
            )
            ->addField(
                (new Field('posts', 'Post', 'Blog posts created by this user'))
                    ->setList(true)
            )
            ->addField(
                (new Field('topics', 'Topic', 'List of topics this user to allowed to create posts for'))
                    ->setList(true)
            );

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
                    ->setDescription('The nickname uniquely identifies the user in the site\'s public spaces')
                    ->setErrorMessage('This nickname name is already in use. Please select another one.')
            );

        $businessBundle->addBusinessModel($businessModel);

        $businessModel = new BusinessModel('UserStats', 'Simple statistics about the user');
        $businessModel
            ->addField(new Field('firstLogin', FieldType::DATETIME, 'First time the user logged in the system'))
            ->addField(new Field('lastLogin', FieldType::DATETIME, 'Last time the user logged in the system'))
            ->addField(new Field('loginCount', FieldType::LONG, 'Number of time the user logged in the system'));

        $businessBundle->addBusinessModel($businessModel);

        $businessModel = new BusinessModel('Metadata', 'Variable set of extra information');
        $businessModel
            ->addField(
                (new Field('name', FieldType::STRING, 'The name to uniquely identify this metadata'))
                    ->setMandatory(true)
            )
            ->addField(
                (new Field('value', FieldType::TEXT, 'The value associated with this metadata'))
                    ->setMandatory(true)
            );

        $businessBundle->addBusinessModel($businessModel);

        $businessModel = new BusinessModel('Post', 'Post created by the user');
        $businessModel
            ->addField(
                (new Field('id', FieldType::UUID, "The post's unique ID in our system"))
                    ->setMandatory(true)
                    ->setManaged(true)
                    ->setExample('deadbeef-1164-4289-98b4-65706837c4d7')
                    ->setIdentifier(true)
            )
            ->addField(new Field('title', FieldType::STRING, 'The post title', true))
            ->addField(new Field('body', FieldType::TEXT, 'The post body', true))
            ->addField(new Field('scheduled', FieldType::DATETIME, 'The time at which this post must be published', false))
            ->addField(new Field('author', 'User', 'The user who created this post', true))
            ->addField(new Field('topic', 'Topic', 'The topic to which this post belongs', true))
            ->addField(
                (new Field('created', FieldType::DATETIME, 'Time at which the post was created'))
                    ->setManaged(true)
            )
            ->addField(
                (new Field('updated', FieldType::DATETIME, 'Last time at which the post was updated'))
                    ->setManaged(true)
            );

        $businessBundle->addBusinessModel($businessModel);

        $businessModel = new  BusinessModel('Topic', 'A topic regroups a set of posts made by various authors');
        $businessModel
            ->addField(new Field('title', FieldType::STRING, 'The topic title', true))
            ->addField(new Field('description', FieldType::TEXT, 'The topic description'))
            ->addField(
                (new Field('authors', 'User', 'List of authors who are allowed to post on this topic'))
                    ->setList(true)
            )
            ->addField(
                (new Field('posts', 'Post', 'List of posts published on this topic'))
                    ->setList(true)
            )
            ->addField(
                (new Field('created', FieldType::DATETIME, 'Time at which the post was created'))
                    ->setManaged(true)
            )
            ->addField(
                (new Field('updated', FieldType::DATETIME, 'Last time at which the post was updated'))
                    ->setManaged(true)
            );

        $businessBundle->addBusinessModel($businessModel);

        $businessModel = new BusinessModel('Subscription', 'The subscription bought by a user to user our services');
        $businessModel
            ->addField(new Field('user', 'User', 'The user to which this subscription belongs', true))
            ->addField(new Field('plan', FieldType::STRING, 'The plan subscribed by this user in our billing system', true))
            ->addField(new Field('renewal', FieldType::DATE, 'The date at which the subscription must be renewed', true))
            ->addField(
                (new Field('created', FieldType::DATETIME, 'Time at which the post was created'))
                    ->setManaged(true)
            )
            ->addField(
                (new Field('updated', FieldType::DATETIME, 'Last time at which the post was updated'))
                    ->setManaged(true)
            );

        $businessBundle->addBusinessModel($businessModel);
    }

    public static function addSampleBusinessProcesses(BusinessBundle $businessBundle)
    {
        $dataBundleHelper = new DataBundleHelper($businessBundle);
        $user = $businessBundle->getBusinessModel('User');
        $post = $businessBundle->getBusinessModel('Post');

        // --------------------------------------------------------
        // Process 1: Add a simple synchronous process without data
        // --------------------------------------------------------
        $event = new Event('Simple Event');
        $businessProcess = new BusinessProcess('Synchronous Process No Data', 'This is a sample synchronous process that does not require any data as input', $event);
        $businessBundle->addBusinessProcess($businessProcess);

        // --------------------------------------------------------
        // Process 2: Simulate a login process
        // --------------------------------------------------------
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

        $businessBundle->addBusinessProcess($businessProcess);

        // --------------------------------------------------------
        // Process 3: Simulate a register process
        // --------------------------------------------------------
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

        $businessBundle->addBusinessProcess($businessProcess);

        // --------------------------------------------------------
        // Process 4: Schedule a post
        // --------------------------------------------------------
        // 1. Define the input data required for this process
        $eventBundle = new EventDataBundle();
        $dataBundleHelper->addFieldsAsMandatory($eventBundle, $post, ['id', 'scheduled']);

        // 2. Define the event that will trigger this process
        $event = new Event(
            'Schedule Post',
            'Event triggered when user wants to schedule a post at a given time');
        $event->addDataBundle($eventBundle);

        // 3. Create the Business Process
        $businessProcess = new BusinessProcess(
            'Schedule Publication',
            'This process is triggered when a user wants to publish a specific post at at given time.',
            $event);

        // Set the process attributes
        $businessProcess
            ->setCategory('Posts')
            ->setType(ProcessType::UPDATE)
            ->setExternalAccess(true);

        // 4. Define the bundle of data required by this process
        // N/A

        // 5. Define the bundle of data produced/updated by this process
        $internalBundle = new InternalDataBundle();
        $internalBundle->setDescription('Post fields to update in the database');
        $dataBundleHelper->addFields($internalBundle, $post, ['scheduled']);
        $businessProcess->addProducedData($contextBundle);

        // 6. Publish a message to trigger other processes
        // N/A

        $businessBundle->addBusinessProcess($businessProcess);
    }
}
