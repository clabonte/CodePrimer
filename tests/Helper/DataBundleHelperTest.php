<?php

namespace CodePrimer\Tests\Helper;

use CodePrimer\Helper\DataBundleHelper;
use CodePrimer\Model\BusinessBundle;
use CodePrimer\Model\Data\Data;
use CodePrimer\Model\Data\DataBundle;
use CodePrimer\Model\Data\ExistingData;
use CodePrimer\Model\Data\FetchDataBundle;
use CodePrimer\Model\Data\InputData;
use CodePrimer\Model\Data\InputDataBundle;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class DataBundleHelperTest extends TestCase
{
    /** @var DataBundleHelper */
    private $helper;

    /** @var DataBundle */
    private $dataBundle;

    /** @var BusinessBundle */
    private $businessBundle;

    public function setUp(): void
    {
        parent::setUp();
        $this->dataBundle = new DataBundle('TestBundle', 'Test Description');
        $this->helper = new DataBundleHelper();
        $this->businessBundle = TestHelper::getSampleBusinessBundle();
    }

    public function testAddBusinessModelAsInputShouldOnlyAddUnmanagedFields()
    {
        $user = $this->businessBundle->getBusinessModel('User');
        $this->assertEmptyDataBundle();
        self::assertFalse($this->dataBundle->isBusinessModelPresent('User'));
        self::assertEmpty($this->dataBundle->listData('User'));

        $this->helper->addBusinessModelAsInput($this->dataBundle, $user);
        self::assertCount(1, $this->dataBundle->listBusinessModelNames());
        self::assertTrue($this->dataBundle->isBusinessModelPresent('User'));
        $list = $this->dataBundle->listData('User');
        self::assertCount(11, $list);

        // Validate that each data has been properly created
        foreach ($list as $data) {
            self::assertInstanceOf(InputData::class, $data);
            self::assertEquals(Data::BASIC, $data->getDetails());
            self::assertEquals($data->getField()->isMandatory(), $data->isMandatory());
        }

        // Adding the same model with different attributes overrides the existing data
        $this->helper->addBusinessModelAsInput($this->dataBundle, $user, Data::REFERENCE);
        self::assertCount(1, $this->dataBundle->listBusinessModelNames());
        self::assertTrue($this->dataBundle->isBusinessModelPresent('User'));
        $list = $this->dataBundle->listData('User');
        self::assertCount(11, $list);

        // Validate that each data has been properly created
        foreach ($list as $data) {
            self::assertInstanceOf(InputData::class, $data);
            self::assertEquals(Data::REFERENCE, $data->getDetails());
            self::assertEquals($data->getField()->isMandatory(), $data->isMandatory());
        }
    }

    public function testAddBusinessModelAsInputWithInvalidArgumentThrowsException()
    {
        self::expectException(InvalidArgumentException::class);
        $user = $this->businessBundle->getBusinessModel('User');
        $this->helper->addBusinessModelAsInput($this->dataBundle, $user, 'unknown');
    }

    public function testAddFieldsAsMandatoryInputAddsAllFieldsProperly()
    {
        $user = $this->businessBundle->getBusinessModel('User');
        $this->assertEmptyDataBundle();

        $this->helper->addFieldsAsMandatoryInput($this->dataBundle, $user, $user->getFields());
        self::assertCount(1, $this->dataBundle->listBusinessModelNames());
        self::assertTrue($this->dataBundle->isBusinessModelPresent('User'));
        $list = $this->dataBundle->listData('User');
        self::assertCount(15, $list);

        // Validate that each data has been properly created
        foreach ($list as $data) {
            self::assertInstanceOf(InputData::class, $data);
            self::assertEquals(Data::BASIC, $data->getDetails());
            self::assertTrue($data->isMandatory());
        }
    }

    public function testAddFieldsAsMandatoryInputWithInvalidArgumentThrowsException()
    {
        self::expectException(InvalidArgumentException::class);
        $user = $this->businessBundle->getBusinessModel('User');
        $this->helper->addFieldsAsMandatoryInput($this->dataBundle, $user, $user->getFields(), 'unknown');
    }

    public function testAddFieldsAsOptionalInputAddsAllFieldsProperly()
    {
        $user = $this->businessBundle->getBusinessModel('User');
        $this->assertEmptyDataBundle();

        $this->helper->addFieldsAsOptionalInput($this->dataBundle, $user, $user->getFields());
        self::assertCount(1, $this->dataBundle->listBusinessModelNames());
        self::assertTrue($this->dataBundle->isBusinessModelPresent('User'));
        $list = $this->dataBundle->listData('User');
        self::assertCount(15, $list);

        // Validate that each data has been properly created
        foreach ($list as $data) {
            self::assertInstanceOf(InputData::class, $data);
            self::assertEquals(Data::BASIC, $data->getDetails());
            self::assertFalse($data->isMandatory());
        }
    }

    public function testAddFieldsAsOptionalInputWithInvalidArgumentThrowsException()
    {
        self::expectException(InvalidArgumentException::class);
        $user = $this->businessBundle->getBusinessModel('User');
        $this->helper->addFieldsAsOptionalInput($this->dataBundle, $user, $user->getFields(), 'unknown');
    }

    public function testAddBusinessModelAsExistingShouldAddAllFields()
    {
        $user = $this->businessBundle->getBusinessModel('User');
        $this->assertEmptyDataBundle();
        self::assertFalse($this->dataBundle->isBusinessModelPresent('User'));
        self::assertEmpty($this->dataBundle->listData('User'));

        $this->helper->addBusinessModelAsExisting($this->dataBundle, $user);
        self::assertCount(1, $this->dataBundle->listBusinessModelNames());
        self::assertTrue($this->dataBundle->isBusinessModelPresent('User'));
        $list = $this->dataBundle->listData('User');
        self::assertCount(15, $list);

        // Validate that each data has been properly created
        foreach ($list as $data) {
            self::assertInstanceOf(ExistingData::class, $data);
            self::assertEquals(Data::BASIC, $data->getDetails());
            self::assertEquals(ExistingData::DEFAULT_SOURCE, $data->getSource());
        }

        // Adding the same model with different attributes overrides the existing data
        $this->helper->addBusinessModelAsExisting($this->dataBundle, $user, 'Test Source 2', Data::REFERENCE);
        self::assertCount(1, $this->dataBundle->listBusinessModelNames());
        self::assertTrue($this->dataBundle->isBusinessModelPresent('User'));
        $list = $this->dataBundle->listData('User');
        self::assertCount(15, $list);

        // Validate that each data has been properly created
        foreach ($list as $data) {
            self::assertInstanceOf(ExistingData::class, $data);
            self::assertEquals(Data::REFERENCE, $data->getDetails());
            self::assertEquals('Test Source 2', $data->getSource());
        }
    }

    public function testAddBusinessModelAsExistingWithInvalidArgumentThrowsException()
    {
        self::expectException(InvalidArgumentException::class);
        $user = $this->businessBundle->getBusinessModel('User');
        $this->helper->addBusinessModelAsExisting($this->dataBundle, $user, '', 'unknown');
    }

    public function testAddFieldsAsExistingAddsAllFieldsProperly()
    {
        $user = $this->businessBundle->getBusinessModel('User');
        $this->assertEmptyDataBundle();

        $this->helper->addFieldsAsExisting($this->dataBundle, $user, $user->getFields());
        self::assertCount(1, $this->dataBundle->listBusinessModelNames());
        self::assertTrue($this->dataBundle->isBusinessModelPresent('User'));
        $list = $this->dataBundle->listData('User');
        self::assertCount(15, $list);

        // Validate that each data has been properly created
        foreach ($list as $data) {
            self::assertInstanceOf(ExistingData::class, $data);
            self::assertEquals(Data::BASIC, $data->getDetails());
            self::assertEquals(ExistingData::DEFAULT_SOURCE, $data->getSource());
        }
    }

    public function testAddFieldsAsExistingWithInvalidArgumentThrowsException()
    {
        self::expectException(InvalidArgumentException::class);
        $user = $this->businessBundle->getBusinessModel('User');
        $this->helper->addFieldsAsExisting($this->dataBundle, $user, $user->getFields(), '', 'unknown');
    }

    public function testAddingExistingDataToInputDataBundleThrowsException()
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('InputDataBundle only supports InputData arguments');

        $dataBundle = new InputDataBundle();
        $user = $this->businessBundle->getBusinessModel('User');
        $this->helper->addFieldsAsExisting($dataBundle, $user, $user->getFields());
    }

    public function testAddingInputDataToFetchDataBundleThrowsException()
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('FetchDataBundle only supports ExistingData arguments');

        $dataBundle = new FetchDataBundle();
        $user = $this->businessBundle->getBusinessModel('User');
        $this->helper->addFieldsAsMandatoryInput($dataBundle, $user, $user->getFields());
    }

    public function testAddingExistingDataFromInvalidSourceThrowsException()
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('This DataBundle only supports data from the following source: default. Received: other');

        $dataBundle = new FetchDataBundle();
        $user = $this->businessBundle->getBusinessModel('User');
        $this->helper->addFieldsAsExisting($dataBundle, $user, $user->getFields(), 'other');
    }

    private function assertEmptyDataBundle()
    {
        self::assertEmpty($this->dataBundle->listBusinessModelNames());
        self::assertEmpty($this->dataBundle->getData());
    }
}
