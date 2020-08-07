<?php

namespace CodePrimer\Tests\Twig;

use CodePrimer\Helper\FieldType;
use CodePrimer\Model\ BusinessModel;
use CodePrimer\Model\BusinessBundle;
use CodePrimer\Model\Constraint;
use CodePrimer\Model\Derived\Event;
use CodePrimer\Model\Field;
use CodePrimer\Model\Set;
use CodePrimer\Model\State;
use CodePrimer\Model\StateMachine;
use CodePrimer\Model\Transition;
use CodePrimer\Tests\Helper\TestHelper;
use CodePrimer\Twig\LanguageTwigExtension;

class LanguageTwigExtensionTest extends TwigExtensionTest
{
    /** @var LanguageTwigExtension */
    protected $twigExtension;

    public function setUp(): void
    {
        parent::setUp();
        $this->twigExtension = new LanguageTwigExtension();
    }

    public function testGetFiltersShouldPass()
    {
        $filters = $this->twigExtension->getFilters();

        self::assertCount(20, $filters);

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
        $this->assertTwigFilter('parameter', $filters);
        $this->assertTwigFilter('type', $filters);
        $this->assertTwigFilter('listType', $filters);
        $this->assertTwigFilter('getter', $filters);
        $this->assertTwigFilter('setter', $filters);
        $this->assertTwigFilter('addMethod', $filters);
        $this->assertTwigFilter('removeMethod', $filters);
        $this->assertTwigFilter('containsMethod', $filters);
        $this->assertTwigFilter('yesNo', $filters);
    }

    public function testGetTestsShouldPass()
    {
        $tests = $this->twigExtension->getTests();

        self::assertCount(14, $tests);

        $this->assertTwigTest('scalar', $tests);
        $this->assertTwigTest('double', $tests);
        $this->assertTwigTest('float', $tests);
        $this->assertTwigTest('price', $tests);
        $this->assertTwigTest('long', $tests);
        $this->assertTwigTest('integer', $tests);
        $this->assertTwigTest('boolean', $tests);
        $this->assertTwigTest('string', $tests);
        $this->assertTwigTest('uuid', $tests);
        $this->assertTwigTest('entity', $tests);
        $this->assertTwigTest('oneToOne', $tests);
        $this->assertTwigTest('oneToMany', $tests);
        $this->assertTwigTest('manyToOne', $tests);
        $this->assertTwigTest('manyToMany', $tests);
    }

    /**
     * @dataProvider pluralDataProvider
     *
     * @param mixed  $obj           Object to filter
     * @param string $expectedValue expected filtered value
     */
    public function testPluralFilterShouldPass($obj, $expectedValue)
    {
        $value = $this->twigExtension->pluralFilter($obj);

        self::assertEquals($expectedValue, $value);
    }

    public function pluralDataProvider()
    {
        return [
            ['Table', 'Tables'],
            ['Tables', 'Tables'],
            ['Table_Element', 'Table_Elements'],
            ['field', 'fields'],
            ['field_name', 'field_names'],
            [new BusinessModel('Entity'), 'Entities'],
            [new Field('Field', 'int'), 'Fields'],
            [new BusinessBundle('Package', 'Name'), 'Names'],
            [new Event('Name', 'Code'), 'Names'],
            [new Set('Name', 'Description'), 'Names'],
            [new StateMachine('Name'), 'Names'],
            [new State('Name', 'Description'), 'Names'],
            [new Transition('Name', 'Description', new State('fromState'), new State('ToState')), 'Names'],
            [new Constraint('Name'), 'Names'],
            [123, 123],
            [1.345, 1.345],
            [null, null],
            ['', ''],
        ];
    }

    /**
     * @dataProvider singularDataProvider
     *
     * @param mixed  $obj           Object to filter
     * @param string $expectedValue expected filtered value
     */
    public function testSingularFilterShouldPass($obj, $expectedValue)
    {
        $value = $this->twigExtension->singularFilter($obj);

        self::assertEquals($expectedValue, $value);
    }

    public function singularDataProvider()
    {
        return [
            ['Tables', 'Table'],
            ['Table', 'Table'],
            ['Table_Elements', 'Table_Element'],
            ['fields', 'field'],
            ['field_names', 'field_name'],
            [new BusinessModel('Entities'), 'Entity'],
            [new Field('Fields', 'int'), 'Field'],
            [new BusinessBundle('Packages', 'Name'), 'Name'],
            [new Event('Names', 'Code'), 'Name'],
            [new Set('Names', 'Description'), 'Name'],
            [new StateMachine('Names'), 'Name'],
            [new State('Names', 'Description'), 'Name'],
            [new Transition('Names', 'Description', new State('fromState'), new State('ToState')), 'Name'],
            [new Constraint('Names'), 'Name'],
            [123, 123],
            [1.345, 1.345],
            [null, null],
            ['', ''],
        ];
    }

    /**
     * @dataProvider wordsDataProvider
     *
     * @param mixed  $obj           Object to filter
     * @param string $expectedValue expected filtered value
     */
    public function testWordsFilterShouldPass($obj, $expectedValue)
    {
        $value = $this->twigExtension->wordsFilter($obj);

        self::assertEquals($expectedValue, $value);
    }

    public function wordsDataProvider()
    {
        return [
            ['Tables', 'Tables'],
            ['Table', 'Table'],
            ['Table_Elements', 'Table Elements'],
            ['field_name', 'field name'],
            ['field.name', 'field.name'],
            ['field-name', 'field-name'],
            [new BusinessModel('Entities'), 'Entities'],
            [new Field('MYSQL_Field', 'int'), 'MYSQL Field'],
            [new BusinessBundle('Packages', 'Package_name'), 'Package name'],
            [new Event('valueNames', 'Event'), 'valueNames'],
            [123, 123],
            [1.345, 1.345],
            [null, null],
            ['', ''],
        ];
    }

    /**
     * @dataProvider camelDataProvider
     *
     * @param mixed  $obj           Object to filter
     * @param string $expectedValue expected filtered value
     */
    public function testCamelFilterShouldPass($obj, $expectedValue)
    {
        $value = $this->twigExtension->camelFilter($obj);

        self::assertEquals($expectedValue, $value);
    }

    public function camelDataProvider()
    {
        return [
            ['Tables', 'tables'],
            ['Table', 'table'],
            ['Table_Elements', 'tableElements'],
            ['field_name', 'fieldName'],
            ['field.name', 'field.name'],
            ['field-name', 'fieldName'],
            [new BusinessModel('Entities'), 'entities'],
            [new Field('MYSQL_Field', 'int'), 'mYSQLField'],
            [new BusinessBundle('Packages', 'Package_name'), 'packageName'],
            [new Event('valueNames', 'Event'), 'valueNames'],
            [123, 123],
            [1.345, 1.345],
            [null, null],
            ['', ''],
        ];
    }

    /**
     * @dataProvider underscoreDataProvider
     *
     * @param mixed  $obj           Object to filter
     * @param string $expectedValue expected filtered value
     */
    public function testUnderscoreFilterShouldPass($obj, $expectedValue)
    {
        $value = $this->twigExtension->underscoreFilter($obj);

        self::assertEquals($expectedValue, $value);
    }

    public function underscoreDataProvider()
    {
        return [
            ['Tables', 'tables'],
            ['Table', 'table'],
            ['Table Elements', 'table_elements'],
            ['tableElements', 'table_elements'],
            ['Table_Elements', 'table_elements'],
            ['fieldName', 'field_name'],
            ['field_name', 'field_name'],
            ['field.name', 'field_name'],
            ['field-name', 'field_name'],
            [new BusinessModel('Entities'), 'entities'],
            [new Field('MYSQL_Field', 'int'), 'm_y_s_q_l_field'],
            [new BusinessBundle('Packages', 'Package_name'), 'package_name'],
            [new Event('valueNames', 'Event'), 'value_names'],
            [123, 123],
            [1.345, 1.345],
            [null, null],
            ['', ''],
        ];
    }

    /**
     * @dataProvider classDataProvider
     *
     * @param mixed  $obj           Object to filter
     * @param string $expectedValue expected filtered value
     */
    public function testClassFilterShouldPass($obj, $expectedValue)
    {
        $value = $this->twigExtension->classFilter($obj);

        self::assertEquals($expectedValue, $value);
    }

    public function classDataProvider()
    {
        return [
            ['Tables', 'Tables'],
            ['Table', 'Table'],
            ['Table_Elements', 'TableElements'],
            ['field_name', 'FieldName'],
            ['field.name', 'Field.name'],
            ['field-name', 'FieldName'],
            ['class name', 'ClassName'],
            [new  BusinessModel('Entities'), 'Entities'],
            [new Field('MYSQL_Field', 'int'), 'MYSQLField'],
            [new BusinessBundle('Packages', 'Package_name'), 'PackageName'],
            [new Event('valueNames', 'Event'), 'ValueNames'],
            [123, 123],
            [1.345, 1.345],
            [null, null],
            ['', ''],
        ];
    }

    /**
     * @dataProvider constantDataProvider
     *
     * @param mixed  $obj           Object to filter
     * @param string $expectedValue expected filtered value
     */
    public function testConstantFilterShouldPass($obj, $expectedValue)
    {
        $value = $this->twigExtension->constantFilter($obj);

        self::assertEquals($expectedValue, $value);
    }

    public function constantDataProvider()
    {
        return [
            ['Tables', 'TABLES'],
            ['Table', 'TABLE'],
            ['Table_Elements', 'TABLE_ELEMENTS'],
            ['field_name', 'FIELD_NAME'],
            ['field.name', 'FIELD.NAME'],
            ['field-name', 'FIELD_NAME'],
            ['field name', 'FIELD_NAME'],
            [new BusinessModel('Entities'), 'ENTITIES'],
            [new Field('MYSQL_Field', 'int'), 'MYSQL_FIELD'],
            [new BusinessBundle('Packages', 'Package_name'), 'PACKAGE_NAME'],
            [new Event('valueNames', 'Event'), 'VALUENAMES'],
            [123, 123],
            [1.345, 1.345],
            [null, null],
            ['', ''],
        ];
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

    /**
     * @dataProvider variableDataProvider
     *
     * @param mixed  $obj           Object to filter
     * @param string $expectedValue expected filtered value
     */
    public function testMemberFilterShouldPass($obj, $expectedValue)
    {
        $value = $this->twigExtension->memberFilter($obj);

        self::assertEquals($expectedValue, $value);
    }

    public function variableDataProvider()
    {
        return [
            ['Tables', 'tables'],
            ['Table', 'table'],
            ['Table_Elements', 'tableElements'],
            ['field_name', 'fieldName'],
            ['field.name', 'field.name'],
            ['field-name', 'fieldName'],
            ['class name', 'className'],
            [new BusinessModel('Entities'), 'entities'],
            [new Field('MYSQL_Field', 'int'), 'mYSQLField'],
            [new BusinessBundle('Packages', 'Package_name'), 'packageName'],
            [new Event('valueNames', 'Event'), 'valueNames'],
            [123, 123],
            [1.345, 1.345],
            [null, null],
            ['', ''],
        ];
    }

    /**
     * @dataProvider pathDataProvider
     *
     * @param mixed  $obj           Object to filter
     * @param string $expectedValue expected filtered value
     */
    public function testPathFilterShouldPass($obj, $expectedValue)
    {
        $value = $this->twigExtension->pathFilter($obj);

        self::assertEquals($expectedValue, $value);
    }

    public function pathDataProvider()
    {
        return [
            ['Company\\Table', 'Company/Table'],
            ['Company/Table', 'Company/Table'],
            ['Company.Table', 'Company/Table'],
            ['company/table', 'company/table'],
            ['Table_Elements', 'Table_Elements'],
            ['field_name', 'field_name'],
            ['field.name', 'field/name'],
            ['field-name', 'field-name'],
            ['class name', 'class/name'],
            [new BusinessModel('Entities'), ''],
            [new Field('MYSQL/Field', 'int'), ''],
            [new BusinessBundle('Packages', 'Package_name'), 'Packages'],
            [new Event('Event\\Names', 'Event'), ''],
            [123, ''],
            [1.345, ''],
            [null, ''],
            ['', ''],
        ];
    }

    /**
     * @dataProvider lastPathDataProvider
     *
     * @param mixed  $obj           Object to filter
     * @param string $expectedValue expected filtered value
     */
    public function testLastPathFilterShouldPass($obj, $expectedValue)
    {
        $value = $this->twigExtension->lastPathFilter($obj);

        self::assertEquals($expectedValue, $value);
    }

    public function lastPathDataProvider()
    {
        return [
            ['Company\\Table', 'Table'],
            ['Company/Table', 'Table'],
            ['Company.Table', 'Table'],
            ['company/table', 'table'],
            ['Table_Elements', 'Table_Elements'],
            ['field_name', 'field_name'],
            ['field.name', 'name'],
            ['field-name', 'field-name'],
            ['class name', 'class name'],
            [new BusinessModel('Entities'), ''],
            [new Field('MYSQL/Field', 'int'), ''],
            [new BusinessBundle('Packages', 'Package_name'), 'Packages'],
            [new Event('Event\\Names', 'Event'), ''],
            [123, ''],
            [1.345, ''],
            [null, ''],
            ['', ''],
        ];
    }

    /**
     * @dataProvider getterDataProvider
     *
     * @param mixed  $obj           Object to filter
     * @param string $expectedValue expected filtered value
     */
    public function testGetterFilterShouldPass($obj, $expectedValue)
    {
        $value = $this->twigExtension->getterFilter($obj);

        self::assertEquals($expectedValue, $value);
    }

    public function getterDataProvider()
    {
        return [
            ['Tables', 'getTables()'],
            ['Table', 'getTable()'],
            ['Table_Elements', 'getTableElements()'],
            ['field_name', 'getFieldName()'],
            ['field.name', 'getField.name()'],
            ['field-name', 'getFieldName()'],
            ['class name', 'getClassName()'],
            [new BusinessModel('Entities'), 'getEntities()'],
            [new Field('MYSQL_Field', 'int'), 'getMYSQLField()'],
            [new BusinessBundle('Packages', 'Package_name'), 'getPackageName()'],
            [new Event('valueNames', 'Event'), 'getValueNames()'],
            [123, 123],
            [1.345, 1.345],
            [null, null],
            ['', ''],
        ];
    }

    /**
     * @dataProvider setterDataProvider
     *
     * @param mixed  $obj           Object to filter
     * @param string $expectedValue expected filtered value
     */
    public function testSetterFilterShouldPass($obj, $expectedValue)
    {
        $value = $this->twigExtension->setterFilter($obj);

        self::assertEquals($expectedValue, $value);
    }

    public function setterDataProvider()
    {
        return [
            ['Tables', 'setTables'],
            ['Table', 'setTable'],
            ['Table_Elements', 'setTableElements'],
            ['field_name', 'setFieldName'],
            ['field.name', 'setField.name'],
            ['field-name', 'setFieldName'],
            ['class name', 'setClassName'],
            [new BusinessModel('Entities'), 'setEntities'],
            [new Field('MYSQL_Field', 'int'), 'setMYSQLField'],
            [new BusinessBundle('Packages', 'Package_name'), 'setPackageName'],
            [new Event('valueNames', 'Event'), 'setValueNames'],
            [123, 123],
            [1.345, 1.345],
            [null, null],
            ['', ''],
        ];
    }

    /**
     * @dataProvider addMethodDataProvider
     *
     * @param mixed  $obj           Object to filter
     * @param string $expectedValue expected filtered value
     */
    public function testAddMethodFilterShouldPass($obj, $expectedValue)
    {
        $value = $this->twigExtension->addMethodFilter($obj);

        self::assertEquals($expectedValue, $value);
    }

    public function addMethodDataProvider()
    {
        return [
            ['Tables', 'addTable'],
            ['Table', 'addTable'],
            ['Table_Elements', 'addTableElement'],
            ['field_name', 'addFieldName'],
            ['field-name', 'addFieldName'],
            ['class name', 'addClassName'],
            [new BusinessModel('Entities'), 'addEntity'],
            [new Field('MYSQL_Field', 'int'), 'addMYSQLField'],
            [new BusinessBundle('Packages', 'Package_name'), 'addPackageName'],
            [new Event('valueNames', 'Event'), 'addValueName'],
            [123, 123],
            [1.345, 1.345],
            [null, null],
            ['', ''],
        ];
    }

    /**
     * @dataProvider removeMethodDataProvider
     *
     * @param mixed  $obj           Object to filter
     * @param string $expectedValue expected filtered value
     */
    public function testRemoveMethodFilterShouldPass($obj, $expectedValue)
    {
        $value = $this->twigExtension->removeMethodFilter($obj);

        self::assertEquals($expectedValue, $value);
    }

    public function removeMethodDataProvider()
    {
        return [
            ['Tables', 'removeTable'],
            ['Table', 'removeTable'],
            ['Table_Elements', 'removeTableElement'],
            ['field_name', 'removeFieldName'],
            ['field-name', 'removeFieldName'],
            ['class name', 'removeClassName'],
            [new BusinessModel('Entities'), 'removeEntity'],
            [new Field('MYSQL_Field', 'int'), 'removeMYSQLField'],
            [new BusinessBundle('Packages', 'Package_name'), 'removePackageName'],
            [new Event('valueNames', 'Event'), 'removeValueName'],
            [123, 123],
            [1.345, 1.345],
            [null, null],
            ['', ''],
        ];
    }

    /**
     * @dataProvider containsMethodDataProvider
     *
     * @param mixed  $obj           Object to filter
     * @param string $expectedValue expected filtered value
     */
    public function testContainsMethodFilterShouldPass($obj, $expectedValue)
    {
        $value = $this->twigExtension->containsMethodFilter($obj);

        self::assertEquals($expectedValue, $value);
    }

    public function containsMethodDataProvider()
    {
        return [
            ['Tables', 'containsTable'],
            ['Table', 'containsTable'],
            ['Table_Elements', 'containsTableElement'],
            ['field_name', 'containsFieldName'],
            ['field-name', 'containsFieldName'],
            ['class name', 'containsClassName'],
            [new BusinessModel('Entities'), 'containsEntity'],
            [new Field('MYSQL_Field', 'int'), 'containsMYSQLField'],
            [new BusinessBundle('Packages', 'Package_name'), 'containsPackageName'],
            [new Event('valueNames', 'Event'), 'containsValueName'],
            [123, 123],
            [1.345, 1.345],
            [null, null],
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
            'BOOL' => [new Field('Test', FieldType::BOOL, 'Test Description', true), 'bool'],
            'BOOLEAN' => [new Field('Test', FieldType::BOOLEAN, 'Test Description', true), 'bool'],
            'DATE' => [new Field('Test', FieldType::DATE, 'Test Description', true), 'date'],
            'DATETIME' => [new Field('Test', FieldType::DATETIME, 'Test Description', true), 'datetime'],
            'DECIMAL' => [new Field('Test', FieldType::DECIMAL, 'Test Description', true), 'double'],
            'DOUBLE' => [new Field('Test', FieldType::DOUBLE, 'Test Description', true), 'double'],
            'EMAIL' => [new Field('Test', FieldType::EMAIL, 'Test Description', true), 'string'],
            'FLOAT' => [new Field('Test', FieldType::FLOAT, 'Test Description', true), 'float'],
            'ID' => [new Field('Test', FieldType::ID, 'Test Description', true), 'long'],
            'INT' => [new Field('Test', FieldType::INT, 'Test Description', true), 'int'],
            'INTEGER' => [new Field('Test', FieldType::INTEGER, 'Test Description', true), 'int'],
            'LONG' => [new Field('Test', FieldType::LONG, 'Test Description', true), 'long'],
            'PASSWORD' => [new Field('Test', FieldType::PASSWORD, 'Test Description', true), 'string'],
            'PHONE' => [new Field('Test', FieldType::PHONE, 'Test Description', true), 'string'],
            'PRICE' => [new Field('Test', FieldType::PRICE, 'Test Description', true), 'double'],
            'RANDOM_STRING' => [new Field('Test', FieldType::RANDOM_STRING, 'Test Description', true), 'string'],
            'STRING' => [new Field('Test', FieldType::STRING, 'Test Description', true), 'string'],
            'TEXT' => [new Field('Test', FieldType::TEXT, 'Test Description', true), 'string'],
            'TIME' => [new Field('Test', FieldType::TIME, 'Test Description', true), 'time'],
            'URL' => [new Field('Test', FieldType::URL, 'Test Description', true), 'string'],
            'UUID' => [new Field('Test', FieldType::UUID, 'Test Description', true), 'string'],
            'UNKNOWN' => [new Field('Test', 'Unknown', 'Test Description', true), 'string'],
            'BOOL ARRAY' => [
                (new Field('Test', FieldType::BOOL, 'Test Description', true))
                    ->setList(true),
                'list of bool',
            ],
            'ENTITY' => [new Field('Test', 'User', 'Test Description', true), 'User'],
            'OPTIONAL ENTITY' => [new Field('Test', 'User'), 'User (Optional)'],
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
                'date',
            ],
            'DATETIME' => [
                (new Field('Test', FieldType::DATETIME, 'Test Description', true))
                    ->setList(true),
                'datetime',
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
                'long',
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
                'long',
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
                'double',
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
                'time',
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
     * @dataProvider yesNoProvider
     */
    public function testYesNoFilterShouldPass($obj, $expectedValue)
    {
        $value = $this->twigExtension->yesNoFilter($obj);

        self::assertEquals($expectedValue, $value);
    }

    public function yesNoProvider()
    {
        return [
            'false' => [false, 'no'],
            'true' => [true, 'yes'],
            'false (string)' => ['false', 'no'],
            'true (string)' => ['true', 'yes'],
            'no' => ['no', 'no'],
            'yes' => ['yes', 'yes'],
            'NO' => ['NO', 'no'],
            'YES' => ['YES', 'yes'],
            '0' => [0, 'no'],
            '1' => [1, 'yes'],
            '2' => [2, 'yes'],
            'unknown' => ['unknown', 'N/A'],
        ];
    }

    /**
     * @dataProvider scalarTestProvider
     *
     * @param $expectedValue
     */
    public function testScalarTest(Field $field, $expectedValue)
    {
        $value = $this->twigExtension->scalarTest($field);

        self::assertEquals($expectedValue, $value);
    }

    public function scalarTestProvider()
    {
        return [
            'BOOL' => [new Field('Test', FieldType::BOOL), true],
            'BOOLEAN' => [new Field('Test', FieldType::BOOLEAN), true],
            'DATE' => [new Field('Test', FieldType::DATE), false],
            'DATETIME' => [new Field('Test', FieldType::DATETIME), false],
            'DECIMAL' => [new Field('Test', FieldType::DECIMAL), true],
            'DOUBLE' => [new Field('Test', FieldType::DOUBLE), true],
            'EMAIL' => [new Field('Test', FieldType::EMAIL), false],
            'FLOAT' => [new Field('Test', FieldType::FLOAT), true],
            'ID' => [new Field('Test', FieldType::ID), true],
            'INT' => [new Field('Test', FieldType::INT), true],
            'INTEGER' => [new Field('Test', FieldType::INTEGER), true],
            'LONG' => [new Field('Test', FieldType::LONG), true],
            'PASSWORD' => [new Field('Test', FieldType::PASSWORD), false],
            'PHONE' => [new Field('Test', FieldType::PHONE), false],
            'PRICE' => [new Field('Test', FieldType::PRICE), true],
            'RANDOM_STRING' => [new Field('Test', FieldType::RANDOM_STRING), false],
            'STRING' => [new Field('Test', FieldType::STRING), false],
            'TEXT' => [new Field('Test', FieldType::TEXT), false],
            'TIME' => [new Field('Test', FieldType::TIME), false],
            'URL' => [new Field('Test', FieldType::URL), false],
            'UUID' => [new Field('Test', FieldType::UUID), false],
            'UNKNOWN' => [new Field('Test', 'Unknown'), false],
        ];
    }

    /**
     * @dataProvider doubleTestProvider
     *
     * @param $expectedValue
     */
    public function testDoubleTest(Field $field, $expectedValue)
    {
        $value = $this->twigExtension->doubleTest($field);

        self::assertEquals($expectedValue, $value);
    }

    public function doubleTestProvider()
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
            'UNKNOWN' => [new Field('Test', 'Unknown'), false],
        ];
    }

    /**
     * @dataProvider floatTestProvider
     *
     * @param $expectedValue
     */
    public function testFloatTest(Field $field, $expectedValue)
    {
        $value = $this->twigExtension->floatTest($field);

        self::assertEquals($expectedValue, $value);
    }

    public function floatTestProvider()
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
            'UNKNOWN' => [new Field('Test', 'Unknown'), false],
        ];
    }

    /**
     * @dataProvider priceTestProvider
     *
     * @param $expectedValue
     */
    public function testPriceTest(Field $field, $expectedValue)
    {
        $value = $this->twigExtension->priceTest($field);

        self::assertEquals($expectedValue, $value);
    }

    public function priceTestProvider()
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
            'UNKNOWN' => [new Field('Test', 'Unknown'), false],
        ];
    }

    /**
     * @dataProvider longTestProvider
     *
     * @param $expectedValue
     */
    public function testLongTest(Field $field, $expectedValue)
    {
        $value = $this->twigExtension->longTest($field);

        self::assertEquals($expectedValue, $value);
    }

    public function longTestProvider()
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
            'UNKNOWN' => [new Field('Test', 'Unknown'), false],
        ];
    }

    /**
     * @dataProvider integerTestProvider
     *
     * @param $expectedValue
     */
    public function testIntegerTest(Field $field, $expectedValue)
    {
        $value = $this->twigExtension->integerTest($field);

        self::assertEquals($expectedValue, $value);
    }

    public function integerTestProvider()
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
            'UNKNOWN' => [new Field('Test', 'Unknown'), false],
        ];
    }

    /**
     * @dataProvider booleanTestProvider
     *
     * @param $expectedValue
     */
    public function testBooleanTest(Field $field, $expectedValue)
    {
        $value = $this->twigExtension->booleanTest($field);

        self::assertEquals($expectedValue, $value);
    }

    public function booleanTestProvider()
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
            'UNKNOWN' => [new Field('Test', 'Unknown'), false],
        ];
    }

    /**
     * @dataProvider stringTestProvider
     *
     * @param $expectedValue
     */
    public function testStringTest(Field $field, $expectedValue)
    {
        $value = $this->twigExtension->stringTest($field);

        self::assertEquals($expectedValue, $value);
    }

    public function stringTestProvider()
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
            'UNKNOWN' => [new Field('Test', 'Unknown'), false],
        ];
    }

    /**
     * @dataProvider uuidTestProvider
     *
     * @param $expectedValue
     */
    public function testUuidTest(Field $field, $expectedValue)
    {
        $value = $this->twigExtension->uuidTest($field);

        self::assertEquals($expectedValue, $value);
    }

    public function uuidTestProvider()
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
            'UNKNOWN' => [new Field('Test', 'Unknown'), false],
        ];
    }

    /**
     * @dataProvider businessModelTestProvider
     *
     * @param $expectedValue
     */
    public function testBusinessModelTest(Field $field, $expectedValue)
    {
        $businessBundle = TestHelper::getSampleBusinessBundle();

        $value = $this->twigExtension->entityTest($field, $businessBundle);

        self::assertEquals($expectedValue, $value);
    }

    public function businessModelTestProvider()
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
            'User' => [new Field('Test', 'User'), true],
            'UserStats' => [new Field('Test', 'UserStats'), true],
            'Metadata' => [new Field('Test', 'Metadata'), true],
        ];
    }

    /**
     * @dataProvider oneToOneTestProvider
     *
     * @param mixed $obj
     */
    public function testOneToOneTest($obj, bool $expectedValue)
    {
        $value = $this->twigExtension->oneToOneTest($obj);

        self::assertEquals($expectedValue, $value);
    }

    public function oneToOneTestProvider()
    {
        $businessBundle = TestHelper::getSampleBusinessBundle();
        $user = $businessBundle->getBusinessModel('User');
        $post = $businessBundle->getBusinessModel('Post');
        $topic = $businessBundle->getBusinessModel('Topic');

        return [
            'ENTITY - No relation' => [$post, false],
            'ENTITY - With relation' => [$user, true],
            'FIELD - No relation' => [$user->getField('id'), false],
            'FIELD - OneToOne relation' => [$user->getField('stats'), true],
            'FIELD - OneToMany relation' => [$user->getField('posts'), false],
            'FIELD - ManyToOne relation' => [$post->getField('author'), false],
            'FIELD - ManyToMany relation' => [$user->getField('topics'), false],
            'FIELD - ManyToMany relation 2' => [$topic->getField('authors'), false],
            'RELATIONSHIP_SIDE - OneToOne relation' => [$user->getField('stats')->getRelation(), true],
            'RELATIONSHIP_SIDE - OneToMany relation' => [$user->getField('posts')->getRelation(), false],
            'RELATIONSHIP_SIDE - ManyToOne relation' => [$post->getField('author')->getRelation(), false],
            'RELATIONSHIP_SIDE - ManyToMany relation' => [$user->getField('topics')->getRelation(), false],
            'RELATIONSHIP_SIDE - ManyToMany relation 2' => [$topic->getField('authors')->getRelation(), false],
        ];
    }

    /**
     * @dataProvider oneToManyTestProvider
     *
     * @param mixed $obj
     */
    public function testOneToManyTest($obj, bool $expectedValue)
    {
        $value = $this->twigExtension->oneToManyTest($obj);

        self::assertEquals($expectedValue, $value);
    }

    public function oneToManyTestProvider()
    {
        $businessBundle = TestHelper::getSampleBusinessBundle();
        $user = $businessBundle->getBusinessModel('User');
        $post = $businessBundle->getBusinessModel('Post');
        $topic = $businessBundle->getBusinessModel('Topic');

        return [
            'ENTITY - No relation' => [$post, false],
            'ENTITY - With relation' => [$user, true],
            'FIELD - No relation' => [$user->getField('id'), false],
            'FIELD - OneToOne relation' => [$user->getField('stats'), false],
            'FIELD - OneToMany relation' => [$user->getField('posts'), true],
            'FIELD - ManyToOne relation' => [$post->getField('author'), false],
            'FIELD - ManyToMany relation' => [$user->getField('topics'), false],
            'FIELD - ManyToMany relation 2' => [$topic->getField('authors'), false],
            'RELATIONSHIP_SIDE - OneToOne relation' => [$user->getField('stats')->getRelation(), false],
            'RELATIONSHIP_SIDE - OneToMany relation' => [$user->getField('posts')->getRelation(), true],
            'RELATIONSHIP_SIDE - ManyToOne relation' => [$post->getField('author')->getRelation(), false],
            'RELATIONSHIP_SIDE - ManyToMany relation' => [$user->getField('topics')->getRelation(), false],
            'RELATIONSHIP_SIDE - ManyToMany relation 2' => [$topic->getField('authors')->getRelation(), false],
        ];
    }

    /**
     * @dataProvider manyToOneTestProvider
     *
     * @param mixed $obj
     */
    public function testManyToOneTest($obj, bool $expectedValue)
    {
        $value = $this->twigExtension->manyToOneTest($obj);

        self::assertEquals($expectedValue, $value);
    }

    public function manyToOneTestProvider()
    {
        $businessBundle = TestHelper::getSampleBusinessBundle();
        $user = $businessBundle->getBusinessModel('User');
        $post = $businessBundle->getBusinessModel('Post');
        $topic = $businessBundle->getBusinessModel('Topic');

        return [
            'ENTITY - No relation' => [$user, false],
            'ENTITY - With relation' => [$post, true],
            'FIELD - No relation' => [$user->getField('id'), false],
            'FIELD - OneToOne relation' => [$user->getField('stats'), false],
            'FIELD - OneToMany relation' => [$user->getField('posts'), false],
            'FIELD - ManyToOne relation' => [$post->getField('author'), true],
            'FIELD - ManyToMany relation' => [$user->getField('topics'), false],
            'FIELD - ManyToMany relation 2' => [$topic->getField('authors'), false],
            'RELATIONSHIP_SIDE - OneToOne relation' => [$user->getField('stats')->getRelation(), false],
            'RELATIONSHIP_SIDE - OneToMany relation' => [$user->getField('posts')->getRelation(), false],
            'RELATIONSHIP_SIDE - ManyToOne relation' => [$post->getField('author')->getRelation(), true],
            'RELATIONSHIP_SIDE - ManyToMany relation' => [$user->getField('topics')->getRelation(), false],
            'RELATIONSHIP_SIDE - ManyToMany relation 2' => [$topic->getField('authors')->getRelation(), false],
        ];
    }

    /**
     * @dataProvider manyToManyTestProvider
     *
     * @param mixed $obj
     */
    public function testManyToManyTest($obj, bool $expectedValue)
    {
        $value = $this->twigExtension->manyToManyTest($obj);

        self::assertEquals($expectedValue, $value);
    }

    public function manyToManyTestProvider()
    {
        $businessBundle = TestHelper::getSampleBusinessBundle();
        $user = $businessBundle->getBusinessModel('User');
        $post = $businessBundle->getBusinessModel('Post');
        $topic = $businessBundle->getBusinessModel('Topic');

        return [
            'ENTITY - No relation' => [$post, false],
            'ENTITY - With relation' => [$user, true],
            'FIELD - No relation' => [$user->getField('id'), false],
            'FIELD - OneToOne relation' => [$user->getField('stats'), false],
            'FIELD - OneToMany relation' => [$user->getField('posts'), false],
            'FIELD - ManyToOne relation' => [$post->getField('author'), false],
            'FIELD - ManyToMany relation' => [$user->getField('topics'), true],
            'FIELD - ManyToMany relation 2' => [$topic->getField('authors'), true],
            'RELATIONSHIP_SIDE - OneToOne relation' => [$user->getField('stats')->getRelation(), false],
            'RELATIONSHIP_SIDE - OneToMany relation' => [$user->getField('posts')->getRelation(), false],
            'RELATIONSHIP_SIDE - ManyToOne relation' => [$post->getField('author')->getRelation(), false],
            'RELATIONSHIP_SIDE - ManyToMany relation' => [$user->getField('topics')->getRelation(), true],
            'RELATIONSHIP_SIDE - ManyToMany relation 2' => [$topic->getField('authors')->getRelation(), true],
        ];
    }
}
