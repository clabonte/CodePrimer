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

        $data = new InputData($user, 'firstName');
        self::assertEquals('User', $data->getBusinessModel()->getName());
        self::assertEquals('firstName', $data->getField()->getName());
        self::assertEquals(Data::BASIC, $data->getDetails());
        self::assertTrue($data->isMandatory());

        $data = new InputData($user, $user->getField('firstName'), false, Data::FULL);
        self::assertEquals('User', $data->getBusinessModel()->getName());
        self::assertEquals('firstName', $data->getField()->getName());
        self::assertEquals(Data::FULL, $data->getDetails());
        self::assertFalse($data->isMandatory());

        $data = new InputData($user, 'firstName', true, Data::REFERENCE);
        self::assertEquals('User', $data->getBusinessModel()->getName());
        self::assertEquals('firstName', $data->getField()->getName());
        self::assertEquals(Data::REFERENCE, $data->getDetails());
        self::assertTrue($data->isMandatory());
    }

    public function testConstructorWithInvalidFieldThrowsException()
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Requested field unknown is not defined in BusinessModel User');

        new InputData($this->businessBundle->getBusinessModel('User'), 'unknown');
    }

    public function testConstructorWithInvalidFieldTypeThrowsException()
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Requested field must be either of type Field or string');

        new InputData($this->businessBundle->getBusinessModel('User'), 234);
    }

    public function testConstructorWithInvalidDetailsThrowsException()
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Invalid details provided: unknown. Must be one of: basic, reference or full');

        new InputData($this->businessBundle->getBusinessModel('User'), 'firstName', true, 'unknown');
    }
}
