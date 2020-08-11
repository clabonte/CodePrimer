<?php

use CodePrimer\Helper\DataBundleHelper;
use CodePrimer\Model\BusinessBundle;
use CodePrimer\Model\BusinessProcess;
use CodePrimer\Model\Data\ContextDataBundle;
use CodePrimer\Model\Data\Data;
use CodePrimer\Model\Data\DataBundle;
use CodePrimer\Model\Data\EventDataBundle;
use CodePrimer\Model\Data\InternalDataBundle;
use CodePrimer\Model\Derived\Event;
use CodePrimer\Model\Derived\Message;

/**
 * Factory used to create the various BusinessProcesses instances needed in our Channel sample application.
 */
class BusinessProcessFactory
{
    /** @var DataBundleHelper */
    private $dataBundleHelper;

    public function __construct()
    {
        $this->dataBundleHelper = new DataBundleHelper();
    }

    public function createNewArticleProcess(BusinessBundle $businessBundle): BusinessProcess
    {
        $article = $businessBundle->getBusinessModel('Article');
        $user = $businessBundle->getBusinessModel('User');

        // 1. Define the input data required for this process
        //    - Mandatory:
        //      - title
        //      - body
        //      - topic: reference
        //    - Optional:
        //      - description
        //      - labels: full
        $inputBundle = new EventDataBundle();
        $this->dataBundleHelper->addFieldsAsMandatory($inputBundle, $article, ['title', 'body', 'topic'], Data::REFERENCE);
        $this->dataBundleHelper->addFieldsAsOptional($inputBundle, $article, ['description', 'labels'], Data::FULL);

        // 2. Define the event that will be used as a trigger for this process
        $event = new Event(
            'New Article',
            'Event triggered when a new article is created by an author');
        $event->addDataBundle($inputBundle);

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
            ->setType(BusinessProcess::CREATE)
            ->setExternalAccess(true)
            ->addRole(ChannelApp::AUTHOR);

        // 4. Define the bundle of data required by this process
        $contextData = new ContextDataBundle();
        $contextData->setDescription("Set the article's author based on the user who triggered the event.");
        $this->dataBundleHelper->addFields($contextData, $user, ['id']);
        $businessProcess->addRequiredData($contextData);

        // 5. Define the bundle of data produced by this process
        $internalData = new InternalDataBundle();
        $internalData->setDescription('Save the new article internally');
        $this->dataBundleHelper->addBusinessModel($internalData, $article, Data::REFERENCE);
        $businessProcess->addProducedData($internalData);

        // 6. Publish 'article.new' message
        $msgBundle = new DataBundle();
        $this->dataBundleHelper->addBusinessModelExceptFields($msgBundle, $article, ['views'], Data::FULL);
        $message = new Message('article.new');
        $message->addDataBundle($msgBundle);
        $businessProcess->addMessage($message);

        return $businessProcess;
    }
}
