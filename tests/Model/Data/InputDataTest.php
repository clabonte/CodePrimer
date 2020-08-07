<?php

namespace CodePrimer\Tests\Model\Data;

use CodePrimer\Model\BusinessBundle;
use CodePrimer\Model\Data\Data;
use CodePrimer\Model\Data\InputData;
use CodePrimer\Tests\Helper\TestHelper;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class InputDataTest extends TestCase
{
    /** @var BusinessBundle */
    private $businessBundle;

    public function setUp(): void
    {
        parent::setUp();
        $this->businessBundle = TestHelper::getSampleBusinessBundle();
    }

    public function testValidConstructorShouldPass()
    {
        $user = $this->businessBundle->getBusinessModel('User');

        $data = new InputData(InputData::NEW, $user, 'firstName');
        self::assertEquals(InputData::NEW, $data->getType());
        self::assertEquals('User', $data->getBusinessModel()->getName());
        self::assertEquals('firstName', $data->getField()->getName());
        self::assertEquals(Data::BASIC, $data->getDetails());
        self::assertTrue($data->isMandatory());

        $data = new InputData(InputData::UPDATED, $user, $user->getField('firstName'), false, Data::FULL);
        self::assertEquals(InputData::UPDATED, $data->getType());
        self::assertEquals('User', $data->getBusinessModel()->getName());
        self::assertEquals('firstName', $data->getField()->getName());
        self::assertEquals(Data::FULL, $data->getDetails());
        self::assertFalse($data->isMandatory());

        $data = new InputData(InputData::NEW_OR_UPDATED, $user, 'firstName', true, Data::REFERENCE);
        self::assertEquals(InputData::NEW_OR_UPDATED, $data->getType());
        self::assertEquals('User', $data->getBusinessModel()->getName());
        self::assertEquals('firstName', $data->getField()->getName());
        self::assertEquals(Data::REFERENCE, $data->getDetails());
        self::assertTrue($data->isMandatory());

        $data = new InputData(InputData::OBSOLETE, $user, 'firstName');
        self::assertEquals(InputData::OBSOLETE, $data->getType());
        self::assertEquals('User', $data->getBusinessModel()->getName());
        self::assertEquals('firstName', $data->getField()->getName());
        self::assertEquals(Data::BASIC, $data->getDetails());
        self::assertTrue($data->isMandatory());
    }

    public function testSetValidTypeShouldWork()
    {
        $user = $this->businessBundle->getBusinessModel('User');

        $data = new InputData(InputData::NEW, $user, 'firstName');
        self::assertEquals(InputData::NEW, $data->getType());

        $data->setType(InputData::NEW_OR_UPDATED);
        self::assertEquals(InputData::NEW_OR_UPDATED, $data->getType());

        $data->setType(InputData::UPDATED);
        self::assertEquals(InputData::UPDATED, $data->getType());

        $data->setType(InputData::OBSOLETE);
        self::assertEquals(InputData::OBSOLETE, $data->getType());

        $data->setType(InputData::NEW);
        self::assertEquals(InputData::NEW, $data->getType());
    }

    public function testSetInvalidTypeShouldThrowException()
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Invalid type provided: unknown. Must be one of: new, updated, newOrUpdated or obsolete');

        $user = $this->businessBundle->getBusinessModel('User');

        $data = new InputData(InputData::NEW, $user, 'firstName');
        self::assertEquals(InputData::NEW, $data->getType());

        $data->setType('unknown');
    }

    public function testConstructorWithInvalidTypeThrowsException()
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Invalid type provided: unknown. Must be one of: new, updated, newOrUpdated or obsolete');

        new InputData('unknown', $this->businessBundle->getBusinessModel('User'), 'firstName');
    }

    public function testConstructorWithInvalidFieldThrowsException()
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Requested field unknown is not defined in BusinessModel User');

        new InputData(InputData::NEW, $this->businessBundle->getBusinessModel('User'), 'unknown');
    }

    public function testConstructorWithInvalidFieldTypeThrowsException()
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Requested field must be either of type Field or string');

        new InputData(InputData::NEW, $this->businessBundle->getBusinessModel('User'), 234);
    }

    public function testConstructorWithInvalidDetailsThrowsException()
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Invalid details provided: unknown. Must be one of: basic, reference or full');

        new InputData(InputData::NEW, $this->businessBundle->getBusinessModel('User'), 'firstName', true, 'unknown');
    }
}
