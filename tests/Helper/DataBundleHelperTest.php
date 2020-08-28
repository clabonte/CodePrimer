<?php

namespace CodePrimer\Tests\Helper;

use CodePrimer\Helper\DataBundleHelper;
use CodePrimer\Helper\FieldHelper;
use CodePrimer\Model\BusinessBundle;
use CodePrimer\Model\Data\ContextDataBundle;
use CodePrimer\Model\Data\Data;
use CodePrimer\Model\Data\DataBundle;
use CodePrimer\Model\Data\EventData;
use CodePrimer\Model\Data\EventDataBundle;
use CodePrimer\Model\Data\ExternalDataBundle;
use CodePrimer\Model\Data\InternalDataBundle;
use CodePrimer\Model\Data\MessageDataBundle;
use CodePrimer\Model\Data\ReturnedDataBundle;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class DataBundleHelperTest extends TestCase
{
    /** @var DataBundleHelper */
    private $helper;

    /** @var FieldHelper */
    private $fieldHelper;

    /** @var EventDataBundle */
    private $eventDataBundle;

    /** @var ContextDataBundle */
    private $contextDataBundle;

    /** @var InternalDataBundle */
    private $internalDataBundle;

    /** @var ExternalDataBundle */
    private $externalDataBundle;

    /** @var ReturnedDataBundle */
    private $returnedDataBundle;

    /** @var MessageDataBundle */
    private $messageDataBundle;

    /** @var BusinessBundle */
    private $businessBundle;

    public function setUp(): void
    {
        parent::setUp();
        $this->eventDataBundle = new EventDataBundle('TestEventBundle', 'Test Event Bundle Description');
        $this->contextDataBundle = new ContextDataBundle('TestContextBundle', 'Test Context Bundle Description');
        $this->internalDataBundle = new InternalDataBundle('TestInternalBundle', 'Test Internal Bundle Description');
        $this->externalDataBundle = new ExternalDataBundle('TestExternalBundle', 'Test External Bundle Description');
        $this->returnedDataBundle = new ReturnedDataBundle('TestReturnedBundle', 'Test Returned Bundle Description');
        $this->messageDataBundle = new MessageDataBundle('TestMessageBundle', 'Test Message Bundle Description');
        $this->businessBundle = TestHelper::getSampleBusinessBundle();
        $this->helper = new DataBundleHelper($this->businessBundle);
        $this->fieldHelper = new FieldHelper();
    }

    public function testAddBusinessModelOnEventDataBundleShouldOnlyAddUnmanagedFields()
    {
        $user = $this->businessBundle->getBusinessModel('User');
        $this->assertEmptyDataBundle($this->eventDataBundle);
        self::assertFalse($this->eventDataBundle->isBusinessModelPresent('User'));
        self::assertEmpty($this->eventDataBundle->listData('User'));

        $this->helper->addBusinessModel($this->eventDataBundle, $user);
        self::assertCount(1, $this->eventDataBundle->listBusinessModelNames());
        self::assertTrue($this->eventDataBundle->isBusinessModelPresent('User'));
        $list = $this->eventDataBundle->listData('User');
        self::assertCount(12, $list);

        // Validate that each data has been properly created
        foreach ($list as $data) {
            $this->assertData(EventData::class, $data, $data->getField()->isMandatory());
        }

        // Adding the same model with different attributes overrides the existing data
        $this->helper->addBusinessModel($this->eventDataBundle, $user, Data::REFERENCE);
        self::assertCount(1, $this->eventDataBundle->listBusinessModelNames());
        self::assertTrue($this->eventDataBundle->isBusinessModelPresent('User'));
        $list = $this->eventDataBundle->listData('User');
        self::assertCount(12, $list);

        // Validate that each data has been properly created
        foreach ($list as $data) {
            $this->assertData(EventData::class, $data, $data->getField()->isMandatory());
        }
    }

    public function testAddBusinessModelOnEventDataBundleWithInvalidArgumentThrowsException()
    {
        self::expectException(InvalidArgumentException::class);
        $user = $this->businessBundle->getBusinessModel('User');
        $this->helper->addBusinessModel($this->eventDataBundle, $user, 'unknown');
    }

    public function testAddBusinessModelAttributesOnEventDataBundleShouldOnlyAddUnmanagedAttributes()
    {
        $user = $this->businessBundle->getBusinessModel('User');
        $this->assertEmptyDataBundle($this->eventDataBundle);
        self::assertFalse($this->eventDataBundle->isBusinessModelPresent('User'));
        self::assertEmpty($this->eventDataBundle->listData('User'));

        $this->helper->addBusinessModelAttributes($this->eventDataBundle, $user, true);
        self::assertCount(1, $this->eventDataBundle->listBusinessModelNames());
        self::assertTrue($this->eventDataBundle->isBusinessModelPresent('User'));
        $list = $this->eventDataBundle->listData('User');
        self::assertCount(7, $list);

        // Validate that each data has been properly created
        foreach ($list as $data) {
            $this->assertData(EventData::class, $data, $data->getField()->isMandatory());
            self::assertFalse($this->fieldHelper->isBusinessModel($data->getField(), $this->businessBundle));
            self::assertFalse($data->getField()->isManaged());
        }
    }

    public function testAddFieldsAsMandatoryAddsAllFieldsProperly()
    {
        $user = $this->businessBundle->getBusinessModel('User');
        $this->assertEmptyDataBundle($this->eventDataBundle);

        $this->helper->addFieldsAsMandatory($this->eventDataBundle, $user, $user->getFields());
        self::assertCount(1, $this->eventDataBundle->listBusinessModelNames());
        self::assertTrue($this->eventDataBundle->isBusinessModelPresent('User'));
        $list = $this->eventDataBundle->listData('User');
        self::assertCount(16, $list);

        // Validate that each data has been properly created
        foreach ($list as $data) {
            $this->assertData(EventData::class, $data, true);
        }
    }

    public function testAddStringFieldsAsMandatoryAddsAllFieldsProperly()
    {
        $user = $this->businessBundle->getBusinessModel('User');
        $this->assertEmptyDataBundle($this->eventDataBundle);

        $this->helper->addFieldsAsMandatory($this->eventDataBundle, $user, ['crmId', 'stats'], Data::REFERENCE);
        self::assertCount(1, $this->eventDataBundle->listBusinessModelNames());
        self::assertTrue($this->eventDataBundle->isBusinessModelPresent('User'));
        $list = $this->eventDataBundle->listData('User');
        self::assertCount(2, $list);

        // Validate that each data has been properly created
        self::assertArrayHasKey('crmId', $list);
        $data = $list['crmId'];
        self::assertInstanceOf(EventData::class, $data);
        self::assertEquals(Data::ATTRIBUTES, $data->getDetails());
        self::assertTrue($data->isMandatory());

        self::assertArrayHasKey('stats', $list);
        $data = $list['stats'];
        self::assertInstanceOf(EventData::class, $data);
        self::assertEquals(Data::REFERENCE, $data->getDetails());
        self::assertTrue($data->isMandatory());
    }

    public function testAddBusinessModelExceptFieldsAddsTheRightFields()
    {
        $user = $this->businessBundle->getBusinessModel('User');
        $this->assertEmptyDataBundle($this->eventDataBundle);

        $this->helper->addBusinessModelExceptFields($this->eventDataBundle, $user, [$user->getField('crmId'), $user->getField('stats')], Data::REFERENCE);
        self::assertCount(1, $this->eventDataBundle->listBusinessModelNames());
        self::assertTrue($this->eventDataBundle->isBusinessModelPresent('User'));
        $list = $this->eventDataBundle->listData('User');
        self::assertCount(10, $list);

        self::assertArrayNotHasKey('crmId', $list);
        self::assertArrayNotHasKey('stats', $list);
    }

    public function testAddBusinessModelExceptStringFieldsAddsTheRightFields()
    {
        $user = $this->businessBundle->getBusinessModel('User');
        $this->assertEmptyDataBundle($this->internalDataBundle);

        $this->helper->addBusinessModelExceptFields($this->internalDataBundle, $user, ['crmId', 'stats'], Data::REFERENCE);
        self::assertCount(1, $this->internalDataBundle->listBusinessModelNames());
        self::assertTrue($this->internalDataBundle->isBusinessModelPresent('User'));
        $list = $this->internalDataBundle->listData('User');
        self::assertCount(14, $list);

        self::assertArrayNotHasKey('crmId', $list);
        self::assertArrayNotHasKey('stats', $list);
    }

    public function testAddFieldsInEventDataBundleConvertsDataToEventData()
    {
        $user = $this->businessBundle->getBusinessModel('User');
        $this->assertEmptyDataBundle($this->eventDataBundle);

        $this->helper->addFields($this->eventDataBundle, $user, $user->getFields());
        self::assertCount(1, $this->eventDataBundle->listBusinessModelNames());
        self::assertTrue($this->eventDataBundle->isBusinessModelPresent('User'));
        $list = $this->eventDataBundle->listData('User');
        self::assertCount(16, $list);

        // Validate that each data has been properly created
        foreach ($list as $data) {
            $this->assertData(EventData::class, $data, $data->getField()->isMandatory());
        }
    }

    public function testAddFieldsAsMandatoryWithInvalidArgumentThrowsException()
    {
        self::expectException(InvalidArgumentException::class);
        $user = $this->businessBundle->getBusinessModel('User');
        $this->helper->addFieldsAsMandatory($this->eventDataBundle, $user, $user->getFields(), 'unknown');
    }

    public function testAddFieldsAsOptionalAddsAllFieldsProperly()
    {
        $user = $this->businessBundle->getBusinessModel('User');
        $this->assertEmptyDataBundle($this->eventDataBundle);

        $this->helper->addFieldsAsOptional($this->eventDataBundle, $user, $user->getFields());
        self::assertCount(1, $this->eventDataBundle->listBusinessModelNames());
        self::assertTrue($this->eventDataBundle->isBusinessModelPresent('User'));
        $list = $this->eventDataBundle->listData('User');
        self::assertCount(16, $list);

        // Validate that each data has been properly created
        foreach ($list as $data) {
            $this->assertData(EventData::class, $data, false);
        }
    }

    public function testAddFieldsAsOptionalWithInvalidArgumentThrowsException()
    {
        self::expectException(InvalidArgumentException::class);
        $user = $this->businessBundle->getBusinessModel('User');
        $this->helper->addFieldsAsOptional($this->eventDataBundle, $user, $user->getFields(), 'unknown');
    }

    /**
     * @dataProvider dataBundleProvider
     */
    public function testAddBusinessModelShouldAddAllFields($dataBundle)
    {
        $user = $this->businessBundle->getBusinessModel('User');
        $this->assertEmptyDataBundle($dataBundle);
        self::assertFalse($dataBundle->isBusinessModelPresent('User'));
        self::assertEmpty($dataBundle->listData('User'));

        $this->helper->addBusinessModel($dataBundle, $user);
        self::assertCount(1, $dataBundle->listBusinessModelNames());
        self::assertTrue($dataBundle->isBusinessModelPresent('User'));
        $list = $dataBundle->listData('User');
        self::assertCount(16, $list);

        // Validate that each data has been properly created
        foreach ($list as $data) {
            $this->assertData(Data::class, $data);
        }

        // Adding the same model with different attributes overrides the existing data
        $this->helper->addBusinessModel($dataBundle, $user, Data::REFERENCE);
        self::assertCount(1, $dataBundle->listBusinessModelNames());
        self::assertTrue($dataBundle->isBusinessModelPresent('User'));
        $list = $dataBundle->listData('User');
        self::assertCount(16, $list);

        // Validate that each data has been properly created
        foreach ($list as $data) {
            $this->assertData(Data::class, $data);
        }
    }

    /**
     * @dataProvider dataBundleProvider
     */
    public function testAddBusinessModelAttributesOnDataBundleShouldOnlyAddUnmanagedAttributes($dataBundle)
    {
        $user = $this->businessBundle->getBusinessModel('User');
        $this->assertEmptyDataBundle($dataBundle);
        self::assertFalse($dataBundle->isBusinessModelPresent('User'));
        self::assertEmpty($dataBundle->listData('User'));

        $this->helper->addBusinessModelAttributes($dataBundle, $user);
        self::assertCount(1, $dataBundle->listBusinessModelNames());
        self::assertTrue($dataBundle->isBusinessModelPresent('User'));
        $list = $dataBundle->listData('User');
        self::assertCount(7, $list);

        // Validate that each data has been properly created
        foreach ($list as $data) {
            $this->assertData(Data::class, $data);
            self::assertEquals(Data::class, get_class($data));
            self::assertFalse($this->fieldHelper->isBusinessModel($data->getField(), $this->businessBundle));
            self::assertFalse($data->getField()->isManaged());
        }
    }

    /**
     * @dataProvider dataBundleProvider
     */
    public function testAddBusinessModelAttributesOnDataBundleShouldOnlyAddManagedAttributes($dataBundle)
    {
        $user = $this->businessBundle->getBusinessModel('User');
        $this->assertEmptyDataBundle($dataBundle);
        self::assertFalse($dataBundle->isBusinessModelPresent('User'));
        self::assertEmpty($dataBundle->listData('User'));

        $this->helper->addBusinessModelAttributes($dataBundle, $user, true);
        self::assertCount(1, $dataBundle->listBusinessModelNames());
        self::assertTrue($dataBundle->isBusinessModelPresent('User'));
        $list = $dataBundle->listData('User');
        self::assertCount(11, $list);

        // Validate that each data has been properly created
        foreach ($list as $data) {
            $this->assertData(Data::class, $data);
            self::assertEquals(Data::class, get_class($data));
            self::assertFalse($this->fieldHelper->isBusinessModel($data->getField(), $this->businessBundle));
        }
    }

    public function dataBundleProvider()
    {
        return [
            'MessageDataBundle' => [new MessageDataBundle('Test MessageDataBundle', 'Test MessageDataBundle description')],
            'ContextDataBundle' => [new ContextDataBundle('Test ContextDataBundle', 'Test ContextDataBundle description')],
            'InternalDataBundle' => [new InternalDataBundle('Test InternalDataBundle', 'Test InternalDataBundle description')],
            'ExternalDataBundle' => [new ExternalDataBundle('Test ExternalDataBundle', 'Test ExternalDataBundle description')],
        ];
    }

    public function testAddBusinessModelWithInvalidArgumentThrowsException()
    {
        self::expectException(InvalidArgumentException::class);
        $user = $this->businessBundle->getBusinessModel('User');
        $this->helper->addBusinessModel($this->internalDataBundle, $user, 'unknown');
    }

    public function testAddFieldsAddsAllFieldsProperly()
    {
        $user = $this->businessBundle->getBusinessModel('User');
        $this->assertEmptyDataBundle($this->internalDataBundle);

        $this->helper->addFields($this->internalDataBundle, $user, $user->getFields());
        self::assertCount(1, $this->internalDataBundle->listBusinessModelNames());
        self::assertTrue($this->internalDataBundle->isBusinessModelPresent('User'));
        $list = $this->internalDataBundle->listData('User');
        self::assertCount(16, $list);

        // Validate that each data has been properly created
        foreach ($list as $data) {
            $this->assertData(Data::class, $data);
        }
    }

    public function testAddFieldsWithInvalidArgumentThrowsException()
    {
        self::expectException(InvalidArgumentException::class);
        $user = $this->businessBundle->getBusinessModel('User');
        $this->helper->addFields($this->internalDataBundle, $user, $user->getFields(), 'unknown');
    }

    /**
     * @dataProvider allDataBundleVariantsProvider
     */
    public function testCreateMessageDataBundleFromExistingShouldWork(DataBundle $existingDataBundle)
    {
        $dataBundle = $this->helper->createMessageDataBundleFromExisting($existingDataBundle);

        $this->assertCopiedDataBundle($existingDataBundle, $dataBundle);
    }

    /**
     * @dataProvider allDataBundleVariantsProvider
     */
    public function testCreateReturnedDataBundleFromExistingShouldWork(DataBundle $existingDataBundle)
    {
        $dataBundle = $this->helper->createReturnedDataBundleFromExisting($existingDataBundle);

        $this->assertCopiedDataBundle($existingDataBundle, $dataBundle);
    }

    /**
     * @dataProvider allDataBundleVariantsProvider
     */
    public function testCreateInternalDataBundleFromExistingShouldWork(DataBundle $existingDataBundle)
    {
        $dataBundle = $this->helper->createInternalDataBundleFromExisting($existingDataBundle);

        $this->assertCopiedDataBundle($existingDataBundle, $dataBundle);
    }

    /**
     * @dataProvider allDataBundleVariantsProvider
     */
    public function testCreateExternalDataBundleFromExistingShouldWork(DataBundle $existingDataBundle)
    {
        $dataBundle = $this->helper->createExternalDataBundleFromExisting($existingDataBundle);

        $this->assertCopiedDataBundle($existingDataBundle, $dataBundle);
    }

    /**
     * @dataProvider allDataBundleVariantsProvider
     */
    public function testCreateContextDataBundleFromExistingShouldWork(DataBundle $existingDataBundle)
    {
        $dataBundle = $this->helper->createContextDataBundleFromExisting($existingDataBundle);

        $this->assertCopiedDataBundle($existingDataBundle, $dataBundle);
    }

    public function allDataBundleVariantsProvider()
    {
        $businessBundle = TestHelper::getSampleBusinessBundle();
        $user = $businessBundle->getBusinessModel('User');

        return [
            'MessageDataBundle' => [
                (new MessageDataBundle('Test MessageDataBundle', 'Test MessageDataBundle description'))
                    ->add(new Data($user, $user->getField('firstName')))
                    ->add(new Data($user, $user->getField('lastName')))
                    ->add(new Data($user, $user->getField('topics'), Data::REFERENCE))
                    ->add(new Data($user, $user->getField('stats'), Data::FULL)),
            ],
            'ContextDataBundle' => [
                (new ContextDataBundle('Test ContextDataBundle', 'Test ContextDataBundle description'))
                    ->add(new Data($user, $user->getField('firstName')))
                    ->add(new Data($user, $user->getField('lastName')))
                    ->add(new Data($user, $user->getField('topics'), Data::REFERENCE))
                    ->add(new Data($user, $user->getField('stats'), Data::FULL))
                    ->setAsListStructure(),
            ],
            'InternalDataBundle' => [
                (new InternalDataBundle('Test InternalDataBundle', 'Test InternalDataBundle description'))
                    ->add(new Data($user, $user->getField('firstName')))
                    ->add(new Data($user, $user->getField('lastName')))
                    ->add(new Data($user, $user->getField('topics'), Data::REFERENCE))
                    ->add(new Data($user, $user->getField('stats'), Data::FULL))
                    ->setAsSimpleStructure(),
            ],
            'ExternalDataBundle' => [
                (new ExternalDataBundle('Test ExternalDataBundle', 'Test ExternalDataBundle description'))
                    ->add(new Data($user, $user->getField('firstName')))
                    ->add(new Data($user, $user->getField('lastName')))
                    ->add(new Data($user, $user->getField('topics'), Data::REFERENCE))
                    ->add(new Data($user, $user->getField('stats'), Data::FULL)),
            ],
            'EventDataBundle' => [
                (new EventDataBundle('Test EventDataBundle', 'Test EventDataBundle description'))
                    ->add(new EventData($user, $user->getField('firstName'), true))
                    ->add(new EventData($user, $user->getField('lastName'), true))
                    ->add(new EventData($user, $user->getField('topics'), false, Data::REFERENCE))
                    ->add(new EventData($user, $user->getField('stats'), true, Data::FULL)),
            ],
            'ReturnedDataBundle' => [
                (new ReturnedDataBundle('Test ReturnedDataBundle', 'Test ReturnedDataBundle description'))
                    ->add(new Data($user, $user->getField('firstName')))
                    ->add(new Data($user, $user->getField('lastName')))
                    ->add(new Data($user, $user->getField('topics'), Data::REFERENCE))
                    ->add(new Data($user, $user->getField('stats'), Data::FULL)),
            ],
        ];
    }

    private function assertEmptyDataBundle($dataBundle)
    {
        self::assertEmpty($dataBundle->listBusinessModelNames());
        self::assertEmpty($dataBundle->getData());
    }

    private function assertCopiedDataBundle(DataBundle $existingDataBundle, DataBundle $dataBundle)
    {
        self::assertInstanceOf(DataBundle::class, $dataBundle);
        self::assertEquals($existingDataBundle->getName(), $dataBundle->getName());
        self::assertEquals($existingDataBundle->getDescription(), $dataBundle->getDescription());
        self::assertEquals($existingDataBundle->getStructure(), $dataBundle->getStructure());
        self::assertCount(count($existingDataBundle->getData()), $dataBundle->getData());
        self::assertEquals($existingDataBundle->listBusinessModelNames(), $dataBundle->listBusinessModelNames());

        // Make sure all data has been properly created
        foreach ($existingDataBundle->getData() as $key => $existingList) {
            self::assertArrayHasKey($key, $dataBundle->getData());
            self::assertTrue($dataBundle->isBusinessModelPresent($key));

            $list = $dataBundle->listData($key);
            self::assertCount(count($existingList), $list);

            foreach ($list as $data) {
                self::assertEquals(Data::class, get_class($data));
                self::assertTrue($this->isDataPresent($data, $existingList));
            }
        }
    }

    /**
     * @param Data[] $existingList
     */
    private function isDataPresent(Data $data, array $existingList): bool
    {
        foreach ($existingList as $existingData) {
            if ($data->isSame($existingData)) {
                return true;
            }
        }

        return false;
    }

    private function assertData(string $class, Data $data, bool $mandatory = null)
    {
        self::assertInstanceOf($class, $data);
        if ($this->fieldHelper->isNativeType($data->getField())) {
            self::assertEquals(Data::ATTRIBUTES, $data->getDetails());
        } else {
            self::assertEquals(Data::REFERENCE, $data->getDetails());
        }
        if ($data instanceof EventData) {
            self::assertEquals($mandatory, $data->isMandatory());
        }
    }
}
