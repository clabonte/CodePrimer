<?php

namespace CodePrimer\Tests\Helper;

use CodePrimer\Helper\FieldHelper;
use CodePrimer\Helper\FieldType;
use CodePrimer\Model\BusinessBundle;
use CodePrimer\Model\BusinessModel;
use CodePrimer\Model\Field;
use PHPUnit\Framework\TestCase;

class FieldHelperTest extends TestCase
{
    /** @var FieldHelper */
    private $helper;

    public function setUp(): void
    {
        parent::setUp();
        $this->helper = new FieldHelper();
    }

    /**
     * @param Field $field    The field to test
     * @param bool  $expected The expected value for the field being tested
     * @dataProvider stringProvider
     */
    public function testIsString(Field $field, bool $expected)
    {
        self::assertEquals($expected, $this->helper->isString($field));
    }

    public function stringProvider()
    {
        return [
            'BOOL' => [new Field('Test', FieldType::BOOL), false],
            'BOOLEAN' => [new Field('Test', FieldType::BOOLEAN), false],
            'DATE' => [new Field('Test', FieldType::DATE), false],
            'DATETIME' => [new Field('Test', FieldType::DATETIME), false],
            'DECIMAL' => [new Field('Test', FieldType::DECIMAL), false],
            'DOUBLE' => [new Field('Test', FieldType::DOUBLE), false],
            'EMAIL' => [new Field('Test', FieldType::EMAIL), true],
            'FLOAT' => [new Field('Test', FieldType::FLOAT), false],
            'ID' => [new Field('Test', FieldType::ID), false],
            'INT' => [new Field('Test', FieldType::INT), false],
            'INTEGER' => [new Field('Test', FieldType::INTEGER), false],
            'LONG' => [new Field('Test', FieldType::LONG), false],
            'PASSWORD' => [new Field('Test', FieldType::PASSWORD), true],
            'PHONE' => [new Field('Test', FieldType::PHONE), true],
            'PRICE' => [new Field('Test', FieldType::PRICE), false],
            'RANDOM_STRING' => [new Field('Test', FieldType::RANDOM_STRING), true],
            'STRING' => [new Field('Test', FieldType::STRING), true],
            'TEXT' => [new Field('Test', FieldType::TEXT), true],
            'TIME' => [new Field('Test', FieldType::TIME), false],
            'URL' => [new Field('Test', FieldType::URL), true],
            'UUID' => [new Field('Test', FieldType::UUID), true],
        ];
    }

    /**
     * @param Field $field    The field to test
     * @param bool  $expected The expected value for the field being tested
     * @dataProvider autoIncrementProvider
     */
    public function testIsAutoIncrement(Field $field, bool $expected)
    {
        self::assertEquals($expected, $this->helper->isAutoIncrement($field));
    }

    public function autoIncrementProvider()
    {
        return [
            'BOOL' => [new Field('Test', FieldType::BOOL), false],
            'BOOLEAN' => [new Field('Test', FieldType::BOOLEAN), false],
            'DATE' => [new Field('Test', FieldType::DATE), false],
            'DATETIME' => [new Field('Test', FieldType::DATETIME), false],
            'DECIMAL' => [new Field('Test', FieldType::DECIMAL), false],
            'DOUBLE' => [new Field('Test', FieldType::DOUBLE), false],
            'EMAIL' => [new Field('Test', FieldType::EMAIL), false],
            'FLOAT' => [new Field('Test', FieldType::FLOAT), false],
            'ID' => [new Field('Test', FieldType::ID), true],
            'INT' => [new Field('Test', FieldType::INT), false],
            'INTEGER' => [new Field('Test', FieldType::INTEGER), false],
            'LONG' => [new Field('Test', FieldType::LONG), false],
            'PASSWORD' => [new Field('Test', FieldType::PASSWORD), false],
            'PHONE' => [new Field('Test', FieldType::PHONE), false],
            'PRICE' => [new Field('Test', FieldType::PRICE), false],
            'RANDOM_STRING' => [new Field('Test', FieldType::RANDOM_STRING), false],
            'STRING' => [new Field('Test', FieldType::STRING), false],
            'TEXT' => [new Field('Test', FieldType::TEXT), false],
            'TIME' => [new Field('Test', FieldType::TIME), false],
            'URL' => [new Field('Test', FieldType::URL), false],
            'UUID' => [new Field('Test', FieldType::UUID), false],
        ];
    }

    /**
     * @param Field $field    The field to test
     * @param bool  $expected The expected value for the field being tested
     * @dataProvider priceProvider
     */
    public function testIsPrice(Field $field, bool $expected)
    {
        self::assertEquals($expected, $this->helper->isPrice($field));
    }

    public function priceProvider()
    {
        return [
            'BOOL' => [new Field('Test', FieldType::BOOL), false],
            'BOOLEAN' => [new Field('Test', FieldType::BOOLEAN), false],
            'DATE' => [new Field('Test', FieldType::DATE), false],
            'DATETIME' => [new Field('Test', FieldType::DATETIME), false],
            'DECIMAL' => [new Field('Test', FieldType::DECIMAL), false],
            'DOUBLE' => [new Field('Test', FieldType::DOUBLE), false],
            'EMAIL' => [new Field('Test', FieldType::EMAIL), false],
            'FLOAT' => [new Field('Test', FieldType::FLOAT), false],
            'ID' => [new Field('Test', FieldType::ID), false],
            'INT' => [new Field('Test', FieldType::INT), false],
            'INTEGER' => [new Field('Test', FieldType::INTEGER), false],
            'LONG' => [new Field('Test', FieldType::LONG), false],
            'PASSWORD' => [new Field('Test', FieldType::PASSWORD), false],
            'PHONE' => [new Field('Test', FieldType::PHONE), false],
            'PRICE' => [new Field('Test', FieldType::PRICE), true],
            'RANDOM_STRING' => [new Field('Test', FieldType::RANDOM_STRING), false],
            'STRING' => [new Field('Test', FieldType::STRING), false],
            'TEXT' => [new Field('Test', FieldType::TEXT), false],
            'TIME' => [new Field('Test', FieldType::TIME), false],
            'URL' => [new Field('Test', FieldType::URL), false],
            'UUID' => [new Field('Test', FieldType::UUID), false],
        ];
    }

    /**
     * @param Field $field    The field to test
     * @param bool  $expected The expected value for the field being tested
     * @dataProvider doubleProvider
     */
    public function testIsDouble(Field $field, bool $expected)
    {
        self::assertEquals($expected, $this->helper->isDouble($field));
    }

    public function doubleProvider()
    {
        return [
            'BOOL' => [new Field('Test', FieldType::BOOL), false],
            'BOOLEAN' => [new Field('Test', FieldType::BOOLEAN), false],
            'DATE' => [new Field('Test', FieldType::DATE), false],
            'DATETIME' => [new Field('Test', FieldType::DATETIME), false],
            'DECIMAL' => [new Field('Test', FieldType::DECIMAL), true],
            'DOUBLE' => [new Field('Test', FieldType::DOUBLE), true],
            'EMAIL' => [new Field('Test', FieldType::EMAIL), false],
            'FLOAT' => [new Field('Test', FieldType::FLOAT), false],
            'ID' => [new Field('Test', FieldType::ID), false],
            'INT' => [new Field('Test', FieldType::INT), false],
            'INTEGER' => [new Field('Test', FieldType::INTEGER), false],
            'LONG' => [new Field('Test', FieldType::LONG), false],
            'PASSWORD' => [new Field('Test', FieldType::PASSWORD), false],
            'PHONE' => [new Field('Test', FieldType::PHONE), false],
            'PRICE' => [new Field('Test', FieldType::PRICE), false],
            'RANDOM_STRING' => [new Field('Test', FieldType::RANDOM_STRING), false],
            'STRING' => [new Field('Test', FieldType::STRING), false],
            'TEXT' => [new Field('Test', FieldType::TEXT), false],
            'TIME' => [new Field('Test', FieldType::TIME), false],
            'URL' => [new Field('Test', FieldType::URL), false],
            'UUID' => [new Field('Test', FieldType::UUID), false],
        ];
    }

    /**
     * @param Field $field    The field to test
     * @param bool  $expected The expected value for the field being tested
     * @dataProvider dateTimeProvider
     */
    public function testIsDateTime(Field $field, bool $expected)
    {
        self::assertEquals($expected, $this->helper->isDateTime($field));
    }

    public function dateTimeProvider()
    {
        return [
            'BOOL' => [new Field('Test', FieldType::BOOL), false],
            'BOOLEAN' => [new Field('Test', FieldType::BOOLEAN), false],
            'DATE' => [new Field('Test', FieldType::DATE), false],
            'DATETIME' => [new Field('Test', FieldType::DATETIME), true],
            'DECIMAL' => [new Field('Test', FieldType::DECIMAL), false],
            'DOUBLE' => [new Field('Test', FieldType::DOUBLE), false],
            'EMAIL' => [new Field('Test', FieldType::EMAIL), false],
            'FLOAT' => [new Field('Test', FieldType::FLOAT), false],
            'ID' => [new Field('Test', FieldType::ID), false],
            'INT' => [new Field('Test', FieldType::INT), false],
            'INTEGER' => [new Field('Test', FieldType::INTEGER), false],
            'LONG' => [new Field('Test', FieldType::LONG), false],
            'PASSWORD' => [new Field('Test', FieldType::PASSWORD), false],
            'PHONE' => [new Field('Test', FieldType::PHONE), false],
            'PRICE' => [new Field('Test', FieldType::PRICE), false],
            'RANDOM_STRING' => [new Field('Test', FieldType::RANDOM_STRING), false],
            'STRING' => [new Field('Test', FieldType::STRING), false],
            'TEXT' => [new Field('Test', FieldType::TEXT), false],
            'TIME' => [new Field('Test', FieldType::TIME), false],
            'URL' => [new Field('Test', FieldType::URL), false],
            'UUID' => [new Field('Test', FieldType::UUID), false],
        ];
    }

    /**
     * @param Field $field    The field to test
     * @param bool  $expected The expected value for the field being tested
     * @dataProvider dateProvider
     */
    public function testIsDate(Field $field, bool $expected)
    {
        self::assertEquals($expected, $this->helper->isDate($field));
    }

    public function dateProvider()
    {
        return [
            'BOOL' => [new Field('Test', FieldType::BOOL), false],
            'BOOLEAN' => [new Field('Test', FieldType::BOOLEAN), false],
            'DATE' => [new Field('Test', FieldType::DATE), true],
            'DATETIME' => [new Field('Test', FieldType::DATETIME), false],
            'DECIMAL' => [new Field('Test', FieldType::DECIMAL), false],
            'DOUBLE' => [new Field('Test', FieldType::DOUBLE), false],
            'EMAIL' => [new Field('Test', FieldType::EMAIL), false],
            'FLOAT' => [new Field('Test', FieldType::FLOAT), false],
            'ID' => [new Field('Test', FieldType::ID), false],
            'INT' => [new Field('Test', FieldType::INT), false],
            'INTEGER' => [new Field('Test', FieldType::INTEGER), false],
            'LONG' => [new Field('Test', FieldType::LONG), false],
            'PASSWORD' => [new Field('Test', FieldType::PASSWORD), false],
            'PHONE' => [new Field('Test', FieldType::PHONE), false],
            'PRICE' => [new Field('Test', FieldType::PRICE), false],
            'RANDOM_STRING' => [new Field('Test', FieldType::RANDOM_STRING), false],
            'STRING' => [new Field('Test', FieldType::STRING), false],
            'TEXT' => [new Field('Test', FieldType::TEXT), false],
            'TIME' => [new Field('Test', FieldType::TIME), false],
            'URL' => [new Field('Test', FieldType::URL), false],
            'UUID' => [new Field('Test', FieldType::UUID), false],
        ];
    }

    /**
     * @param Field $field    The field to test
     * @param bool  $expected The expected value for the field being tested
     * @dataProvider booleanProvider
     */
    public function testIsBoolean(Field $field, bool $expected)
    {
        self::assertEquals($expected, $this->helper->isBoolean($field));
    }

    public function booleanProvider()
    {
        return [
            'BOOL' => [new Field('Test', FieldType::BOOL), true],
            'BOOLEAN' => [new Field('Test', FieldType::BOOLEAN), true],
            'DATE' => [new Field('Test', FieldType::DATE), false],
            'DATETIME' => [new Field('Test', FieldType::DATETIME), false],
            'DECIMAL' => [new Field('Test', FieldType::DECIMAL), false],
            'DOUBLE' => [new Field('Test', FieldType::DOUBLE), false],
            'EMAIL' => [new Field('Test', FieldType::EMAIL), false],
            'FLOAT' => [new Field('Test', FieldType::FLOAT), false],
            'ID' => [new Field('Test', FieldType::ID), false],
            'INT' => [new Field('Test', FieldType::INT), false],
            'INTEGER' => [new Field('Test', FieldType::INTEGER), false],
            'LONG' => [new Field('Test', FieldType::LONG), false],
            'PASSWORD' => [new Field('Test', FieldType::PASSWORD), false],
            'PHONE' => [new Field('Test', FieldType::PHONE), false],
            'PRICE' => [new Field('Test', FieldType::PRICE), false],
            'RANDOM_STRING' => [new Field('Test', FieldType::RANDOM_STRING), false],
            'STRING' => [new Field('Test', FieldType::STRING), false],
            'TEXT' => [new Field('Test', FieldType::TEXT), false],
            'TIME' => [new Field('Test', FieldType::TIME), false],
            'URL' => [new Field('Test', FieldType::URL), false],
            'UUID' => [new Field('Test', FieldType::UUID), false],
        ];
    }

    /**
     * @param Field $field    The field to test
     * @param bool  $expected The expected value for the field being tested
     * @dataProvider uuidProvider
     */
    public function testIsUuid(Field $field, bool $expected)
    {
        self::assertEquals($expected, $this->helper->isUuid($field));
    }

    public function uuidProvider()
    {
        return [
            'BOOL' => [new Field('Test', FieldType::BOOL), false],
            'BOOLEAN' => [new Field('Test', FieldType::BOOLEAN), false],
            'DATE' => [new Field('Test', FieldType::DATE), false],
            'DATETIME' => [new Field('Test', FieldType::DATETIME), false],
            'DECIMAL' => [new Field('Test', FieldType::DECIMAL), false],
            'DOUBLE' => [new Field('Test', FieldType::DOUBLE), false],
            'EMAIL' => [new Field('Test', FieldType::EMAIL), false],
            'FLOAT' => [new Field('Test', FieldType::FLOAT), false],
            'ID' => [new Field('Test', FieldType::ID), false],
            'INT' => [new Field('Test', FieldType::INT), false],
            'INTEGER' => [new Field('Test', FieldType::INTEGER), false],
            'LONG' => [new Field('Test', FieldType::LONG), false],
            'PASSWORD' => [new Field('Test', FieldType::PASSWORD), false],
            'PHONE' => [new Field('Test', FieldType::PHONE), false],
            'PRICE' => [new Field('Test', FieldType::PRICE), false],
            'RANDOM_STRING' => [new Field('Test', FieldType::RANDOM_STRING), false],
            'STRING' => [new Field('Test', FieldType::STRING), false],
            'TEXT' => [new Field('Test', FieldType::TEXT), false],
            'TIME' => [new Field('Test', FieldType::TIME), false],
            'URL' => [new Field('Test', FieldType::URL), false],
            'UUID' => [new Field('Test', FieldType::UUID), true],
        ];
    }

    /**
     * @param Field $field    The field to test
     * @param bool  $expected The expected value for the field being tested
     * @dataProvider binaryProvider
     */
    public function testIsBinary(Field $field, bool $expected)
    {
        self::assertEquals($expected, $this->helper->isBinary($field));
    }

    public function binaryProvider()
    {
        return [
            'BOOL' => [new Field('Test', FieldType::BOOL), false],
            'BOOLEAN' => [new Field('Test', FieldType::BOOLEAN), false],
            'DATE' => [new Field('Test', FieldType::DATE), false],
            'DATETIME' => [new Field('Test', FieldType::DATETIME), false],
            'DECIMAL' => [new Field('Test', FieldType::DECIMAL), false],
            'DOUBLE' => [new Field('Test', FieldType::DOUBLE), false],
            'EMAIL' => [new Field('Test', FieldType::EMAIL), false],
            'FLOAT' => [new Field('Test', FieldType::FLOAT), false],
            'ID' => [new Field('Test', FieldType::ID), false],
            'INT' => [new Field('Test', FieldType::INT), false],
            'INTEGER' => [new Field('Test', FieldType::INTEGER), false],
            'LONG' => [new Field('Test', FieldType::LONG), false],
            'PASSWORD' => [new Field('Test', FieldType::PASSWORD), false],
            'PHONE' => [new Field('Test', FieldType::PHONE), false],
            'PRICE' => [new Field('Test', FieldType::PRICE), false],
            'RANDOM_STRING' => [new Field('Test', FieldType::RANDOM_STRING), false],
            'STRING' => [new Field('Test', FieldType::STRING), false],
            'TEXT' => [new Field('Test', FieldType::TEXT), false],
            'TIME' => [new Field('Test', FieldType::TIME), false],
            'URL' => [new Field('Test', FieldType::URL), false],
            'UUID' => [new Field('Test', FieldType::UUID), false],
        ];
    }

    /**
     * @dataProvider floatProvider
     */
    public function testIsFloat(Field $field, bool $expected)
    {
        self::assertEquals($expected, $this->helper->isFloat($field));
    }

    public function floatProvider()
    {
        return [
            'BOOL' => [new Field('Test', FieldType::BOOL), false],
            'BOOLEAN' => [new Field('Test', FieldType::BOOLEAN), false],
            'DATE' => [new Field('Test', FieldType::DATE), false],
            'DATETIME' => [new Field('Test', FieldType::DATETIME), false],
            'DECIMAL' => [new Field('Test', FieldType::DECIMAL), false],
            'DOUBLE' => [new Field('Test', FieldType::DOUBLE), false],
            'EMAIL' => [new Field('Test', FieldType::EMAIL), false],
            'FLOAT' => [new Field('Test', FieldType::FLOAT), true],
            'ID' => [new Field('Test', FieldType::ID), false],
            'INT' => [new Field('Test', FieldType::INT), false],
            'INTEGER' => [new Field('Test', FieldType::INTEGER), false],
            'LONG' => [new Field('Test', FieldType::LONG), false],
            'PASSWORD' => [new Field('Test', FieldType::PASSWORD), false],
            'PHONE' => [new Field('Test', FieldType::PHONE), false],
            'PRICE' => [new Field('Test', FieldType::PRICE), false],
            'RANDOM_STRING' => [new Field('Test', FieldType::RANDOM_STRING), false],
            'STRING' => [new Field('Test', FieldType::STRING), false],
            'TEXT' => [new Field('Test', FieldType::TEXT), false],
            'TIME' => [new Field('Test', FieldType::TIME), false],
            'URL' => [new Field('Test', FieldType::URL), false],
            'UUID' => [new Field('Test', FieldType::UUID), false],
        ];
    }

    /**
     * @param Field $field    The field to test
     * @param bool  $expected The expected value for the field being tested
     * @dataProvider timeProvider
     */
    public function testIsTime(Field $field, bool $expected)
    {
        self::assertEquals($expected, $this->helper->isTime($field));
    }

    public function timeProvider()
    {
        return [
            'BOOL' => [new Field('Test', FieldType::BOOL), false],
            'BOOLEAN' => [new Field('Test', FieldType::BOOLEAN), false],
            'DATE' => [new Field('Test', FieldType::DATE), false],
            'DATETIME' => [new Field('Test', FieldType::DATETIME), false],
            'DECIMAL' => [new Field('Test', FieldType::DECIMAL), false],
            'DOUBLE' => [new Field('Test', FieldType::DOUBLE), false],
            'EMAIL' => [new Field('Test', FieldType::EMAIL), false],
            'FLOAT' => [new Field('Test', FieldType::FLOAT), false],
            'ID' => [new Field('Test', FieldType::ID), false],
            'INT' => [new Field('Test', FieldType::INT), false],
            'INTEGER' => [new Field('Test', FieldType::INTEGER), false],
            'LONG' => [new Field('Test', FieldType::LONG), false],
            'PASSWORD' => [new Field('Test', FieldType::PASSWORD), false],
            'PHONE' => [new Field('Test', FieldType::PHONE), false],
            'PRICE' => [new Field('Test', FieldType::PRICE), false],
            'RANDOM_STRING' => [new Field('Test', FieldType::RANDOM_STRING), false],
            'STRING' => [new Field('Test', FieldType::STRING), false],
            'TEXT' => [new Field('Test', FieldType::TEXT), false],
            'TIME' => [new Field('Test', FieldType::TIME), true],
            'URL' => [new Field('Test', FieldType::URL), false],
            'UUID' => [new Field('Test', FieldType::UUID), false],
        ];
    }

    /**
     * @param Field $field    The field to test
     * @param bool  $expected The expected value for the field being tested
     * @dataProvider longProvider
     */
    public function testIsLong(Field $field, bool $expected)
    {
        self::assertEquals($expected, $this->helper->isLong($field));
    }

    public function longProvider()
    {
        return [
            'BOOL' => [new Field('Test', FieldType::BOOL), false],
            'BOOLEAN' => [new Field('Test', FieldType::BOOLEAN), false],
            'DATE' => [new Field('Test', FieldType::DATE), false],
            'DATETIME' => [new Field('Test', FieldType::DATETIME), false],
            'DECIMAL' => [new Field('Test', FieldType::DECIMAL), false],
            'DOUBLE' => [new Field('Test', FieldType::DOUBLE), false],
            'EMAIL' => [new Field('Test', FieldType::EMAIL), false],
            'FLOAT' => [new Field('Test', FieldType::FLOAT), false],
            'ID' => [new Field('Test', FieldType::ID), true],
            'INT' => [new Field('Test', FieldType::INT), false],
            'INTEGER' => [new Field('Test', FieldType::INTEGER), false],
            'LONG' => [new Field('Test', FieldType::LONG), true],
            'PASSWORD' => [new Field('Test', FieldType::PASSWORD), false],
            'PHONE' => [new Field('Test', FieldType::PHONE), false],
            'PRICE' => [new Field('Test', FieldType::PRICE), false],
            'RANDOM_STRING' => [new Field('Test', FieldType::RANDOM_STRING), false],
            'STRING' => [new Field('Test', FieldType::STRING), false],
            'TEXT' => [new Field('Test', FieldType::TEXT), false],
            'TIME' => [new Field('Test', FieldType::TIME), false],
            'URL' => [new Field('Test', FieldType::URL), false],
            'UUID' => [new Field('Test', FieldType::UUID), false],
        ];
    }

    /**
     * @param Field $field    The field to test
     * @param bool  $expected The expected value for the field being tested
     * @dataProvider integerProvider
     */
    public function testIsInteger(Field $field, bool $expected)
    {
        self::assertEquals($expected, $this->helper->isInteger($field));
    }

    public function integerProvider()
    {
        return [
            'BOOL' => [new Field('Test', FieldType::BOOL), false],
            'BOOLEAN' => [new Field('Test', FieldType::BOOLEAN), false],
            'DATE' => [new Field('Test', FieldType::DATE), false],
            'DATETIME' => [new Field('Test', FieldType::DATETIME), false],
            'DECIMAL' => [new Field('Test', FieldType::DECIMAL), false],
            'DOUBLE' => [new Field('Test', FieldType::DOUBLE), false],
            'EMAIL' => [new Field('Test', FieldType::EMAIL), false],
            'FLOAT' => [new Field('Test', FieldType::FLOAT), false],
            'ID' => [new Field('Test', FieldType::ID), false],
            'INT' => [new Field('Test', FieldType::INT), true],
            'INTEGER' => [new Field('Test', FieldType::INTEGER), true],
            'LONG' => [new Field('Test', FieldType::LONG), false],
            'PASSWORD' => [new Field('Test', FieldType::PASSWORD), false],
            'PHONE' => [new Field('Test', FieldType::PHONE), false],
            'PRICE' => [new Field('Test', FieldType::PRICE), false],
            'RANDOM_STRING' => [new Field('Test', FieldType::RANDOM_STRING), false],
            'STRING' => [new Field('Test', FieldType::STRING), false],
            'TEXT' => [new Field('Test', FieldType::TEXT), false],
            'TIME' => [new Field('Test', FieldType::TIME), false],
            'URL' => [new Field('Test', FieldType::URL), false],
            'UUID' => [new Field('Test', FieldType::UUID), false],
        ];
    }

    /**
     * @param Field $field    The field to test
     * @param bool  $expected The expected value for the field being tested
     * @dataProvider identifierProvider
     */
    public function testIsIdentifier(Field $field, bool $expected)
    {
        self::assertEquals($expected, $this->helper->isIdentifier($field));
    }

    public function identifierProvider()
    {
        return [
            'BOOL' => [new Field('Test', FieldType::BOOL), false],
            'BOOLEAN' => [new Field('Test', FieldType::BOOLEAN), false],
            'DATE' => [new Field('Test', FieldType::DATE), false],
            'DATETIME' => [new Field('Test', FieldType::DATETIME), false],
            'DECIMAL' => [new Field('Test', FieldType::DECIMAL), false],
            'DOUBLE' => [new Field('Test', FieldType::DOUBLE), false],
            'EMAIL' => [new Field('Test', FieldType::EMAIL), false],
            'FLOAT' => [new Field('Test', FieldType::FLOAT), false],
            'ID' => [new Field('Test', FieldType::ID), true],
            'INT' => [new Field('Test', FieldType::INT), false],
            'INTEGER' => [new Field('Test', FieldType::INTEGER), false],
            'LONG' => [new Field('Test', FieldType::LONG), false],
            'PASSWORD' => [new Field('Test', FieldType::PASSWORD), false],
            'PHONE' => [new Field('Test', FieldType::PHONE), false],
            'PRICE' => [new Field('Test', FieldType::PRICE), false],
            'RANDOM_STRING' => [new Field('Test', FieldType::RANDOM_STRING), false],
            'STRING' => [new Field('Test', FieldType::STRING), false],
            'TEXT' => [new Field('Test', FieldType::TEXT), false],
            'TIME' => [new Field('Test', FieldType::TIME), false],
            'URL' => [new Field('Test', FieldType::URL), false],
            'UUID' => [new Field('Test', FieldType::UUID), true],
        ];
    }

    /**
     * @param Field $field    The field to test
     * @param bool  $expected The expected value for the field being tested
     * @dataProvider entityProvider
     */
    public function testIsBusinessModel(Field $field, bool $expected)
    {
        $package = new BusinessBundle('namespace', 'name');
        $package
            ->addBusinessModel(new BusinessModel('TestData1', 'description1'))
            ->addBusinessModel(new BusinessModel('TestData4', 'description4'));

        self::assertEquals($expected, $this->helper->isBusinessModel($field, $package));
    }

    public function entityProvider()
    {
        return [
            'BOOL' => [new Field('Test', FieldType::BOOL), false],
            'BOOLEAN' => [new Field('Test', FieldType::BOOLEAN), false],
            'DATE' => [new Field('Test', FieldType::DATE), false],
            'DATETIME' => [new Field('Test', FieldType::DATETIME), false],
            'DECIMAL' => [new Field('Test', FieldType::DECIMAL), false],
            'DOUBLE' => [new Field('Test', FieldType::DOUBLE), false],
            'EMAIL' => [new Field('Test', FieldType::EMAIL), false],
            'FLOAT' => [new Field('Test', FieldType::FLOAT), false],
            'ID' => [new Field('Test', FieldType::ID), false],
            'INT' => [new Field('Test', FieldType::INT), false],
            'INTEGER' => [new Field('Test', FieldType::INTEGER), false],
            'LONG' => [new Field('Test', FieldType::LONG), false],
            'PASSWORD' => [new Field('Test', FieldType::PASSWORD), false],
            'PHONE' => [new Field('Test', FieldType::PHONE), false],
            'PRICE' => [new Field('Test', FieldType::PRICE), false],
            'RANDOM_STRING' => [new Field('Test', FieldType::RANDOM_STRING), false],
            'STRING' => [new Field('Test', FieldType::STRING), false],
            'TEXT' => [new Field('Test', FieldType::TEXT), false],
            'TIME' => [new Field('Test', FieldType::TIME), false],
            'URL' => [new Field('Test', FieldType::URL), false],
            'UUID' => [new Field('Test', FieldType::UUID), false],
            'UNKNOWN' => [new Field('Test', 'Unknown'), false],
            'TestData1' => [new Field('Test', 'TestData1'), true],
            'TestData2' => [new Field('Test', 'TestData2'), false],
            'TestData3' => [new Field('Test', 'TestData3'), false],
            'TestData4' => [new Field('Test', 'TestData4'), true],
        ];
    }

    /**
     * @param Field $field    The field to test
     * @param bool  $expected The expected value for the field being tested
     * @dataProvider nativeProvider
     */
    public function testIsNative(Field $field, bool $expected)
    {
        self::assertEquals($expected, $this->helper->isNativeType($field));
    }

    public function nativeProvider()
    {
        return [
            'BOOL' => [new Field('Test', FieldType::BOOL), true],
            'BOOLEAN' => [new Field('Test', FieldType::BOOLEAN), true],
            'DATE' => [new Field('Test', FieldType::DATE), true],
            'DATETIME' => [new Field('Test', FieldType::DATETIME), true],
            'DECIMAL' => [new Field('Test', FieldType::DECIMAL), true],
            'DOUBLE' => [new Field('Test', FieldType::DOUBLE), true],
            'EMAIL' => [new Field('Test', FieldType::EMAIL), true],
            'FLOAT' => [new Field('Test', FieldType::FLOAT), true],
            'ID' => [new Field('Test', FieldType::ID), true],
            'INT' => [new Field('Test', FieldType::INT), true],
            'INTEGER' => [new Field('Test', FieldType::INTEGER), true],
            'LONG' => [new Field('Test', FieldType::LONG), true],
            'PASSWORD' => [new Field('Test', FieldType::PASSWORD), true],
            'PHONE' => [new Field('Test', FieldType::PHONE), true],
            'PRICE' => [new Field('Test', FieldType::PRICE), true],
            'RANDOM_STRING' => [new Field('Test', FieldType::RANDOM_STRING), true],
            'STRING' => [new Field('Test', FieldType::STRING), true],
            'TEXT' => [new Field('Test', FieldType::TEXT), true],
            'TIME' => [new Field('Test', FieldType::TIME), true],
            'URL' => [new Field('Test', FieldType::URL), true],
            'UUID' => [new Field('Test', FieldType::UUID), true],
            'UNKNOWN' => [new Field('Test', 'Unknown'), false],
            'TestData1' => [new Field('Test', 'TestData1'), false],
            'TestData2' => [new Field('Test', 'TestData2'), false],
            'TestData3' => [new Field('Test', 'TestData3'), false],
            'TestData4' => [new Field('Test', 'TestData4'), false],
        ];
    }

    /**
     * @dataProvider businessModelCreatedTimestampProvider
     *
     * @param bool $expected
     */
    public function testIsBusinessModelCreatedTimestamp(Field $field, $expected)
    {
        self::assertEquals($expected, $this->helper->isBusinessModelCreatedTimestamp($field));
    }

    public function businessModelCreatedTimestampProvider()
    {
        return [
            'managed created datetime field' => [
                (new Field('created', FieldType::DATETIME))
                    ->setManaged(true),
                true,
            ],
            'managed createdAt datetime field' => [
                (new Field('createdAt', FieldType::DATETIME))
                    ->setManaged(true),
                true,
            ],
            'managed created at datetime field' => [
                (new Field('created at', FieldType::DATETIME))
                    ->setManaged(true),
                true,
            ],
            'managed created date field' => [
                (new Field('created', FieldType::DATE))
                    ->setManaged(true),
                false,
            ],
            'managed created time field' => [
                (new Field('created', FieldType::TIME))
                    ->setManaged(true),
                false,
            ],
            'unmanaged created datetime field' => [
                (new Field('created', FieldType::DATETIME))
                    ->setManaged(false),
                false,
            ],
            'managed updated datetime field' => [
                (new Field('updated', FieldType::DATETIME))
                    ->setManaged(true),
                false,
            ],
        ];
    }

    /**
     * @dataProvider businessModelUpdatedTimestampProvider
     *
     * @param bool $expected
     */
    public function testIsBusinessModelUpdatedTimestamp(Field $field, $expected)
    {
        self::assertEquals($expected, $this->helper->isBusinessModelUpdatedTimestamp($field));
    }

    public function businessModelUpdatedTimestampProvider()
    {
        return [
            'managed updated datetime field' => [
                (new Field('updated', FieldType::DATETIME))
                    ->setManaged(true),
                true,
            ],
            'managed updatedAt datetime field' => [
                (new Field('updatedAt', FieldType::DATETIME))
                    ->setManaged(true),
                true,
            ],
            'managed updated at datetime field' => [
                (new Field('updated at', FieldType::DATETIME))
                    ->setManaged(true),
                true,
            ],
            'managed updated date field' => [
                (new Field('updated', FieldType::DATE))
                    ->setManaged(true),
                false,
            ],
            'managed updated time field' => [
                (new Field('updated', FieldType::TIME))
                    ->setManaged(true),
                false,
            ],
            'unmanaged updated datetime field' => [
                (new Field('updated', FieldType::DATETIME))
                    ->setManaged(false),
                false,
            ],
            'managed created datetime field' => [
                (new Field('created', FieldType::DATETIME))
                    ->setManaged(true),
                false,
            ],
        ];
    }
}
