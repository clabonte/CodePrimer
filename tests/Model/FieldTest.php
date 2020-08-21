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
        self::assertFalse($field->isIdentifier());

        $field
            ->setManaged(true)
            ->setMandatory(true)
            ->setDescription('New Description')
            ->setName('NewName')
            ->setDefault('Default')
            ->setExample('Example')
            ->setList(true)
            ->setSearchable(true)
            ->setType(FieldType::URL)
            ->setIdentifier(true);

        self::assertEquals('NewName', $field->getName());
        self::assertEquals(FieldType::URL, $field->getType());
        self::assertEquals('New Description', $field->getDescription());
        self::assertTrue($field->isMandatory());
        self::assertTrue($field->isManaged());
        self::assertTrue($field->isList());
        self::assertTrue($field->isSearchable());
        self::assertEquals('Default', $field->getDefault());
        self::assertEquals('Example', $field->getExample());
        self::assertTrue($field->isIdentifier());
    }

    /**
     * @param Field $field The field to test
     * @param bool $expected The expected value for the field being tested
     * @dataProvider identifierProvider
     */
    public function testIsIdentifier(Field $field, bool $expected)
    {
        $field->setIdentifier($expected);
        self::assertEquals($expected, $field->isIdentifier());
    }

    public function identifierProvider()
    {
        return [
            'BOOL_NOT_IDENTIFIER' => [new Field('Test', FieldType::BOOL), false],
            'BOOLEAN_NOT_IDENTIFIER' => [new Field('Test', FieldType::BOOLEAN), false],
            'DATE_NOT_IDENTIFIER' => [new Field('Test', FieldType::DATE), false],
            'DATETIME_NOT_IDENTIFIER' => [new Field('Test', FieldType::DATETIME), false],
            'DECIMAL_NOT_IDENTIFIER' => [new Field('Test', FieldType::DECIMAL), false],
            'DOUBLE_NOT_IDENTIFIER' => [new Field('Test', FieldType::DOUBLE), false],
            'EMAIL_NOT_IDENTIFIER' => [new Field('Test', FieldType::EMAIL), false],
            'FLOAT_NOT_IDENTIFIER' => [new Field('Test', FieldType::FLOAT), false],
            'INT_NOT_IDENTIFIER' => [new Field('Test', FieldType::INT), false],
            'INTEGER_NOT_IDENTIFIER' => [new Field('Test', FieldType::INTEGER), false],
            'LONG_NOT_IDENTIFIER' => [new Field('Test', FieldType::LONG), false],
            'PASSWORD_NOT_IDENTIFIER' => [new Field('Test', FieldType::PASSWORD), false],
            'PHONE_NOT_IDENTIFIER' => [new Field('Test', FieldType::PHONE), false],
            'PRICE_NOT_IDENTIFIER' => [new Field('Test', FieldType::PRICE), false],
            'RANDOM_STRING_NOT_IDENTIFIER' => [new Field('Test', FieldType::RANDOM_STRING), false],
            'STRING_NOT_IDENTIFIER' => [new Field('Test', FieldType::STRING), false],
            'TEXT_NOT_IDENTIFIER' => [new Field('Test', FieldType::TEXT), false],
            'TIME_NOT_IDENTIFIER' => [new Field('Test', FieldType::TIME), false],
            'URL_NOT_IDENTIFIER' => [new Field('Test', FieldType::URL), false],
            'ID_NOT_IDENTIFIER' => [new Field('Test', FieldType::ID), false],
            'UUID_NOT_IDENTIFIER' => [new Field('Test', FieldType::UUID), false],
            'BOOL_IDENTIFIER' => [new Field('Test', FieldType::BOOL), true],
            'BOOLEAN_IDENTIFIER' => [new Field('Test', FieldType::BOOLEAN), true],
            'DATE_IDENTIFIER' => [new Field('Test', FieldType::DATE), true],
            'DATETIME_IDENTIFIER' => [new Field('Test', FieldType::DATETIME), true],
            'DECIMAL_IDENTIFIER' => [new Field('Test', FieldType::DECIMAL), true],
            'DOUBLE_IDENTIFIER' => [new Field('Test', FieldType::DOUBLE), true],
            'EMAIL_IDENTIFIER' => [new Field('Test', FieldType::EMAIL), true],
            'FLOAT_IDENTIFIER' => [new Field('Test', FieldType::FLOAT), true],
            'INT_IDENTIFIER' => [new Field('Test', FieldType::INT), true],
            'INTEGER_IDENTIFIER' => [new Field('Test', FieldType::INTEGER), true],
            'LONG_IDENTIFIER' => [new Field('Test', FieldType::LONG), true],
            'PASSWORD_IDENTIFIER' => [new Field('Test', FieldType::PASSWORD), true],
            'PHONE_IDENTIFIER' => [new Field('Test', FieldType::PHONE), true],
            'PRICE_IDENTIFIER' => [new Field('Test', FieldType::PRICE), true],
            'RANDOM_STRING_IDENTIFIER' => [new Field('Test', FieldType::RANDOM_STRING), true],
            'STRING_IDENTIFIER' => [new Field('Test', FieldType::STRING), true],
            'TEXT_IDENTIFIER' => [new Field('Test', FieldType::TEXT), true],
            'TIME_IDENTIFIER' => [new Field('Test', FieldType::TIME), true],
            'URL_IDENTIFIER' => [new Field('Test', FieldType::URL), true],
            'ID_IDENTIFIER' => [new Field('Test', FieldType::ID), true],
            'UUID_IDENTIFIER' => [new Field('Test', FieldType::UUID), true],
        ];
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
