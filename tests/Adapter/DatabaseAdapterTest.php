<?php

namespace CodePrimer\Tests\Adapter;

use CodePrimer\Adapter\DatabaseAdapter;
use CodePrimer\Helper\FieldType;
use CodePrimer\Model\BusinessBundle;
use CodePrimer\Model\BusinessModel;
use CodePrimer\Model\Field;
use CodePrimer\Model\RelationshipSide;
use CodePrimer\Tests\Helper\RelationshipTestHelper;
use CodePrimer\Tests\Helper\TestHelper;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class DatabaseAdapterTest extends TestCase
{
    /** @var DatabaseAdapter */
    private $adapter;

    public function setUp(): void
    {
        parent::setUp();
        $this->adapter = new DatabaseAdapter();
    }

    /**
     * @dataProvider databaseNameProvider
     */
    public function testGetDatabaseName(BusinessBundle $businessBundle, string $expected)
    {
        self::assertEquals($expected, $this->adapter->getDatabaseName($businessBundle));
    }

    public function databaseNameProvider()
    {
        return [
            'Name' => [new BusinessBundle('Namespace', 'Name'), 'namespace_name'],
            'Namespace Space Name' => [new BusinessBundle('Namespace Space', 'Name'), 'namespace_space_name'],
            'Namespace Spaces Name' => [new BusinessBundle('Namespace Spaces', 'Name'), 'namespace_spaces_name'],
            'sampleName' => [new BusinessBundle('Namespace', 'sampleName'), 'namespace_sample_name'],
            'SampleName' => [new BusinessBundle('Namespace', 'SampleName'), 'namespace_sample_name'],
            'Sample Name' => [new BusinessBundle('Namespace', 'Sample Name'), 'namespace_sample_name'],
            'Samples Names' => [new BusinessBundle('Namespace', 'Samples Names'), 'namespace_samples_name'],
            'Sample-Name' => [new BusinessBundle('Namespace', 'Sample-Name'), 'namespace_sample_name'],
            'TestPackage' => [TestHelper::getSampleBusinessBundle(), 'code_primer_tests_functional_test'],
        ];
    }

    /**
     * @dataProvider tableNameProvider
     */
    public function testGetTableName(BusinessModel $businessModel, string $expected)
    {
        self::assertEquals($expected, $this->adapter->getTableName($businessModel));
    }

    public function tableNameProvider()
    {
        return [
            'Name' => [new BusinessModel('Name'), 'names'],
            'sampleName' => [new BusinessModel('sampleName'), 'sample_names'],
            'SampleName' => [new BusinessModel('SampleName'), 'sample_names'],
            'Sample Name' => [new BusinessModel('Sample Name'), 'sample_names'],
            'Samples Names' => [new BusinessModel('Samples Names'), 'samples_names'],
            'Sample-Name' => [new BusinessModel('Sample-Name'), 'sample_names'],
        ];
    }

    /**
     * @dataProvider relationTableNameProvider
     */
    public function testGetRelationTableName(RelationshipSide $relation, string $expected)
    {
        self::assertEquals($expected, $this->adapter->getRelationTableName($relation));
    }

    public function relationTableNameProvider()
    {
        $helper = new RelationshipTestHelper();

        return [
            'Many-To-Many - Left' => [
                $helper->getManyToManyLeftRelationship(),
                'users_topics',
            ],
            'Many-To-Many - Right' => [
                $helper->getManyToManyRightRelationship(),
                'users_topics',
            ],
        ];
    }

    /**
     * @dataProvider relationTableNameExceptionProvider
     *
     * @throws RuntimeException
     */
    public function testGetRelationTableNameShouldThrowException(RelationshipSide $relation)
    {
        $this->expectException(RuntimeException::class);
        $this->adapter->getRelationTableName($relation);
    }

    public function relationTableNameExceptionProvider()
    {
        $helper = new RelationshipTestHelper();

        return [
            'One-To-One unidirectional' => [
                $helper->getOneToOneUnidirectionalRelationship(),
            ],
            'One-To-One birectional - left' => [
                $helper->getOneToOneBidirectionalLeftRelationship(),
            ],
            'One-To-One birectional - right' => [
                $helper->getOneToOneBidirectionalRightRelationship(),
            ],
            'Many-To-One' => [
                $helper->getManytoOneRelationship(),
            ],
            'One-To-Many' => [
                $helper->getOneToManyRelationship(),
            ],
        ];
    }

    /**
     * @dataProvider auditTableNameProvider
     */
    public function testGetAuditTableName(BusinessModel $businessModel, string $expected)
    {
        self::assertEquals($expected, $this->adapter->getAuditTableName($businessModel));
    }

    public function auditTableNameProvider()
    {
        return [
            'Name' => [new BusinessModel('Name'), 'names_logs'],
            'sampleName' => [new BusinessModel('sampleName'), 'sample_names_logs'],
            'SampleName' => [new BusinessModel('SampleName'), 'sample_names_logs'],
            'Sample Name' => [new BusinessModel('Sample Name'), 'sample_names_logs'],
            'Samples Names' => [new BusinessModel('Samples Names'), 'samples_names_logs'],
            'Sample-Name' => [new BusinessModel('Sample-Name'), 'sample_names_logs'],
        ];
    }

    /**
     * @dataProvider columnNameProvider
     */
    public function testGetColumnName(Field $field, string $expected)
    {
        self::assertEquals($expected, $this->adapter->getColumnName($field));
    }

    public function columnNameProvider()
    {
        return [
            'Name' => [new Field('Name', FieldType::STRING), 'name'],
            'sampleName' => [new Field('sampleName', FieldType::STRING), 'sample_name'],
            'SampleName' => [new Field('SampleName', FieldType::STRING), 'sample_name'],
            'Sample Name' => [new Field('Sample Name', FieldType::STRING), 'sample_name'],
            'Samples Names' => [new Field('Samples Names', FieldType::STRING), 'samples_names'],
            'Sample-Name' => [new Field('Sample-Name', FieldType::STRING), 'sample_name'],
        ];
    }

    /**
     * @dataProvider entityColumnNameProvider
     */
    public function testGetBusinessModelColumnName(BusinessModel $businessModel, string $expected)
    {
        self::assertEquals($expected, $this->adapter->getBusinessModelColumnName($businessModel));
    }

    public function entityColumnNameProvider()
    {
        return [
            'Name' => [new BusinessModel('Name'), 'name_id'],
            'sampleName' => [new BusinessModel('sampleName'), 'sample_name_id'],
            'SampleName' => [new BusinessModel('SampleName'), 'sample_name_id'],
            'Sample Name' => [new BusinessModel('Sample Name'), 'sample_name_id'],
            'Samples Names' => [new BusinessModel('Samples Names'), 'samples_names_id'],
            'Sample-Name' => [new BusinessModel('Sample-Name'), 'sample_name_id'],
        ];
    }
}
