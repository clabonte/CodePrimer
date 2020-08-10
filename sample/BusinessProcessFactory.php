<?php

use CodePrimer\Helper\DataBundleHelper;
use CodePrimer\Model\BusinessBundle;
use CodePrimer\Model\BusinessProcess;
use CodePrimer\Model\Data\Data;
use CodePrimer\Model\Data\ExistingData;
use CodePrimer\Model\Data\ExistingDataBundle;
use CodePrimer\Model\Data\InputDataBundle;
use CodePrimer\Model\Derived\Event;
use CodePrimer\Model\Derived\Message;


/**
 * Factory used to create the various BusinessProcesses instances needed in our Channel sample application
 */
class BusinessProcessFactory
{
    /** @var DataBundleHelper */
    private $dataBundleHelper;

    public function __construct()
    {
        $this->dataBundleHelper = new DataBundleHelper();
    }

    public function createCreateArticleProcess(BusinessBundle $businessBundle): BusinessProcess
    {
        // 1. Define the input data required for this process
        //    - Mandatory:
        //      - title
        //      - body
        //      - topic: reference
        //    - Optional:
        //      - description
        //      - labels
        $inputBundle = new InputDataBundle();

        $article = $businessBundle->getBusinessModel('Article');
        $this->dataBundleHelper->addFieldsAsMandatoryInput($inputBundle, $article, ['title', 'body']);
        $this->dataBundleHelper->addFieldsAsMandatoryInput($inputBundle, $article, ['topic'], Data::REFERENCE);
        $this->dataBundleHelper->addFieldsAsOptionalInput($inputBundle, $article, ['description', 'labels'], Data::FULL);

        // 2. Define the event that will be used as a trigger for this process
        $event = new Event(
            'New Article',
            'Event triggered when a new article is created by an author');
        $event->addDataBundle($inputBundle);

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
            ->addRole(ChannelApp::AUTHOR);

        // 4. Set the process outcomes
        /*
        //  - Insert in the database
        $dbBundle = new DataBundle();
        $this->dataBundleHelper->addBusinessModelAsInput($dbBundle, $article, InputData::NEW);
        $businessProcess->setInternalUpdates([$dbBundle]);
        */

        //  - Publish 'article.new' message
        $msgBundle = new ExistingDataBundle();
        $this->dataBundleHelper->addBusinessModelAsExisting($msgBundle, $article, ExistingData::DEFAULT_SOURCE, Data::FULL);
        $message = new Message('article.new');
        $message->addDataBundle($msgBundle);
        $businessProcess->setMessages([$message]);

        return $businessProcess;
    }

}