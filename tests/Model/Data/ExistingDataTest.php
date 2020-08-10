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
        $this->businessBundle = TestHelper::getSampleBusinessBundle();
    }

    public function testValidConstructorShouldPass()
    {
        $user = $this->businessBundle->getBusinessModel('User');

        $data = new ExistingData($user, 'firstName');
        self::assertEquals(ExistingData::DEFAULT_SOURCE, $data->getSource());
        self::assertEquals('User', $data->getBusinessModel()->getName());
        self::assertEquals('firstName', $data->getField()->getName());
        self::assertEquals(Data::BASIC, $data->getDetails());

        $data = new ExistingData($user, $user->getField('firstName'), 'Test Source', Data::FULL);
        self::assertEquals('Test Source', $data->getSource());
        self::assertEquals('User', $data->getBusinessModel()->getName());
        self::assertEquals('firstName', $data->getField()->getName());
        self::assertEquals(Data::FULL, $data->getDetails());

        $data = new ExistingData($user, 'firstName', '', Data::REFERENCE);
        self::assertEquals('', $data->getSource());
        self::assertEquals('User', $data->getBusinessModel()->getName());
        self::assertEquals('firstName', $data->getField()->getName());
        self::assertEquals(Data::REFERENCE, $data->getDetails());
    }

    public function testConstructorWithInvalidFieldThrowsException()
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Requested field unknown is not defined in BusinessModel User');

        new ExistingData($this->businessBundle->getBusinessModel('User'), 'unknown');
    }

    public function testConstructorWithInvalidFieldTypeThrowsException()
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Requested field must be either of type Field or string');

        new ExistingData($this->businessBundle->getBusinessModel('User'), 234);
    }

    public function testConstructorWithInvalidDetailsThrowsException()
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Invalid details provided: unknown. Must be one of: basic, reference or full');

        new ExistingData($this->businessBundle->getBusinessModel('User'), 'firstName', true, 'unknown');
    }
}
