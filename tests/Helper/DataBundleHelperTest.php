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
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class DataBundleHelperTest extends TestCase
{
    /** @var DataBundleHelper */
    private $helper;

    /** @var EventDataBundle */
    private $eventDataBundle;

    /** @var ContextDataBundle */
    private $contextDataBundle;

    /** @var InternalDataBundle */
    private $internalDataBundle;

    /** @var ExternalDataBundle */
    private $externalDataBundle;

    /** @var BusinessBundle */
    private $businessBundle;

    public function setUp(): void
    {
        parent::setUp();
        $this->eventDataBundle = new EventDataBundle('TestEventBundle', 'Test Event Bundle Description');
        $this->contextDataBundle = new ContextDataBundle('TestContextBundle', 'Test Context Bundle Description');
        $this->internalDataBundle = new InternalDataBundle('TestInternalBundle', 'Test Internal Bundle Description');
        $this->externalDataBundle = new ExternalDataBundle('TestExternalBundle', 'Test External Bundle Description');
        $this->helper = new DataBundleHelper();
        $this->businessBundle = TestHelper::getSampleBusinessBundle();
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
        self::assertCount(11, $list);

        // Validate that each data has been properly created
        foreach ($list as $data) {
            self::assertInstanceOf(EventData::class, $data);
            self::assertEquals(Data::BASIC, $data->getDetails());
            self::assertEquals($data->getField()->isMandatory(), $data->isMandatory());
        }

        // Adding the same model with different attributes overrides the existing data
        $this->helper->addBusinessModel($this->eventDataBundle, $user, Data::REFERENCE);
        self::assertCount(1, $this->eventDataBundle->listBusinessModelNames());
        self::assertTrue($this->eventDataBundle->isBusinessModelPresent('User'));
        $list = $this->eventDataBundle->listData('User');
        self::assertCount(11, $list);

        // Validate that each data has been properly created
        $fieldHelper = new FieldHelper();
        foreach ($list as $data) {
            self::assertInstanceOf(EventData::class, $data);
            if ($fieldHelper->isNativeType($data->getField())) {
                self::assertEquals(Data::BASIC, $data->getDetails());
            } else {
                self::assertEquals(Data::REFERENCE, $data->getDetails());
            }
            self::assertEquals($data->getField()->isMandatory(), $data->isMandatory());
        }
    }

    public function testAddBusinessModelOnEventDataBundleWithInvalidArgumentThrowsException()
    {
        self::expectException(InvalidArgumentException::class);
        $user = $this->businessBundle->getBusinessModel('User');
        $this->helper->addBusinessModel($this->eventDataBundle, $user, 'unknown');
    }

    public function testAddFieldsAsMandatoryAddsAllFieldsProperly()
    {
        $user = $this->businessBundle->getBusinessModel('User');
        $this->assertEmptyDataBundle($this->eventDataBundle);

        $this->helper->addFieldsAsMandatory($this->eventDataBundle, $user, $user->getFields());
        self::assertCount(1, $this->eventDataBundle->listBusinessModelNames());
        self::assertTrue($this->eventDataBundle->isBusinessModelPresent('User'));
        $list = $this->eventDataBundle->listData('User');
        self::assertCount(15, $list);

        // Validate that each data has been properly created
        foreach ($list as $data) {
            self::assertInstanceOf(EventData::class, $data);
            self::assertEquals(Data::BASIC, $data->getDetails());
            self::assertTrue($data->isMandatory());
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
        self::assertEquals(Data::BASIC, $data->getDetails());
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
        self::assertCount(9, $list);

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
        self::assertCount(13, $list);

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
        self::assertCount(15, $list);

        // Validate that each data has been properly created
        foreach ($list as $data) {
            self::assertInstanceOf(EventData::class, $data);
            self::assertEquals(Data::BASIC, $data->getDetails());
            self::assertEquals($data->getField()->isMandatory(), $data->isMandatory());
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
        self::assertCount(15, $list);

        // Validate that each data has been properly created
        foreach ($list as $data) {
            self::assertInstanceOf(EventData::class, $data);
            self::assertEquals(Data::BASIC, $data->getDetails());
            self::assertFalse($data->isMandatory());
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
        self::assertCount(15, $list);

        // Validate that each data has been properly created
        foreach ($list as $data) {
            self::assertInstanceOf(Data::class, $data);
            self::assertEquals(Data::BASIC, $data->getDetails());
        }

        // Adding the same model with different attributes overrides the existing data
        $this->helper->addBusinessModel($dataBundle, $user, Data::REFERENCE);
        self::assertCount(1, $dataBundle->listBusinessModelNames());
        self::assertTrue($dataBundle->isBusinessModelPresent('User'));
        $list = $dataBundle->listData('User');
        self::assertCount(15, $list);

        // Validate that each data has been properly created
        $fieldHelper = new FieldHelper();
        foreach ($list as $data) {
            self::assertInstanceOf(Data::class, $data);
            if ($fieldHelper->isNativeType($data->getField())) {
                self::assertEquals(Data::BASIC, $data->getDetails());
            } else {
                self::assertEquals(Data::REFERENCE, $data->getDetails());
            }
        }
    }

    public function dataBundleProvider()
    {
        return [
            'DataBundle' => [new DataBundle()],
            'ContextDataBundle' => [new ContextDataBundle()],
            'InternalDataBundle' => [new InternalDataBundle()],
            'ExternalDataBundle' => [new ExternalDataBundle()],
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
        self::assertCount(15, $list);

        // Validate that each data has been properly created
        foreach ($list as $data) {
            self::assertInstanceOf(Data::class, $data);
            self::assertEquals(Data::BASIC, $data->getDetails());
        }
    }

    public function testAddFieldsWithInvalidArgumentThrowsException()
    {
        self::expectException(InvalidArgumentException::class);
        $user = $this->businessBundle->getBusinessModel('User');
        $this->helper->addFields($this->internalDataBundle, $user, $user->getFields(), 'unknown');
    }

    private function assertEmptyDataBundle($dataBundle)
    {
        self::assertEmpty($dataBundle->listBusinessModelNames());
        self::assertEmpty($dataBundle->getData());
    }
}
