<?php

namespace CodePrimer\Tests\Model;

use CodePrimer\Helper\FieldType;
use CodePrimer\Model\Field;
use PHPUnit\Framework\TestCase;

class FieldTest extends TestCase
{
    public function testBasicSetters()
    {
        $field = new Field('TestName', FieldType::STRING, 'Test Description');

        self::assertEquals('TestName', $field->getName());
        self::assertEquals(FieldType::STRING, $field->getType());
        self::assertEquals('Test Description', $field->getDescription());
        self::assertFalse($field->isMandatory());
        self::assertFalse($field->isManaged());
        self::assertFalse($field->isList());
        self::assertFalse($field->isSearchable());
        self::assertNull($field->getDefault());
        self::assertNull($field->getExample());

        $field
            ->setManaged(true)
            ->setMandatory(true)
            ->setDescription('New Description')
            ->setName('NewName')
            ->setDefault('Default')
            ->setExample('Example')
            ->setList(true)
            ->setSearchable(true)
            ->setType(FieldType::URL);

        self::assertEquals('NewName', $field->getName());
        self::assertEquals(FieldType::URL, $field->getType());
        self::assertEquals('New Description', $field->getDescription());
        self::assertTrue($field->isMandatory());
        self::assertTrue($field->isManaged());
        self::assertTrue($field->isList());
        self::assertTrue($field->isSearchable());
        self::assertEquals('Default', $field->getDefault());
        self::assertEquals('Example', $field->getExample());
    }

    /**
     * @dataProvider validValueProvider
     *
     * @param $value
     */
    public function testSetDefaultWithValidValueShouldWork(Field $field, $value)
    {
        $field->setDefault($value);
        self::assertEquals($value, $field->getDefault());
    }

    /**
     * @dataProvider validValueProvider
     *
     * @param $value
     */
    public function testSetExampleWithValidValueShouldWork(Field $field, $value)
    {
        $field->setExample($value);
        self::assertEquals($value, $field->getExample());
    }

    public function validValueProvider()
    {
        return [
            'Email' => [new Field('Test', FieldType::EMAIL), 'test@test.com'],
            'Integer' => [new Field('Test', FieldType::INTEGER), 25],
            'Bool' => [new Field('Test', FieldType::BOOLEAN), true],
            'Float' => [new Field('Test', FieldType::FLOAT), 25.345],
        ];
    }

    /**
     * @dataProvider invalidValueProvider
     *
     * @param $value
     */
    public function testSetDefaultWithInvalidValueShouldThrowException(Field $field, $value)
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("'$value' is not a valid value for type '{$field->getType()}' as defined for field {$field->getName()}");
        $field->setDefault($value);
    }

    /**
     * @dataProvider invalidValueProvider
     *
     * @param $value
     */
    public function testSetExampleWithInvalidValueShouldThrowException(Field $field, $value)
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("'$value' is not a valid value for type '{$field->getType()}' as defined for field {$field->getName()}");
        $field->setExample($value);
    }

    public function invalidValueProvider()
    {
        return [
            'Email' => [new Field('Test', FieldType::EMAIL), 'test'],
            'Integer' => [new Field('Test', FieldType::INTEGER), 'test'],
            'Bool' => [new Field('Test', FieldType::BOOLEAN), 'test'],
            'Float' => [new Field('Test', FieldType::FLOAT), 'test'],
        ];
    }
}
