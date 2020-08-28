<?php

namespace CodePrimer\Tests\Twig;

use CodePrimer\Helper\FieldType;
use CodePrimer\Model\BusinessBundle;
use CodePrimer\Model\BusinessModel;
use CodePrimer\Model\Data\Data;
use CodePrimer\Model\Data\MessageDataBundle;
use CodePrimer\Model\Dataset;
use CodePrimer\Model\DatasetElement;
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
        $this->assertTwigFilter('value', $filters);
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
        $businessBundle = TestHelper::getSampleBusinessBundle();

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
            'DATA - EMAIL' => [new Data($businessBundle->getBusinessModel('User'), 'email'), 'string'],
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
        $businessBundle = TestHelper::getSampleBusinessBundle();

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
            'DATASET' => [
                (new Field('Test', 'Plan', 'Test Description', true))
                    ->setList(true),
                'Plan',
            ],
            'DATA - TOPIC' => [new Data($businessBundle->getBusinessModel('User'), 'topics'), 'Topic'],
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
        $businessBundle = TestHelper::getSampleBusinessBundle();

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
            'DATA - TOPIC' => [new Data($businessBundle->getBusinessModel('User'), 'topics'), 'Topic[]|null'],
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
        $businessBundle = TestHelper::getSampleBusinessBundle();

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
            'DATA - TOPIC - DEFAULT NAME' => [new Data($businessBundle->getBusinessModel('User'), 'topics'), '?array $topics'],
            'DATA - TOPIC - CUSTOM NAME' => [(new Data($businessBundle->getBusinessModel('User'), 'topics'))->setName('custom'), '?array $custom'],
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
            'Dataset without timestamp' => [
                $businessBundle->getDataset('UserStatus'),
                false,
            ],
            'Dataset with timestamp' => [
                (new Dataset('Test Dataset'))
                    ->addField((new Field('name', FieldType::STRING))->setIdentifier(true))
                    ->addField(new Field('start', FieldType::DATE))
                    ->addElement(new DatasetElement(['name' => 'element1', 'start' => '2000-01-01'])),
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

    /**
     * @dataProvider valueFilterProvider
     *
     * @param $obj
     * @param $value
     * @param $expectedValue
     */
    public function testValueFilterShouldPass($obj, $value, $expectedValue)
    {
        $actual = $this->twigExtension->valueFilter($this->context, $obj, $value);

        self::assertEquals($expectedValue, $actual);
    }

    public function valueFilterProvider()
    {
        $bundle = TestHelper::getSampleBusinessBundle();

        return [
            'UUID' => [new Field('Test', FieldType::UUID), '11123e3a-c02A-4E85-a397-221abac28264', "'11123e3a-c02A-4E85-a397-221abac28264'"],
            'STRING' => [new Field('Test', FieldType::STRING), 'this is a test', "'this is a test'"],
            'STRING with quote' => [new Field('Test', FieldType::STRING), "this is a test with a ' (single quote)", '"this is a test with a \' (single quote)"'],
            'TEXT' => [new Field('Test', FieldType::TEXT), 'this is a test', "'this is a test'"],
            'PASSWORD' => [new Field('Test', FieldType::PASSWORD), 'this is a test', "'this is a test'"],
            'RANDOM_STRING' => [new Field('Test', FieldType::RANDOM_STRING), 'this is a test', "'this is a test'"],
            'EMAIL' => [new Field('Test', FieldType::EMAIL), 'test@test.com', "'test@test.com'"],
            'URL' => [new Field('Test', FieldType::URL), 'http://test.com', "'http://test.com'"],
            'PHONE' => [new Field('Test', FieldType::PHONE), '+15551234567', "'+15551234567'"],
            'DATE - string' => [new Field('Test', FieldType::DATE), '2020-12-25', "new \DateTimeImmutable('2020-12-25')"],
            'DATE - DateTime' => [new Field('Test', FieldType::DATE), new \DateTime('2020-12-25'), "new \DateTimeImmutable('2020-12-25')"],
            'TIME - string' => [new Field('Test', FieldType::TIME), '23:59:59', "new \DateTimeImmutable('23:59:59')"],
            'TIME - DateTime' => [new Field('Test', FieldType::TIME), new \DateTime('23:59:59'), "new \DateTimeImmutable('23:59:59')"],
            'DATETIME - string' => [new Field('Test', FieldType::DATETIME), '2020-12-25T23:59:59Z', "new \DateTimeImmutable('2020-12-25T23:59:59Z')"],
            'DATETIME - DateTime' => [new Field('Test', FieldType::DATETIME), new \DateTime('2020-12-25T23:59:59Z'), "new \DateTimeImmutable('2020-12-25 23:59:59')"],
            'BOOLEAN - true' => [new Field('Test', FieldType::BOOLEAN), true, 'true'],
            'BOOLEAN - false' => [new Field('Test', FieldType::BOOLEAN), 'no', 'false'],
            'INTEGER - number' => [new Field('Test', FieldType::INTEGER), 123, 123],
            'INTEGER - string' => [new Field('Test', FieldType::INTEGER), '123', 123],
            'ID - number' => [new Field('Test', FieldType::ID), 123, 123],
            'ID - string' => [new Field('Test', FieldType::ID), '123', 123],
            'LONG - number' => [new Field('Test', FieldType::LONG), 123, 123],
            'LONG - string' => [new Field('Test', FieldType::LONG), '123', 123],
            'FLOAT - number' => [new Field('Test', FieldType::FLOAT), 123.4, 123.4],
            'FLOAT - int' => [new Field('Test', FieldType::FLOAT), 123, 123],
            'FLOAT - string' => [new Field('Test', FieldType::FLOAT), '123.4', 123.4],
            'DOUBLE - number' => [new Field('Test', FieldType::DOUBLE), 123.4, 123.4],
            'DOUBLE - int' => [new Field('Test', FieldType::DOUBLE), 123, 123],
            'DOUBLE - string' => [new Field('Test', FieldType::DOUBLE), '123.4', 123.4],
            'PRICE - 0' => [new Field('Test', FieldType::PRICE), 0, 0],
            'PRICE - integer' => [new Field('Test', FieldType::PRICE), 100, 100],
            'PRICE - decimal' => [new Field('Test', FieldType::PRICE), 100.23, 100.23],
            'PRICE - negative' => [new Field('Test', FieldType::PRICE), -100.23, -100.23],
            'PRICE - $' => [new Field('Test', FieldType::PRICE), '$1,000,100.34', 1000100.34],
            'LIST - String' => [(new Field('Test', FieldType::STRING))->setList(true), 'This is a string', "['This is a string']"],
            'LIST - Integer' => [(new Field('Test', FieldType::INTEGER))->setList(true), 123, '[123]'],
            'Data - Email' => [new Data($bundle->getBusinessModel('User'), 'email'), 'test@test.com', "'test@test.com'"],
            'DATASET' => [new Field('Test', 'UserStatus'), 'active', 'UserStatus::ACTIVE'],
       ];
    }

    /**
     * @dataProvider invalidValueProvider
     *
     * @param $obj
     * @param $value
     * @param $expectedMessage
     */
    public function testValueFilterWithUnsupportedTypeThrowsException($obj, $value, $expectedMessage)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedMessage);

        $this->twigExtension->valueFilter($this->context, $obj, $value);
    }

    public function invalidValueProvider()
    {
        $bundle = TestHelper::getSampleBusinessBundle();
        $user = $bundle->getBusinessModel('User');

        return [
            'BusinessModel Field Type' => [
                $user->getField('stats'),
                'value',
                'Cannot render a value for Business Model: UserStats',
            ],
            'Unknown Field Type' => [
                new Field('Test', 'Unknown'),
                'value',
                'Cannot render a value for field type: Unknown',
            ],
            'Unknown Dataset Value' => [
                new Field('Test', 'UserStatus'),
                'unknown',
                'Cannot find element unknown in Dataset UserStatus',
            ],
            'Unsupported Type' => [
                $user,
                'value',
                'Cannot render a value for class: CodePrimer\Model\BusinessModel',
            ],
        ];
    }
}
