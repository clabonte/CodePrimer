<?php

namespace CodePrimer\Tests\Twig;

use CodePrimer\Adapter\RelationalDatabaseAdapter;
use CodePrimer\Helper\FieldType;
use CodePrimer\Model\BusinessModel;
use CodePrimer\Model\Database\Index;
use CodePrimer\Model\Field;
use CodePrimer\Tests\Helper\TestHelper;
use CodePrimer\Twig\MySqlTwigExtension;
use RuntimeException;

class MySqlTwigExtensionTest extends TwigExtensionTest
{
    /** @var MySqlTwigExtension */
    private $twigExtension;

    public function setUp(): void
    {
        parent::setUp();
        $this->twigExtension = new MySqlTwigExtension();
    }

    public function testGetFiltersShouldPass()
    {
        $filters = $this->twigExtension->getFilters();

        self::assertNotNull($filters);

        $this->assertTwigFilter('attributes', $filters);
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
        $package = TestHelper::getSamplePackage();
        $adapter = new RelationalDatabaseAdapter();
        $adapter->generateRelationalFields($package, FieldType::ID);

        $user = $package->getBusinessModel('User');
        $metadata = $package->getBusinessModel('Metadata');

        return [
            'BOOL' => [new Field('Test', FieldType::BOOL, 'Test Description', true), 'TINYINT(1)'],
            'BOOLEAN' => [new Field('Test', FieldType::BOOLEAN, 'Test Description', true), 'TINYINT(1)'],
            'DATE' => [new Field('Test', FieldType::DATE, 'Test Description', true), 'DATE'],
            'DATETIME' => [new Field('Test', FieldType::DATETIME, 'Test Description', true), 'DATETIME'],
            'DECIMAL' => [new Field('Test', FieldType::DECIMAL, 'Test Description', true), 'DECIMAL(14,4)'],
            'DOUBLE' => [new Field('Test', FieldType::DOUBLE, 'Test Description', true), 'DOUBLE'],
            'EMAIL' => [new Field('Test', FieldType::EMAIL, 'Test Description', true), 'VARCHAR(255)'],
            'FLOAT' => [new Field('Test', FieldType::FLOAT, 'Test Description', true), 'FLOAT'],
            'ID' => [new Field('Test', FieldType::ID, 'Test Description', true), 'BIGINT'],
            'INT' => [new Field('Test', FieldType::INT, 'Test Description', true), 'INT'],
            'INTEGER' => [new Field('Test', FieldType::INTEGER, 'Test Description', true), 'INT'],
            'LONG' => [new Field('Test', FieldType::LONG, 'Test Description', true), 'BIGINT'],
            'PASSWORD' => [new Field('Test', FieldType::PASSWORD, 'Test Description', true), 'VARCHAR(255)'],
            'PHONE' => [new Field('Test', FieldType::PHONE, 'Test Description', true), 'CHAR(15)'],
            'PRICE' => [new Field('Test', FieldType::PRICE, 'Test Description', true), 'DECIMAL(12,2)'],
            'RANDOM_STRING' => [new Field('Test', FieldType::RANDOM_STRING, 'Test Description', true), 'VARCHAR(255)'],
            'STRING' => [new Field('Test', FieldType::STRING, 'Test Description', true), 'VARCHAR(255)'],
            'TEXT' => [new Field('Test', FieldType::TEXT, 'Test Description', true), 'LONGTEXT'],
            'TIME' => [new Field('Test', FieldType::TIME, 'Test Description', true), 'TIME'],
            'URL' => [new Field('Test', FieldType::URL, 'Test Description', true), 'VARCHAR(255)'],
            'UUID' => [new Field('Test', FieldType::UUID, 'Test Description', true), 'CHAR(36)'],
            'EXISTING FOREIGN KEY' => [$metadata->getField('user'), 'CHAR(36)'],
            'GENERATED FOREIGN KEY' => [$user->getField('stats'), 'BIGINT'],
        ];
    }

    /**
     * @dataProvider unsupportedTypeDataProvider
     *
     * @param mixed $obj Object to filter
     */
    public function testUnsupportedTypeShouldThrowException($obj)
    {
        $this->expectException(RuntimeException::class);
        $this->twigExtension->typeFilter($this->context, $obj);
    }

    public function unsupportedTypeDataProvider()
    {
        $package = TestHelper::getSamplePackage();
        $adapter = new RelationalDatabaseAdapter();
        $adapter->generateRelationalFields($package);

        $user = $package->getBusinessModel('User');

        return [
            'UNKNOWN' => [new Field('Test', 'Unknown', 'Test Description', true)],
            'BOOL ARRAY' => [
                (new Field('Test', FieldType::BOOL, 'Test Description', true))
                    ->setList(true),
            ],
            'ENTITY FIELD' => [new Field('Test', 'User', 'Test Description', true)],
            'OPTIONAL ENTITY FIELD' => [new Field('Test', 'User')],
            'NON FIELD' => ['Invalid'],
            'ENTITY OBJECT' => [new BusinessModel(('Test'))],
            'MANY-TO-MANY FIELD' => [$user->getField('topics')],
            'ONE-TO-MANY FIELD' => [$user->getField('posts')],
        ];
    }

    /**
     * @dataProvider fieldAttributesDataProvider
     */
    public function testFieldAttributesFilterShouldPass(Field $field, string $expectedValue)
    {
        $value = $this->twigExtension->attributesFilter($field);

        self::assertEquals($expectedValue, $value);
    }

    public function fieldAttributesDataProvider()
    {
        return [
            'MANDATORY BOOL' => [new Field('Test', FieldType::BOOL, 'Test Description', true), "NOT NULL COMMENT 'Test Description'"],
            'OPTIONAL BOOL, NO DESCRIPTION' => [new Field('Test', FieldType::BOOLEAN, '', false), 'NULL DEFAULT NULL'],
            'CREATED FIELD' => [
                (new Field('created', FieldType::DATETIME, "Entity's created field", true))
                    ->setManaged(true),
                "NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Entity''s created field'",
                ],
            'UPDATED FIELD' => [
                (new Field('updated', FieldType::DATETIME, "Entity's updated field", false))
                    ->setManaged(true),
                "NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Entity''s updated field'",
            ],
            'ID' => [new Field('id', FieldType::ID, 'auto-increment field', true), "NOT NULL AUTO_INCREMENT COMMENT 'auto-increment field'"],
            'DOUBLE' => [
                (new Field('test', FieldType::DOUBLE, 'Test Description', false))
                    ->setDefault(3.14),
                "NULL DEFAULT 3.14 COMMENT 'Test Description'",
            ],
            'EMAIL' => [new Field('Test', FieldType::EMAIL, 'Test Description', true), "NOT NULL COMMENT 'Test Description' COLLATE ascii_general_ci"],
            'PHONE' => [
                (new Field('test', FieldType::PHONE, 'Test Description', false))
                    ->setDefault('555-555-5555'),
                "NULL DEFAULT '555-555-5555' COMMENT 'Test Description' COLLATE ascii_general_ci",
            ],
            'STRING' => [
                (new Field('test', FieldType::STRING, 'Test Description', true))
                    ->setDefault('sample value'),
                "NOT NULL DEFAULT 'sample value' COMMENT 'Test Description'",
            ],
            'TEXT' => [
                (new Field('test', FieldType::TEXT, 'Test Description', true))
                    ->setDefault('sample value'),
                "NOT NULL DEFAULT 'sample value' COMMENT 'Test Description'",
            ],
            'URL' => [new Field('Test', FieldType::URL, 'Test Description', true), "NOT NULL COMMENT 'Test Description' COLLATE ascii_general_ci"],
            'UUID' => [new Field('Test', FieldType::UUID, 'Test Description', true), "NOT NULL COMMENT 'Test Description' COLLATE ascii_general_ci"],
        ];
    }

    /**
     * @dataProvider indexAttributesDataProvider
     */
    public function testIndexAttributesFilterShouldPass(Index $index, string $expectedValue)
    {
        $value = $this->twigExtension->attributesFilter($index);

        self::assertEquals($expectedValue, $value);
    }

    public function indexAttributesDataProvider()
    {
        return [
            'Index - no description' => [
                new Index('TestIndex', [new Field('Test Field', FieldType::STRING)]),
                '', ],
            'Index - woth description' => [
                (new Index('TestIndex', [new Field('Test Field', FieldType::STRING)]))
                    ->setDescription("Test's Description"),
                "COMMENT 'Test''s Description'", ],
        ];
    }

    /**
     * @dataProvider unsupportedAttributesDataProvider
     *
     * @param mixed $obj Object to filter
     */
    public function testUnsupportedAttributesShouldThrowException($obj)
    {
        $this->expectException(RuntimeException::class);
        $this->twigExtension->attributesFilter($obj);
    }

    public function unsupportedAttributesDataProvider()
    {
        $package = TestHelper::getSamplePackage();
        $user = $package->getBusinessModel('User');

        return [
            'String' => ['Invalid'],
            'Entity' => [$user],
            'RelationshipSide' => [$user->getField('topics')->getRelation()],
            'Package' => [$package],
        ];
    }

    /**
     * @dataProvider indexColumnNameProvider
     */
    public function testIndexColumnFilter(Index $index, string $expected)
    {
        self::assertEquals($expected, $this->twigExtension->columnFilter($index));
    }

    public function indexColumnNameProvider(): array
    {
        $package = TestHelper::getSamplePackage();
        $user = $package->getBusinessModel('User');

        $field1 = new Field('firstName', FieldType::STRING);
        $field1->setSearchable(true);
        $field2 = new Field('lastName', FieldType::STRING);
        $field2->setSearchable(true);
        $field3 = new Field('phone', FieldType::PHONE);
        $field3->setSearchable(true);
        $field4 = new Field('text', FieldType::TEXT);
        $field4->setSearchable(true);
        $field5 = new Field('uuid', FieldType::UUID);
        $field5->setSearchable(true);

        return [
            'one-field index' => [
                new Index('test_index', [$field1]),
                'first_name(20)',
            ],
            'two-field index' => [
                new Index('test_index', [$field1, $field2]),
                'first_name(20),last_name(20)',
            ],
            'all string index' => [
                new Index('test_index', [$field1, $field2, $field3, $field4, $field5]),
                'first_name(20),last_name(20),phone,text,uuid',
            ],
        ];
    }
}
