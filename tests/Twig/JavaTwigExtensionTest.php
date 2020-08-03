<?php

namespace CodePrimer\Tests\Twig;

use CodePrimer\Helper\FieldType;
use CodePrimer\Model\BusinessModel;
use CodePrimer\Model\Derived\Event;
use CodePrimer\Model\Field;
use CodePrimer\Model\Package;
use CodePrimer\Twig\JavaTwigExtension;

class JavaTwigExtensionTest extends TwigExtensionTest
{
    /** @var JavaTwigExtension */
    private $twigExtension;

    public function setUp(): void
    {
        parent::setUp();
        $this->twigExtension = new JavaTwigExtension();
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
        $this->assertTwigFilter('package', $filters);
        $this->assertTwigFilter('type', $filters);
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
            ['Tables', 'this.table'],
            ['Table', 'this.table'],
            ['Table_Elements', 'this.tableElement'],
            ['field_name', 'this.fieldName'],
            ['field.name', 'this.field.name'],
            ['field-name', 'this.fieldName'],
            ['class name', 'this.className'],
            [new BusinessModel('Entities'), 'this.entity'],
            [new Field('MYSQL_Field', 'int'), 'this.mYSQLField'],
            [new Package('Packages', 'Package_name'), 'this.packageName'],
            [new Event('eventNames', 'Value'), 'this.eventName'],
            [123, 123],
            [1.345, 1.345],
            [null, null],
            ['', ''],
        ];
    }

    /**
     * @dataProvider packageDataProvider
     *
     * @param mixed  $obj           Object to filter
     * @param string $expectedValue expected filtered value
     */
    public function testPackageFilterShouldPass($obj, $expectedValue)
    {
        $value = $this->twigExtension->packageFilter($obj);

        self::assertEquals($expectedValue, $value);
    }

    public function packageDataProvider()
    {
        return [
            ['Company\\Table', 'company.table'],
            ['Company/Table', 'company.table'],
            ['Company.Table', 'company.table'],
            ['company/table', 'company.table'],
            ['Table_Elements', 'table_elements'],
            ['field_name', 'field_name'],
            ['field.name', 'field.name'],
            ['field-name', 'field-name'],
            ['class name', 'class.name'],
            [new BusinessModel('Entities'), ''],
            [new Field('MYSQL/Field', 'int'), ''],
            [new Package('Packages', 'Package_name'), 'packages'],
            [new Event('Event\\Names', 'Value'), ''],
            [123, ''],
            [1.345, ''],
            [null, ''],
            ['', ''],
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
            'BOOL' => [new Field('Test', FieldType::BOOL), 'boolean'],
            'BOOLEAN' => [new Field('Test', FieldType::BOOLEAN), 'boolean'],
            'DATE' => [new Field('Test', FieldType::DATE), 'Date'],
            'DATETIME' => [new Field('Test', FieldType::DATETIME), 'Date'],
            'DECIMAL' => [new Field('Test', FieldType::DECIMAL), 'double'],
            'DOUBLE' => [new Field('Test', FieldType::DOUBLE), 'double'],
            'EMAIL' => [new Field('Test', FieldType::EMAIL), 'String'],
            'FLOAT' => [new Field('Test', FieldType::FLOAT), 'float'],
            'ID' => [new Field('Test', FieldType::ID), 'long'],
            'INT' => [new Field('Test', FieldType::INT), 'int'],
            'INTEGER' => [new Field('Test', FieldType::INTEGER), 'int'],
            'LONG' => [new Field('Test', FieldType::LONG), 'long'],
            'PASSWORD' => [new Field('Test', FieldType::PASSWORD), 'String'],
            'PHONE' => [new Field('Test', FieldType::PHONE), 'String'],
            'PRICE' => [new Field('Test', FieldType::PRICE), 'double'],
            'RANDOM_STRING' => [new Field('Test', FieldType::RANDOM_STRING), 'String'],
            'STRING' => [new Field('Test', FieldType::STRING), 'String'],
            'TEXT' => [new Field('Test', FieldType::TEXT), 'String'],
            'TIME' => [new Field('Test', FieldType::TIME), 'long'],
            'URL' => [new Field('Test', FieldType::URL), 'String'],
            'UUID' => [new Field('Test', FieldType::UUID), 'String'],
            'UNKNOWN' => [new Field('Test', 'Unknown'), 'Object'],
            'STRING ARRAY' => [
                (new Field('Test', FieldType::STRING, 'Test Description', true))
                    ->setList(true),
                'List<String>',
            ],
            'ENTITY' => [new Field('Test', 'User', 'Test Description', true), 'User'],
            'OPTIONAL ENTITY' => [new Field('Test', 'User'), 'User'],
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
                'Boolean',
            ],
            'BOOLEAN' => [
                (new Field('Test', FieldType::BOOLEAN, 'Test Description', true))
                    ->setList(true),
                'Boolean',
            ],
            'DATE' => [
                (new Field('Test', FieldType::DATE, 'Test Description', true))
                    ->setList(true),
                'Date',
            ],
            'DATETIME' => [
                (new Field('Test', FieldType::DATETIME, 'Test Description', true))
                    ->setList(true),
                'Date',
            ],
            'DECIMAL' => [
                (new Field('Test', FieldType::DECIMAL, 'Test Description', true))
                    ->setList(true),
                'Double',
            ],
            'DOUBLE' => [
                (new Field('Test', FieldType::DOUBLE, 'Test Description', true))
                    ->setList(true),
                'Double',
            ],
            'EMAIL' => [
                (new Field('Test', FieldType::EMAIL, 'Test Description', true))
                    ->setList(true),
                'String',
            ],
            'FLOAT' => [
                (new Field('Test', FieldType::FLOAT, 'Test Description', true))
                    ->setList(true),
                'Float',
            ],
            'ID' => [
                (new Field('Test', FieldType::ID, 'Test Description', true))
                    ->setList(true),
                'Long',
            ],
            'INT' => [
                (new Field('Test', FieldType::INT, 'Test Description', true))
                    ->setList(true),
                'Integer',
            ],
            'INTEGER' => [
                (new Field('Test', FieldType::INTEGER, 'Test Description', true))
                    ->setList(true),
                'Integer',
            ],
            'LONG' => [
                (new Field('Test', FieldType::LONG, 'Test Description', true))
                    ->setList(true),
                'Long',
            ],
            'PASSWORD' => [
                (new Field('Test', FieldType::PASSWORD, 'Test Description', true))
                    ->setList(true),
                'String',
            ],
            'PHONE' => [
                (new Field('Test', FieldType::PHONE, 'Test Description', true))
                    ->setList(true),
                'String',
            ],
            'PRICE' => [
                (new Field('Test', FieldType::PRICE, 'Test Description', true))
                    ->setList(true),
                'Double',
            ],
            'RANDOM_STRING' => [
                (new Field('Test', FieldType::RANDOM_STRING, 'Test Description', true))
                    ->setList(true),
                'String',
            ],
            'STRING' => [
                (new Field('Test', FieldType::STRING, 'Test Description', true))
                    ->setList(true),
                'String',
            ],
            'TEXT' => [
                (new Field('Test', FieldType::TEXT, 'Test Description', true))
                    ->setList(true),
                'String',
            ],
            'TIME' => [
                (new Field('Test', FieldType::TIME, 'Test Description', true))
                    ->setList(true),
                'Long',
            ],
            'URL' => [
                (new Field('Test', FieldType::URL, 'Test Description', true))
                    ->setList(true),
                'String',
            ],
            'UUID' => [
                (new Field('Test', FieldType::UUID, 'Test Description', true))
                    ->setList(true),
                'String',
            ],
            'UNKNOWN' => [
                (new Field('Test', 'Unknown', 'Test Description', true))
                    ->setList(true),
                'Object',
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
}
