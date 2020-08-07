<?php

namespace CodePrimer\Tests\Helper;

use CodePrimer\Helper\DataBundleHelper;
use CodePrimer\Model\Data\Data;
use CodePrimer\Model\Data\DataBundle;
use CodePrimer\Model\Data\ExistingData;
use CodePrimer\Model\Data\InputData;
use CodePrimer\Model\Package;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class DataBundleHelperTest extends TestCase
{
    /** @var DataBundleHelper */
    private $helper;

    /** @var DataBundle */
    private $dataBundle;

    /** @var Package */
    private $businessBundle;

    public function setUp(): void
    {
        parent::setUp();
        $this->dataBundle = new DataBundle('TestBundle', 'Test Description');
        $this->helper = new DataBundleHelper();
        $this->businessBundle = TestHelper::getSamplePackage();
    }

    public function testAddBusinessModelAsInputShouldOnlyAddUnmanagedFields()
    {
        $user = $this->businessBundle->getBusinessModel('User');
        $this->assertEmptyDataBundle();
        self::assertFalse($this->dataBundle->isBusinessModelPresent('User'));
        self::assertEmpty($this->dataBundle->listInputData('User'));
        self::assertEmpty($this->dataBundle->listExistingData('User'));

        $this->helper->addBusinessModelAsInput($this->dataBundle, $user, InputData::NEW);
        $this->assertEmptyExisting();
        self::assertCount(1, $this->dataBundle->listInputDataBusinessModelNames());
        self::assertTrue($this->dataBundle->isBusinessModelPresent('User'));
        $list = $this->dataBundle->listInputData('User');
        self::assertCount(11, $list);

        // Validate that each data has been properly created
        foreach ($list as $data) {
            self::assertEquals(InputData::NEW, $data->getType());
            self::assertEquals(Data::BASIC, $data->getDetails());
            self::assertEquals($data->getField()->isMandatory(), $data->isMandatory());
        }

        // Adding the same model with different attributes overrides the existing data
        $this->helper->addBusinessModelAsInput($this->dataBundle, $user, InputData::UPDATED, Data::REFERENCE);
        $this->assertEmptyExisting();
        self::assertCount(1, $this->dataBundle->listInputDataBusinessModelNames());
        self::assertTrue($this->dataBundle->isBusinessModelPresent('User'));
        $list = $this->dataBundle->listInputData('User');
        self::assertCount(11, $list);

        // Validate that each data has been properly created
        foreach ($list as $data) {
            self::assertEquals(InputData::UPDATED, $data->getType());
            self::assertEquals(Data::REFERENCE, $data->getDetails());
            self::assertEquals($data->getField()->isMandatory(), $data->isMandatory());
        }
    }

    /**
     * @dataProvider invalidInputArgumentProvider
     */
    public function testAddBusinessModelAsInputWithInvalidArgumentThrowsException($type, $details)
    {
        self::expectException(InvalidArgumentException::class);
        $user = $this->businessBundle->getBusinessModel('User');
        $this->helper->addBusinessModelAsInput($this->dataBundle, $user, $type, $details);
    }

    public function invalidInputArgumentProvider(): array
    {
        return [
            'Invalid type' => ['unknown', Data::REFERENCE],
            'Invalid details' => [InputData::UPDATED, 'unknown'],
            'Using origin instead of type' => [ExistingData::INTERNAL, Data::REFERENCE],
            'Using details instead of type' => [Data::REFERENCE, Data::REFERENCE],
        ];
    }

    public function testAddFieldsAsMandatoryInputAddsAllFieldsProperly()
    {
        $user = $this->businessBundle->getBusinessModel('User');
        $this->assertEmptyDataBundle();

        $this->helper->addFieldsAsMandatoryInput($this->dataBundle, $user, $user->getFields(), InputData::NEW);
        $this->assertEmptyExisting();
        self::assertCount(1, $this->dataBundle->listInputDataBusinessModelNames());
        self::assertTrue($this->dataBundle->isBusinessModelPresent('User'));
        $list = $this->dataBundle->listInputData('User');
        self::assertCount(15, $list);

        // Validate that each data has been properly created
        foreach ($list as $data) {
            self::assertEquals(InputData::NEW, $data->getType());
            self::assertEquals(Data::BASIC, $data->getDetails());
            self::assertTrue($data->isMandatory());
        }
    }

    /**
     * @dataProvider invalidInputArgumentProvider
     */
    public function testAddFieldsAsMandatoryInputWithInvalidArgumentThrowsException($type, $details)
    {
        self::expectException(InvalidArgumentException::class);
        $user = $this->businessBundle->getBusinessModel('User');
        $this->helper->addFieldsAsMandatoryInput($this->dataBundle, $user, $user->getFields(), $type, $details);
    }

    public function testAddFieldsAsOptionalInputAddsAllFieldsProperly()
    {
        $user = $this->businessBundle->getBusinessModel('User');
        $this->assertEmptyDataBundle();

        $this->helper->addFieldsAsOptionalInput($this->dataBundle, $user, $user->getFields(), InputData::NEW);
        $this->assertEmptyExisting();
        self::assertCount(1, $this->dataBundle->listInputDataBusinessModelNames());
        self::assertTrue($this->dataBundle->isBusinessModelPresent('User'));
        $list = $this->dataBundle->listInputData('User');
        self::assertCount(15, $list);

        // Validate that each data has been properly created
        foreach ($list as $data) {
            self::assertEquals(InputData::NEW, $data->getType());
            self::assertEquals(Data::BASIC, $data->getDetails());
            self::assertFalse($data->isMandatory());
        }
    }

    /**
     * @dataProvider invalidInputArgumentProvider
     */
    public function testAddFieldsAsOptionalInputWithInvalidArgumentThrowsException($type, $details)
    {
        self::expectException(InvalidArgumentException::class);
        $user = $this->businessBundle->getBusinessModel('User');
        $this->helper->addFieldsAsOptionalInput($this->dataBundle, $user, $user->getFields(), $type, $details);
    }

    public function testAddBusinessModelAsExistingShouldAddAllFields()
    {
        $user = $this->businessBundle->getBusinessModel('User');
        $this->assertEmptyDataBundle();
        self::assertFalse($this->dataBundle->isBusinessModelPresent('User'));
        self::assertEmpty($this->dataBundle->listInputData('User'));
        self::assertEmpty($this->dataBundle->listExistingData('User'));

        $this->helper->addBusinessModelAsExisting($this->dataBundle, $user, ExistingData::INTERNAL, 'Test Source');
        $this->assertEmptyInput();
        self::assertCount(1, $this->dataBundle->listExistingDataBusinessModelNames());
        self::assertTrue($this->dataBundle->isBusinessModelPresent('User'));
        $list = $this->dataBundle->listExistingData('User');
        self::assertCount(15, $list);

        // Validate that each data has been properly created
        foreach ($list as $data) {
            self::assertEquals(ExistingData::INTERNAL, $data->getOrigin());
            self::assertEquals(Data::BASIC, $data->getDetails());
            self::assertEquals('Test Source', $data->getSource());
        }

        // Adding the same model with different attributes overrides the existing data
        $this->helper->addBusinessModelAsExisting($this->dataBundle, $user, ExistingData::EXTERNAL, 'Test Source 2', Data::REFERENCE);
        $this->assertEmptyInput();
        self::assertCount(1, $this->dataBundle->listExistingDataBusinessModelNames());
        self::assertTrue($this->dataBundle->isBusinessModelPresent('User'));
        $list = $this->dataBundle->listExistingData('User');
        self::assertCount(15, $list);

        // Validate that each data has been properly created
        foreach ($list as $data) {
            self::assertEquals(ExistingData::EXTERNAL, $data->getOrigin());
            self::assertEquals(Data::REFERENCE, $data->getDetails());
            self::assertEquals('Test Source 2', $data->getSource());
        }
    }

    /**
     * @dataProvider invalidExpectArgumentProvider
     */
    public function testAddBusinessModelAsExistingWithInvalidArgumentThrowsException($origin, $details)
    {
        self::expectException(InvalidArgumentException::class);
        $user = $this->businessBundle->getBusinessModel('User');
        $this->helper->addBusinessModelAsExisting($this->dataBundle, $user, $origin, '', $details);
    }

    public function testAddFieldsAsExistingAddsAllFieldsProperly()
    {
        $user = $this->businessBundle->getBusinessModel('User');
        $this->assertEmptyDataBundle();

        $this->helper->addFieldsAsExisting($this->dataBundle, $user, $user->getFields(), ExistingData::INTERNAL);
        $this->assertEmptyInput();
        self::assertCount(1, $this->dataBundle->listExistingDataBusinessModelNames());
        self::assertTrue($this->dataBundle->isBusinessModelPresent('User'));
        $list = $this->dataBundle->listExistingData('User');
        self::assertCount(15, $list);

        // Validate that each data has been properly created
        foreach ($list as $data) {
            self::assertEquals(ExistingData::INTERNAL, $data->getOrigin());
            self::assertEquals(Data::BASIC, $data->getDetails());
            self::assertEquals('', $data->getSource());
        }
    }

    /**
     * @dataProvider invalidExpectArgumentProvider
     */
    public function testAddFieldsAsExistingWithInvalidArgumentThrowsException($origin, $details)
    {
        self::expectException(InvalidArgumentException::class);
        $user = $this->businessBundle->getBusinessModel('User');
        $this->helper->addFieldsAsExisting($this->dataBundle, $user, $user->getFields(), $origin, '', $details);
    }

    public function invalidExpectArgumentProvider(): array
    {
        return [
            'Invalid origin' => ['unknown', Data::REFERENCE],
            'Invalid details' => [ExistingData::EXTERNAL, 'unknown'],
            'Using type instead of origin' => [InputData::NEW, Data::REFERENCE],
            'Using details instead of origin' => [Data::REFERENCE, Data::REFERENCE],
        ];
    }

    private function assertEmptyDataBundle()
    {
        $this->assertEmptyInput();
        $this->assertEmptyExisting();
    }

    private function assertEmptyInput()
    {
        self::assertEmpty($this->dataBundle->listInputDataBusinessModelNames());
        self::assertEmpty($this->dataBundle->getInputData());
    }

    private function assertEmptyExisting()
    {
        self::assertEmpty($this->dataBundle->listExistingDataBusinessModelNames());
        self::assertEmpty($this->dataBundle->getExistingData());
    }
}
