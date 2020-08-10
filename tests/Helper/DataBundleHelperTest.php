<?php

namespace CodePrimer\Tests\Helper;

use CodePrimer\Helper\DataBundleHelper;
use CodePrimer\Model\BusinessBundle;
use CodePrimer\Model\Data\Data;
use CodePrimer\Model\Data\DataBundle;
use CodePrimer\Model\Data\ExistingData;
use CodePrimer\Model\Data\ExistingDataBundle;
use CodePrimer\Model\Data\InputData;
use CodePrimer\Model\Data\InputDataBundle;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class DataBundleHelperTest extends TestCase
{
    /** @var DataBundleHelper */
    private $helper;

    /** @var InputDataBundle */
    private $inputDataBundle;

    /** @var ExistingDataBundle */
    private $existingDataBundle;

    /** @var BusinessBundle */
    private $businessBundle;

    public function setUp(): void
    {
        parent::setUp();
        $this->inputDataBundle = new InputDataBundle('TestInputBundle', 'Test Description');
        $this->existingDataBundle = new ExistingDataBundle(ExistingData::DEFAULT_SOURCE, 'TestExistingBundle', 'Test Description');
        $this->helper = new DataBundleHelper();
        $this->businessBundle = TestHelper::getSampleBusinessBundle();
    }

    public function testAddBusinessModelAsInputShouldOnlyAddUnmanagedFields()
    {
        $user = $this->businessBundle->getBusinessModel('User');
        $this->assertEmptyDataBundle();
        self::assertFalse($this->inputDataBundle->isBusinessModelPresent('User'));
        self::assertEmpty($this->inputDataBundle->listData('User'));

        $this->helper->addBusinessModelAsInput($this->inputDataBundle, $user);
        self::assertCount(1, $this->inputDataBundle->listBusinessModelNames());
        self::assertTrue($this->inputDataBundle->isBusinessModelPresent('User'));
        $list = $this->inputDataBundle->listData('User');
        self::assertCount(11, $list);

        // Validate that each data has been properly created
        foreach ($list as $data) {
            self::assertInstanceOf(InputData::class, $data);
            self::assertEquals(Data::BASIC, $data->getDetails());
            self::assertEquals($data->getField()->isMandatory(), $data->isMandatory());
        }

        // Adding the same model with different attributes overrides the existing data
        $this->helper->addBusinessModelAsInput($this->inputDataBundle, $user, Data::REFERENCE);
        self::assertCount(1, $this->inputDataBundle->listBusinessModelNames());
        self::assertTrue($this->inputDataBundle->isBusinessModelPresent('User'));
        $list = $this->inputDataBundle->listData('User');
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
        $this->helper->addBusinessModelAsInput($this->inputDataBundle, $user, 'unknown');
    }

    public function testAddFieldsAsMandatoryInputAddsAllFieldsProperly()
    {
        $user = $this->businessBundle->getBusinessModel('User');
        $this->assertEmptyDataBundle();

        $this->helper->addFieldsAsMandatoryInput($this->inputDataBundle, $user, $user->getFields());
        self::assertCount(1, $this->inputDataBundle->listBusinessModelNames());
        self::assertTrue($this->inputDataBundle->isBusinessModelPresent('User'));
        $list = $this->inputDataBundle->listData('User');
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
        $this->helper->addFieldsAsMandatoryInput($this->inputDataBundle, $user, $user->getFields(), 'unknown');
    }

    public function testAddFieldsAsOptionalInputAddsAllFieldsProperly()
    {
        $user = $this->businessBundle->getBusinessModel('User');
        $this->assertEmptyDataBundle();

        $this->helper->addFieldsAsOptionalInput($this->inputDataBundle, $user, $user->getFields());
        self::assertCount(1, $this->inputDataBundle->listBusinessModelNames());
        self::assertTrue($this->inputDataBundle->isBusinessModelPresent('User'));
        $list = $this->inputDataBundle->listData('User');
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
        $this->helper->addFieldsAsOptionalInput($this->inputDataBundle, $user, $user->getFields(), 'unknown');
    }

    public function testAddBusinessModelAsExistingShouldAddAllFields()
    {
        $user = $this->businessBundle->getBusinessModel('User');
        $this->assertEmptyDataBundle();
        self::assertFalse($this->existingDataBundle->isBusinessModelPresent('User'));
        self::assertEmpty($this->existingDataBundle->listData('User'));

        $this->helper->addBusinessModelAsExisting($this->existingDataBundle, $user);
        self::assertCount(1, $this->existingDataBundle->listBusinessModelNames());
        self::assertTrue($this->existingDataBundle->isBusinessModelPresent('User'));
        $list = $this->existingDataBundle->listData('User');
        self::assertCount(15, $list);

        // Validate that each data has been properly created
        foreach ($list as $data) {
            self::assertInstanceOf(ExistingData::class, $data);
            self::assertEquals(Data::BASIC, $data->getDetails());
            self::assertEquals(ExistingData::DEFAULT_SOURCE, $data->getSource());
        }

        // Adding the same model with different attributes overrides the existing data
        $this->helper->addBusinessModelAsExisting($this->existingDataBundle, $user, 'Test Source 2', Data::REFERENCE);
        self::assertCount(1, $this->existingDataBundle->listBusinessModelNames());
        self::assertTrue($this->existingDataBundle->isBusinessModelPresent('User'));
        $list = $this->existingDataBundle->listData('User');
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
        $this->helper->addBusinessModelAsExisting($this->existingDataBundle, $user, '', 'unknown');
    }

    public function testAddFieldsAsExistingAddsAllFieldsProperly()
    {
        $user = $this->businessBundle->getBusinessModel('User');
        $this->assertEmptyDataBundle();

        $this->helper->addFieldsAsExisting($this->existingDataBundle, $user, $user->getFields());
        self::assertCount(1, $this->existingDataBundle->listBusinessModelNames());
        self::assertTrue($this->existingDataBundle->isBusinessModelPresent('User'));
        $list = $this->existingDataBundle->listData('User');
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
        $this->helper->addFieldsAsExisting($this->existingDataBundle, $user, $user->getFields(), '', 'unknown');
    }

    /**
    public function testAddingExistingDataFromInvalidSourceThrowsException()
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('This DataBundle only supports data from the following source: default. Received: other');

        $dataBundle = new ExistingDataBundle();
        $user = $this->businessBundle->getBusinessModel('User');
        $this->helper->addFieldsAsExisting($dataBundle, $user, $user->getFields(), 'other');
    }
     */
    private function assertEmptyDataBundle()
    {
        self::assertEmpty($this->inputDataBundle->listBusinessModelNames());
        self::assertEmpty($this->inputDataBundle->getData());
    }
}
