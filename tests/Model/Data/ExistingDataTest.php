<?php

namespace CodePrimer\Tests\Model\Data;

use CodePrimer\Model\BusinessBundle;
use CodePrimer\Model\Data\Data;
use CodePrimer\Model\Data\ExistingData;
use CodePrimer\Tests\Helper\TestHelper;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ExistingDataTest extends TestCase
{
    /** @var BusinessBundle */
    private $businessBundle;

    public function setUp(): void
    {
        parent::setUp();
        $this->businessBundle = TestHelper::getSamplePackage();
    }

    public function testValidConstructorShouldPass()
    {
        $user = $this->businessBundle->getBusinessModel('User');

        $data = new ExistingData(ExistingData::CONTEXT, $user, 'firstName');
        self::assertEquals(ExistingData::CONTEXT, $data->getOrigin());
        self::assertEquals('', $data->getSource());
        self::assertEquals('User', $data->getBusinessModel()->getName());
        self::assertEquals('firstName', $data->getField()->getName());
        self::assertEquals(Data::BASIC, $data->getDetails());

        $data = new ExistingData(ExistingData::INTERNAL, $user, $user->getField('firstName'), 'Test Source', Data::FULL);
        self::assertEquals(ExistingData::INTERNAL, $data->getOrigin());
        self::assertEquals('Test Source', $data->getSource());
        self::assertEquals('User', $data->getBusinessModel()->getName());
        self::assertEquals('firstName', $data->getField()->getName());
        self::assertEquals(Data::FULL, $data->getDetails());

        $data = new ExistingData(ExistingData::EXTERNAL, $user, 'firstName', '', Data::REFERENCE);
        self::assertEquals(ExistingData::EXTERNAL, $data->getOrigin());
        self::assertEquals('', $data->getSource());
        self::assertEquals('User', $data->getBusinessModel()->getName());
        self::assertEquals('firstName', $data->getField()->getName());
        self::assertEquals(Data::REFERENCE, $data->getDetails());
    }

    public function testSetValidOriginShouldWork()
    {
        $user = $this->businessBundle->getBusinessModel('User');

        $data = new ExistingData(ExistingData::CONTEXT, $user, 'firstName');
        self::assertEquals(ExistingData::CONTEXT, $data->getOrigin());

        $data->setOrigin(ExistingData::INTERNAL);
        self::assertEquals(ExistingData::INTERNAL, $data->getOrigin());

        $data->setOrigin(ExistingData::EXTERNAL);
        self::assertEquals(ExistingData::EXTERNAL, $data->getOrigin());

        $data->setOrigin(ExistingData::CONTEXT);
        self::assertEquals(ExistingData::CONTEXT, $data->getOrigin());
    }

    public function testSetInvalidOriginShouldThrowException()
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Invalid origin provided: unknown. Must be one of: context, internal or external');

        $user = $this->businessBundle->getBusinessModel('User');

        $data = new ExistingData(ExistingData::CONTEXT, $user, 'firstName');
        self::assertEquals(ExistingData::CONTEXT, $data->getOrigin());

        $data->setOrigin('unknown');
    }

    public function testConstructorWithInvalidOriginThrowsException()
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Invalid origin provided: unknown. Must be one of: context, internal or external');

        new ExistingData('unknown', $this->businessBundle->getBusinessModel('User'), 'firstName');
    }

    public function testConstructorWithInvalidFieldThrowsException()
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Requested field unknown is not defined in BusinessModel User');

        new ExistingData(ExistingData::CONTEXT, $this->businessBundle->getBusinessModel('User'), 'unknown');
    }

    public function testConstructorWithInvalidFieldTypeThrowsException()
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Requested field must be either of type Field or string');

        new ExistingData(ExistingData::CONTEXT, $this->businessBundle->getBusinessModel('User'), 234);
    }

    public function testConstructorWithInvalidDetailsThrowsException()
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Invalid details provided: unknown. Must be one of: basic, reference or full');

        new ExistingData(ExistingData::CONTEXT, $this->businessBundle->getBusinessModel('User'), 'firstName', true, 'unknown');
    }
}
