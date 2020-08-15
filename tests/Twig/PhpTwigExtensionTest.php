<?php

namespace CodePrimer\Tests\Twig;

use CodePrimer\Helper\FieldType;
use CodePrimer\Model\BusinessBundle;
use CodePrimer\Model\BusinessModel;
use CodePrimer\Model\Data\Data;
use CodePrimer\Model\Data\MessageDataBundle;
use CodePrimer\Model\Derived\Event;
use CodePrimer\Model\Field;
use CodePrimer\Tests\Helper\TestHelper;
use CodePrimer\Twig\PhpTwigExtension;
use InvalidArgumentException;

class PhpTwigExtensionTest extends TwigExtensionTest
{
    /**
     * @var PhpTwigExtension
     */
    protected $twigExtension;

    public function setUp(): void
    {
        parent::setUp();
        $this->twigExtension = new PhpTwigExtension();
    }

    public function testGetFiltersShouldPass()
    {
        $filters = $this->twigExtension->getFilters();

        self::assertNotNull($filters);

        $this->assertTwigFilter('plural', $filters);
        $this->assertTwigFilter('singular', $filters);
        $this->assertTwigFilter('words', $filters);
        $this->assertTwigFilter('camel', $filters);
        $this->assertTwigFilter('underscore', $filters);
        $this->assertTwigFilter('path', $filters);
        $this->assertTwigFilter('lastPath', $filters);
        $this->assertTwigFilter('class', $filters);
        $this->assertTwigFilter('constant', $filters);
        $this->assertTwigFilter('member', $filters);
        $this->assertTwigFilter('variable', $filters);
        $this->assertTwigFilter('getter', $filters);
        $this->assertTwigFilter('setter', $filters);
        $this->assertTwigFilter('namespace', $filters);
        $this->assertTwigFilter('type', $filters);
        $this->assertTwigFilter('listType', $filters);
        $this->assertTwigFilter('parameter', $filters);
    }

    public function testGetTestsShouldPass()
    {
        $tests = $this->twigExtension->getTests();

        self::assertNotNull($tests);

        $this->assertTwigTest('dateTimeUsed', $tests);
    }

    /**
     * @dataProvider variableDataProvider
     *
     * @param mixed  $obj           Object to filter
     * @param string $expectedValue expected filtered value
     */
    public function testVariableFilterShouldPass($obj, $expectedValue)
    {
        $value = $this->twigExtension->variableFilter($obj);

        self::assertEquals($expectedValue, $value);
    }

    public function variableDataProvider()
    {
        return [
            ['Tables', '$tables'],
            ['Table', '$table'],
            ['Table_Elements', '$tableElements'],
            ['field_name', '$fieldName'],
            ['field.name', '$field.name'],
            ['field-name', '$fieldName'],
            ['class name', '$className'],
            [new BusinessModel('Entities'), '$entities'],
            [new Field('MYSQL_Field', 'int'), '$mYSQLField'],
            [new BusinessBundle('Packages', 'Package_name'), '$packageName'],
            [new Event('EventName', 'event.code'), '$eventName'],
            [123, 123],
            [1.345, 1.345],
            [null, null],
            ['', ''],
        ];
    }

    /**
     * @dataProvider memberDataProvider
     *
     * @param mixed  $obj           Object to filter
     * @param string $expectedValue expected filtered value
     */
    public function testMemberFilterShouldPass($obj, $expectedValue)
    {
        $value = $this->twigExtension->memberFilter($obj);

        self::assertEquals($expectedValue, $value);
    }

    public function memberDataProvider()
    {
        return [
            ['Tables', '$this->tables'],
            ['Table', '$this->table'],
            ['Table_Elements', '$this->tableElements'],
            ['field_name', '$this->fieldName'],
            ['field.name', '$this->field.name'],
            ['field-name', '$this->fieldName'],
            ['class name', '$this->className'],
            [new BusinessModel('Entities'), '$this->entities'],
            [new Field('MYSQL_Field', 'int'), '$this->mYSQLField'],
            [new BusinessBundle('Packages', 'Package_name'), '$this->packageName'],
            [new Event('EventName', 'event.code'), '$this->eventName'],
            [123, 123],
            [1.345, 1.345],
            [null, null],
            ['', ''],
        ];
    }

    /**
     * @dataProvider namespaceDataProvider
     *
     * @param array       $context       The context to pass to the filter
     * @param mixed       $obj           Object to filter
     * @param string|null $subpackage    The subpackage to pass to the filter
     * @param string      $expectedValue expected filtered value
     */
    public function testNamespaceFilterShouldPass(array $context, $obj, ?string $subpackage, $expectedValue)
    {
        $value = $this->twigExtension->namespaceFilter($context, $obj, $subpackage);

        self::assertEquals($expectedValue, $value);
    }

    public function namespaceDataProvider()
    {
        return [
            [[], 'Company\\Table', null, 'Company\\Table'],
            [[], 'Company/Table', null, 'Company\\Table'],
            [[], 'Company.Table', null, 'Company\\Table'],
            [[], 'company/table', null, 'company\\table'],
            [[], 'Table_Elements', null, 'Table_Elements'],
            [[], 'field_name', null, 'field_name'],
            [[], 'field.name', null, 'field\\name'],
            [[], 'field-name', null, 'field-name'],
            [[], 'class name', null, 'class\name'],
            [[], new BusinessModel('Entities'), null, ''],
            [[], new Field('MYSQL/Field', 'int'), null, ''],
            [[], new BusinessBundle('Packages', 'Package_name'), null, 'Packages'],
            [[], new BusinessBundle('Packages', 'Package_name'), 'SubPackage', 'Packages\SubPackage'],
            [['subpackage' => 'SubPackage'], new BusinessBundle('Packages', 'Package_name'), null, 'Packages\SubPackage'],
            [['subpackage' => 'SubPackage'], new BusinessBundle('Packages', 'Package_name'), 'OverrideSubPackage', 'Packages\OverrideSubPackage'],
            [[], new Event('Event\\Name', 'event.code'), null, ''],
            [[], 123, null, ''],
            [[], 1.345, null, ''],
            [[], null, null, ''],
            [[], '', null, ''],
        ];
    }

    /**
     * @dataProvider typeDataProvider
     *
     * @param mixed  $obj           Object to filter
     * @param string $expectedValue expected filtered value
     */
    public function testTypeFilterShouldPass($obj, $expectedValue)
    {
        $value = $this->twigExtension->typeFilter($this->context, $obj);

        self::assertEquals($expectedValue, $value);
    }

    public function typeDataProvider()
    {
        return [
            'BOOL' => [new Field('Test', FieldType::BOOL, 'Test Description', true), 'bool'],
            'BOOLEAN' => [new Field('Test', FieldType::BOOLEAN, 'Test Description', true), 'bool'],
            'DATE' => [new Field('Test', FieldType::DATE, 'Test Description', true), 'DateTimeInterface'],
            'DATETIME' => [new Field('Test', FieldType::DATETIME, 'Test Description', true), 'DateTimeInterface'],
            'DECIMAL' => [new Field('Test', FieldType::DECIMAL, 'Test Description', true), 'double'],
            'DOUBLE' => [new Field('Test', FieldType::DOUBLE, 'Test Description', true), 'double'],
            'EMAIL' => [new Field('Test', FieldType::EMAIL, 'Test Description', true), 'string'],
            'FLOAT' => [new Field('Test', FieldType::FLOAT, 'Test Description', true), 'float'],
            'ID' => [new Field('Test', FieldType::ID, 'Test Description', true), 'int'],
            'INT' => [new Field('Test', FieldType::INT, 'Test Description', true), 'int'],
            'INTEGER' => [new Field('Test', FieldType::INTEGER, 'Test Description', true), 'int'],
            'LONG' => [new Field('Test', FieldType::LONG, 'Test Description', true), 'int'],
            'PASSWORD' => [new Field('Test', FieldType::PASSWORD, 'Test Description', true), 'string'],
            'PHONE' => [new Field('Test', FieldType::PHONE, 'Test Description', true), 'string'],
            'PRICE' => [new Field('Test', FieldType::PRICE, 'Test Description', true), 'float'],
            'RANDOM_STRING' => [new Field('Test', FieldType::RANDOM_STRING, 'Test Description', true), 'string'],
            'STRING' => [new Field('Test', FieldType::STRING, 'Test Description', true), 'string'],
            'TEXT' => [new Field('Test', FieldType::TEXT, 'Test Description', true), 'string'],
            'TIME' => [new Field('Test', FieldType::TIME, 'Test Description', true), 'DateTimeInterface'],
            'URL' => [new Field('Test', FieldType::URL, 'Test Description', true), 'string'],
            'UUID' => [new Field('Test', FieldType::UUID, 'Test Description', true), 'string'],
            'UNKNOWN' => [new Field('Test', 'Unknown', 'Test Description', true), 'string'],
            'BOOL ARRAY' => [
                (new Field('Test', FieldType::BOOL, 'Test Description', true))
                    ->setList(true),
                'array',
            ],
            'ENTITY' => [new Field('Test', 'User', 'Test Description', true), 'User'],
            'OPTIONAL ENTITY' => [new Field('Test', 'User'), '?User'],
        ];
    }

    /**
     * @dataProvider listTypeDataProvider
     *
     * @param mixed  $obj           Object to filter
     * @param string $expectedValue expected filtered value
     */
    public function testListTypeFilterShouldPass($obj, $expectedValue)
    {
        $value = $this->twigExtension->listTypeFilter($this->context, $obj);

        self::assertEquals($expectedValue, $value);
    }

    public function listTypeDataProvider()
    {
        return [
            'BOOL' => [
                (new Field('Test', FieldType::BOOL, 'Test Description', true))
                    ->setList(true),
                'bool',
            ],
            'BOOLEAN' => [
                (new Field('Test', FieldType::BOOLEAN, 'Test Description', true))
                    ->setList(true),
                'bool',
            ],
            'DATE' => [
                (new Field('Test', FieldType::DATE, 'Test Description', true))
                    ->setList(true),
                'DateTimeInterface',
            ],
            'DATETIME' => [
                (new Field('Test', FieldType::DATETIME, 'Test Description', true))
                    ->setList(true),
                'DateTimeInterface',
            ],
            'DECIMAL' => [
                (new Field('Test', FieldType::DECIMAL, 'Test Description', true))
                    ->setList(true),
                'double',
            ],
            'DOUBLE' => [
                (new Field('Test', FieldType::DOUBLE, 'Test Description', true))
                    ->setList(true),
                'double',
            ],
            'EMAIL' => [
                (new Field('Test', FieldType::EMAIL, 'Test Description', true))
                    ->setList(true),
                'string',
            ],
            'FLOAT' => [
                (new Field('Test', FieldType::FLOAT, 'Test Description', true))
                    ->setList(true),
                'float',
            ],
            'ID' => [
                (new Field('Test', FieldType::ID, 'Test Description', true))
                    ->setList(true),
                'int',
            ],
            'INT' => [
                (new Field('Test', FieldType::INT, 'Test Description', true))
                    ->setList(true),
                'int',
            ],
            'INTEGER' => [
                (new Field('Test', FieldType::INTEGER, 'Test Description', true))
                    ->setList(true),
                'int',
            ],
            'LONG' => [
                (new Field('Test', FieldType::LONG, 'Test Description', true))
                    ->setList(true),
                'int',
            ],
            'PASSWORD' => [
                (new Field('Test', FieldType::PASSWORD, 'Test Description', true))
                    ->setList(true),
                'string',
            ],
            'PHONE' => [
                (new Field('Test', FieldType::PHONE, 'Test Description', true))
                    ->setList(true),
                'string',
            ],
            'PRICE' => [
                (new Field('Test', FieldType::PRICE, 'Test Description', true))
                    ->setList(true),
                'float',
            ],
            'RANDOM_STRING' => [
                (new Field('Test', FieldType::RANDOM_STRING, 'Test Description', true))
                    ->setList(true),
                'string',
            ],
            'STRING' => [
                (new Field('Test', FieldType::STRING, 'Test Description', true))
                    ->setList(true),
                'string',
            ],
            'TEXT' => [
                (new Field('Test', FieldType::TEXT, 'Test Description', true))
                    ->setList(true),
                'string',
            ],
            'TIME' => [
                (new Field('Test', FieldType::TIME, 'Test Description', true))
                    ->setList(true),
                'DateTimeInterface',
            ],
            'URL' => [
                (new Field('Test', FieldType::URL, 'Test Description', true))
                    ->setList(true),
                'string',
            ],
            'UUID' => [
                (new Field('Test', FieldType::UUID, 'Test Description', true))
                    ->setList(true),
                'string',
            ],
            'UNKNOWN' => [
                (new Field('Test', 'Unknown', 'Test Description', true))
                    ->setList(true),
                'string',
            ],
            'OPTIONAL ENTITY' => [
                (new Field('Test', 'User', 'Test Description', false))
                    ->setList(true),
                'User',
            ],
            'ENTITY' => [
                (new Field('Test', 'User', 'Test Description', true))
                    ->setList(true),
                'User',
            ],
        ];
    }

    /**
     * @dataProvider hintDataProvider
     *
     * @param mixed  $obj           Object to filter
     * @param string $expectedValue expected filtered value
     */
    public function testHintFilterShouldPass($obj, $expectedValue)
    {
        $value = $this->twigExtension->hintFilter($this->context, $obj);

        self::assertEquals($expectedValue, $value);
    }

    public function hintDataProvider()
    {
        return [
            'BOOL' => [new Field('Test', FieldType::BOOL, 'Test Description', true), 'bool'],
            'BOOLEAN' => [new Field('Test', FieldType::BOOLEAN, 'Test Description', true), 'bool'],
            'DATE' => [new Field('Test', FieldType::DATE, 'Test Description', true), 'DateTimeInterface'],
            'DATETIME' => [new Field('Test', FieldType::DATETIME, 'Test Description', true), 'DateTimeInterface'],
            'DECIMAL' => [new Field('Test', FieldType::DECIMAL, 'Test Description', true), 'double'],
            'DOUBLE' => [new Field('Test', FieldType::DOUBLE, 'Test Description', true), 'double'],
            'EMAIL' => [new Field('Test', FieldType::EMAIL, 'Test Description', true), 'string'],
            'FLOAT' => [new Field('Test', FieldType::FLOAT, 'Test Description', true), 'float'],
            'ID' => [new Field('Test', FieldType::ID, 'Test Description', true), 'int'],
            'INT' => [new Field('Test', FieldType::INT, 'Test Description', true), 'int'],
            'INTEGER' => [new Field('Test', FieldType::INTEGER, 'Test Description', true), 'int'],
            'LONG' => [new Field('Test', FieldType::LONG, 'Test Description', true), 'int'],
            'PASSWORD' => [new Field('Test', FieldType::PASSWORD, 'Test Description', true), 'string'],
            'PHONE' => [new Field('Test', FieldType::PHONE, 'Test Description', true), 'string'],
            'PRICE' => [new Field('Test', FieldType::PRICE, 'Test Description', true), 'float'],
            'RANDOM_STRING' => [new Field('Test', FieldType::RANDOM_STRING, 'Test Description', true), 'string'],
            'STRING' => [new Field('Test', FieldType::STRING, 'Test Description', true), 'string'],
            'TEXT' => [new Field('Test', FieldType::TEXT, 'Test Description', true), 'string'],
            'TIME' => [new Field('Test', FieldType::TIME, 'Test Description', true), 'DateTimeInterface'],
            'URL' => [new Field('Test', FieldType::URL, 'Test Description', true), 'string'],
            'UUID' => [new Field('Test', FieldType::UUID, 'Test Description', true), 'string'],
            'UNKNOWN' => [new Field('Test', 'Unknown', 'Test Description', true), 'string'],
            'BOOL ARRAY' => [
                (new Field('Test', FieldType::BOOL, 'Test Description', true))
                    ->setList(true),
                'bool[]',
            ],
            'ENTITY' => [new Field('Test', 'User', 'Test Description', true), 'User'],
            'OPTIONAL ENTITY' => [new Field('Test', 'User'), 'User|null'],
        ];
    }

    /**
     * @dataProvider parameterDataProvider
     *
     * @param mixed  $obj           Object to filter
     * @param string $expectedValue expected filtered value
     */
    public function testParameterFilterShouldPass($obj, $expectedValue)
    {
        $value = $this->twigExtension->parameterFilter($this->context, $obj);

        self::assertEquals($expectedValue, $value);
    }

    public function parameterDataProvider()
    {
        return [
            'BOOL' => [new Field('Test', FieldType::BOOL, 'Test Description', true), 'bool $test'],
            'BOOLEAN' => [new Field('Test', FieldType::BOOLEAN, 'Test Description', true), 'bool $test'],
            'DATE' => [new Field('Test', FieldType::DATE, 'Test Description', true), 'DateTimeInterface $test'],
            'DATETIME' => [new Field('Test', FieldType::DATETIME, 'Test Description', true), 'DateTimeInterface $test'],
            'DECIMAL' => [new Field('Test', FieldType::DECIMAL, 'Test Description', true), 'double $test'],
            'DOUBLE' => [new Field('Test', FieldType::DOUBLE, 'Test Description', true), 'double $test'],
            'EMAIL' => [new Field('Test', FieldType::EMAIL, 'Test Description', true), 'string $test'],
            'FLOAT' => [new Field('Test', FieldType::FLOAT, 'Test Description', true), 'float $test'],
            'ID' => [new Field('Test', FieldType::ID, 'Test Description', true), 'int $test'],
            'INT' => [new Field('Test', FieldType::INT, 'Test Description', true), 'int $test'],
            'INTEGER' => [new Field('Test', FieldType::INTEGER, 'Test Description', true), 'int $test'],
            'LONG' => [new Field('Test', FieldType::LONG, 'Test Description', true), 'int $test'],
            'PASSWORD' => [new Field('Test', FieldType::PASSWORD, 'Test Description', true), 'string $test'],
            'PHONE' => [new Field('Test', FieldType::PHONE, 'Test Description', true), 'string $test'],
            'PRICE' => [new Field('Test', FieldType::PRICE, 'Test Description', true), 'float $test'],
            'RANDOM_STRING' => [new Field('Test', FieldType::RANDOM_STRING, 'Test Description', true), 'string $test'],
            'STRING' => [new Field('Test', FieldType::STRING, 'Test Description', true), 'string $test'],
            'TEXT' => [new Field('Test', FieldType::TEXT, 'Test Description', true), 'string $test'],
            'TIME' => [new Field('Test', FieldType::TIME, 'Test Description', true), 'DateTimeInterface $test'],
            'URL' => [new Field('Test', FieldType::URL, 'Test Description', true), 'string $test'],
            'UUID' => [new Field('Test', FieldType::UUID, 'Test Description', true), 'string $test'],
            'UNKNOWN' => [new Field('Test', 'Unknown', 'Test Description', true), 'string $test'],
            'BOOL ARRAY' => [
                (new Field('Test', FieldType::BOOL, 'Test Description', true))
                    ->setList(true),
                'array $test',
            ],
            'ENTITY' => [new Field('Test', 'User', 'Test Description', true), 'User $test'],
            'OPTIONAL ENTITY' => [new Field('Test', 'User'), '?User $test'],
            'LIST OF 1 FIELD' => [
                [
                    new Field('Param1', FieldType::UUID, 'Test Description', true),
                ],
                'string $param1',
            ],
            'LIST OF FIELDS' => [
                [
                    new Field('Param1', FieldType::UUID, 'Test Description', true),
                    new Field('Param2', 'User'),
                    new Field('Param3', FieldType::INT),
                ],
                'string $param1, ?User $param2, ?int $param3',
            ],
        ];
    }

    /**
     * @dataProvider dateTimeUsedProvider
     *
     * @param BusinessModel|Event $obj
     */
    public function testDateTimeUsedShouldPass($obj, bool $expected)
    {
        self::assertEquals($expected, $this->twigExtension->dateTimeUsed($obj));
    }

    public function dateTimeUsedProvider()
    {
        $businessBundle = TestHelper::getSampleBusinessBundle();

        return [
            'User' => [
                $businessBundle->getBusinessModel('User'),
                true,
            ],
            'Metadata' => [
                $businessBundle->getBusinessModel('Metadata'),
                false,
            ],
            'Post' => [
                $businessBundle->getBusinessModel('Post'),
                true,
            ],
            'Topic' => [
                $businessBundle->getBusinessModel('Topic'),
                true,
            ],
            'Event without timestamp' => [
                $businessBundle->getEvent('Login Request'),
                false,
            ],
            'Event with timestamp' => [
                $businessBundle->getEvent('Schedule Post'),
                true,
            ],
        ];
    }

    public function testDateTimeUsedWithInvalidTypeThrowsException()
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('dateTimeUsed() PHP filter only support BusinessModel and Event');

        $businessBundle = TestHelper::getSampleBusinessBundle();

        $dataBundle = new MessageDataBundle();
        $dataBundle->add(new Data($businessBundle->getBusinessModel('User'), 'created'));
        $this->twigExtension->dateTimeUsed($dataBundle);
    }
}
