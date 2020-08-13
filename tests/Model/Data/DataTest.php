<?php

namespace CodePrimer\Tests\Model\Data;

use CodePrimer\Model\BusinessBundle;
use CodePrimer\Model\Data\Data;
use CodePrimer\Tests\Helper\TestHelper;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class DataTest extends TestCase
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

        $data = new Data($user, 'firstName');
        self::assertEquals('User', $data->getBusinessModel()->getName());
        self::assertEquals('firstName', $data->getField()->getName());
        self::assertEquals(Data::BASIC, $data->getDetails());

        $data = new Data($user, $user->getField('firstName'), Data::FULL);
        self::assertEquals('User', $data->getBusinessModel()->getName());
        self::assertEquals('firstName', $data->getField()->getName());
        self::assertEquals(Data::FULL, $data->getDetails());

        $data = new Data($user, 'firstName', Data::REFERENCE);
        self::assertEquals('User', $data->getBusinessModel()->getName());
        self::assertEquals('firstName', $data->getField()->getName());
        self::assertEquals(Data::REFERENCE, $data->getDetails());
    }

    public function testConstructorWithInvalidFieldThrowsException()
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Requested field unknown is not defined in BusinessModel User');

        new Data($this->businessBundle->getBusinessModel('User'), 'unknown');
    }

    public function testConstructorWithInvalidFieldTypeThrowsException()
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Requested field must be either of type Field or string');

        new Data($this->businessBundle->getBusinessModel('User'), 234);
    }

    public function testConstructorWithInvalidDetailsThrowsException()
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Invalid details provided: unknown. Must be one of: basic, reference or full');

        new Data($this->businessBundle->getBusinessModel('User'), 'firstName', 'unknown');
    }

    /**
     * @dataProvider isSameProvider
     */
    public function testIsSameWorksAsExpected(Data $testData, string $fieldName, bool $expectedResult)
    {
        $data = new Data($this->businessBundle->getBusinessModel('User'), $fieldName);

        self::assertEquals($expectedResult, $data->isSame($testData));
    }

    public function isSameProvider()
    {
        $businessBundle = TestHelper::getSampleBusinessBundle();
        $user = $businessBundle->getBusinessModel('User');
        $post = $businessBundle->getBusinessModel('Post');

        return [
            'Different Model' => [new Data($post, $post->getField('created')), 'created', false],
            'Different Field' => [new Data($user, $user->getField('firstName')), 'created', false],
            'Different Details' => [new Data($user, $user->getField('topics'), Data::FULL), 'topics', false],
            'Same' => [new Data($user, $user->getField('topics')), 'topics', true],
        ];
    }

    public function testGetDescriptionShouldFallbackOnFieldIfNotSet()
    {
        $user = $this->businessBundle->getBusinessModel('User');
        $data = new Data($user, 'id');

        self::assertEquals($user->getField('id')->getDescription(), $data->getDescription());

        $data->setDescription('Test Description Override');
        self::assertEquals('Test Description Override', $data->getDescription());

        $data->setDescription('');
        self::assertEquals('', $data->getDescription());
    }
}
