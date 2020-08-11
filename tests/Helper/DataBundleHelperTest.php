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

    /** @var InternalDataBundle */
    private $dataBundle;

    /** @var BusinessBundle */
    private $businessBundle;

    public function setUp(): void
    {
        parent::setUp();
        $this->eventDataBundle = new EventDataBundle('TestEventBundle', 'Test Description');
        $this->dataBundle = new InternalDataBundle('TestExistingBundle', 'Test Description');
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
        $this->helper->addBusinessModel($this->dataBundle, $user, 'unknown');
    }

    public function testAddFieldsAddsAllFieldsProperly()
    {
        $user = $this->businessBundle->getBusinessModel('User');
        $this->assertEmptyDataBundle($this->dataBundle);

        $this->helper->addFields($this->dataBundle, $user, $user->getFields());
        self::assertCount(1, $this->dataBundle->listBusinessModelNames());
        self::assertTrue($this->dataBundle->isBusinessModelPresent('User'));
        $list = $this->dataBundle->listData('User');
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
        $this->helper->addFields($this->dataBundle, $user, $user->getFields(), 'unknown');
    }

    private function assertEmptyDataBundle($dataBundle)
    {
        self::assertEmpty($dataBundle->listBusinessModelNames());
        self::assertEmpty($dataBundle->getData());
    }
}
