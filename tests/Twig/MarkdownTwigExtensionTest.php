<?php

namespace CodePrimer\Tests\Twig;

use CodePrimer\Helper\FieldType;
use CodePrimer\Model\Data\Data;
use CodePrimer\Model\Data\InternalDataBundle;
use CodePrimer\Model\Field;
use CodePrimer\Tests\Helper\TestHelper;
use CodePrimer\Twig\MarkdownTwigExtension;

class MarkdownTwigExtensionTest extends TwigExtensionTest
{
    /** @var MarkdownTwigExtension */
    private $twigExtension;

    public function setUp(): void
    {
        parent::setUp();
        $this->twigExtension = new MarkdownTwigExtension();
    }

    public function testGetFiltersShouldPass()
    {
        $filters = $this->twigExtension->getFilters();

        self::assertCount(25, $filters);

        $this->assertTwigFilter('details', $filters);
        $this->assertTwigFilter('model', $filters);
        $this->assertTwigFilter('header', $filters);
        $this->assertTwigFilter('row', $filters);
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
            'BOOL' => [new Field('Test', FieldType::BOOL, 'Test Description', true), 'boolean'],
            'BOOLEAN' => [new Field('Test', FieldType::BOOLEAN, 'Test Description', true), 'boolean'],
            'DATE' => [new Field('Test', FieldType::DATE, 'Test Description', true), 'date'],
            'DATETIME' => [new Field('Test', FieldType::DATETIME, 'Test Description', true), 'datetime'],
            'DECIMAL' => [new Field('Test', FieldType::DECIMAL, 'Test Description', true), 'decimal'],
            'DOUBLE' => [new Field('Test', FieldType::DOUBLE, 'Test Description', true), 'double'],
            'EMAIL' => [new Field('Test', FieldType::EMAIL, 'Test Description', true), 'email'],
            'FLOAT' => [new Field('Test', FieldType::FLOAT, 'Test Description', true), 'float'],
            'ID' => [new Field('Test', FieldType::ID, 'Test Description', true), 'id'],
            'INT' => [new Field('Test', FieldType::INT, 'Test Description', true), 'integer'],
            'INTEGER' => [new Field('Test', FieldType::INTEGER, 'Test Description', true), 'integer'],
            'LONG' => [new Field('Test', FieldType::LONG, 'Test Description', true), 'long'],
            'PASSWORD' => [new Field('Test', FieldType::PASSWORD, 'Test Description', true), 'password'],
            'PHONE' => [new Field('Test', FieldType::PHONE, 'Test Description', true), 'phone'],
            'PRICE' => [new Field('Test', FieldType::PRICE, 'Test Description', true), 'price'],
            'RANDOM_STRING' => [new Field('Test', FieldType::RANDOM_STRING, 'Test Description', true), 'randomstring'],
            'STRING' => [new Field('Test', FieldType::STRING, 'Test Description', true), 'string'],
            'TEXT' => [new Field('Test', FieldType::TEXT, 'Test Description', true), 'text'],
            'TIME' => [new Field('Test', FieldType::TIME, 'Test Description', true), 'time'],
            'URL' => [new Field('Test', FieldType::URL, 'Test Description', true), 'url'],
            'UUID' => [new Field('Test', FieldType::UUID, 'Test Description', true), 'uuid'],
            'UNKNOWN' => [new Field('Test', 'Unknown', 'Test Description', true), 'Unknown'],
            'BOOL ARRAY' => [
                (new Field('Test', FieldType::BOOL, 'Test Description', true))
                    ->setList(true),
                'List of boolean',
            ],
            'ENTITY' => [new Field('Test', 'User', 'Test Description', true), '[`User`](../DataModel/Overview.md#user)'],
            'OPTIONAL ENTITY' => [new Field('Test', 'User'), '[`User`](../DataModel/Overview.md#user)'],
            'ENTITY LIST' => [
                (new Field('Test', 'User', 'Test Description', true))
                    ->setList(true),
                'List of [`User`](../DataModel/Overview.md#user)',
            ],
            'DATA - EMAIL' => [new Data($businessBundle->getBusinessModel('User'), 'email'), 'email'],
            'DATA - ENTITY' => [new Data($businessBundle->getBusinessModel('User'), 'topics'), 'List of [`Topic`](../DataModel/Overview.md#topic)'],
            'DATABUNDLE - SIMPLE' => [new InternalDataBundle('Test Bundle'), 'Structure'],
            'DATABUNDLE - LIST' => [(new InternalDataBundle('Test Bundle'))->setAsListStructure(), 'List'],
            'DATASET' => [new Field('Test', 'UserStatus', 'Test Description', true), '[`UserStatus`](../Dataset/Overview.md#userstatus)'],
            'DATASET LIST' => [
                (new Field('Test', 'UserStatus', 'Test Description', true))
                    ->setList(true),
                'List of [`UserStatus`](../Dataset/Overview.md#userstatus)',
            ],
        ];
    }

    /**
     * @dataProvider detailsDataProvider
     *
     * @param $obj
     * @param $expectedValue
     */
    public function testDetailsFilter($obj, $expectedValue)
    {
        $value = $this->twigExtension->detailsFilter($this->context, $obj);

        self::assertEquals($expectedValue, $value);
    }

    public function detailsDataProvider()
    {
        $businessBundle = TestHelper::getSampleBusinessBundle();

        return [
            'FIELD' => [new Field('Test', FieldType::BOOL, 'Test Description', true), '*N/A*'],
            'DATA - EMAIL' => [new Data($businessBundle->getBusinessModel('User'), 'email'), '*N/A*'],
            'DATA - ATTRIBUTES ENTITY' => [new Data($businessBundle->getBusinessModel('User'), 'topics', Data::ATTRIBUTES), 'Attributes'],
            'DATA - REFERENCE ENTITY' => [new Data($businessBundle->getBusinessModel('User'), 'topics'), 'Reference'],
            'DATA - FULL ENTITY' => [new Data($businessBundle->getBusinessModel('User'), 'topics', Data::FULL), 'Full'],
            'DATA - ATTRIBUTES DATASET' => [new Data($businessBundle->getBusinessModel('User'), 'status', Data::ATTRIBUTES), 'Attributes'],
            'DATA - REFERENCE DATASET' => [new Data($businessBundle->getBusinessModel('User'), 'status'), 'Reference'],
            'DATA - FULL DATASET' => [new Data($businessBundle->getBusinessModel('User'), 'status', Data::FULL), 'Full'],
        ];
    }

    /**
     * @dataProvider modelDataProvider
     *
     * @param $obj
     * @param $expectedValue
     */
    public function testModelFilter($obj, $expectedValue)
    {
        $value = $this->twigExtension->modelFilter($this->context, $obj);

        self::assertEquals($expectedValue, $value);
    }

    public function modelDataProvider()
    {
        $businessBundle = TestHelper::getSampleBusinessBundle();

        return [
            'FIELD - Native' => [new Field('Test', FieldType::BOOL, 'Test Description', true), '*N/A*'],
            'FIELD - Dataset' => [new Field('Test', 'UserStatus', 'Test Description', true), '*N/A*'],
            'FIELD - Model' => [new Field('Test', 'UserStats', 'Test Description', true), '[`UserStats`](../DataModel/Overview.md#userstats)'],
            'DATA - EMAIL' => [new Data($businessBundle->getBusinessModel('User'), 'email'), '[`User`](../DataModel/Overview.md#user)'],
            'DATA - ATTRIBUTES ENTITY' => [new Data($businessBundle->getBusinessModel('User'), 'topics'), '[`User`](../DataModel/Overview.md#user)'],
            'DATA - REFERENCE ENTITY' => [new Data($businessBundle->getBusinessModel('User'), 'topics', Data::REFERENCE), '[`User`](../DataModel/Overview.md#user)'],
            'DATA - FULL ENTITY' => [new Data($businessBundle->getBusinessModel('User'), 'topics', Data::FULL), '[`User`](../DataModel/Overview.md#user)'],
        ];
    }
}
