<?php

namespace CodePrimer\Tests\Model;

use CodePrimer\Model\DataSetElement;
use PHPUnit\Framework\TestCase;

class DataSetElementTest extends TestCase
{
    public function testConstructorWithEmptyArrayShouldWork()
    {
        $element = new DataSetElement();
        self::assertEmpty($element->getValues());

        self::assertNull($element->getValue('url'));
        $element->addValue('url', 'http://element1.test.com');
        self::assertEquals('http://element1.test.com', $element->getValue('url'));
    }

    public function testConstructorWithAssociativeArrayShouldWork()
    {
        $element = new DataSetElement(['url' => 'http://element1.test.com']);
        self::assertCount(1, $element->getValues());

        self::assertEquals('http://element1.test.com', $element->getValue('url'));
    }

    /**
     * @dataProvider invalidArrayProvider
     */
    public function testConstructorWithInvalidArrayThrowsException(array $values)
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid array type passed. Must be an associative array of type 'name' (string) => 'value' (mixed)");
        new DataSetElement($values);
    }

    /**
     * @dataProvider invalidArrayProvider
     */
    public function testSetValuesWithInvalidArrayThrowsException(array $values)
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid array type passed. Must be an associative array of type 'name' (string) => 'value' (mixed)");
        $element = new DataSetElement();
        $element->setValues($values);
    }

    public function invalidArrayProvider()
    {
        return [
            'Standard array' => [['http://element.test.com', 'element@email.com']],
            'Mixed array' => [['url' => 'http://element.test.com', 'element@email.com']],
        ];
    }
}
