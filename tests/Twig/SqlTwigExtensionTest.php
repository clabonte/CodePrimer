<?php

namespace CodePrimer\Tests\Twig;

use CodePrimer\Adapter\RelationalDatabaseAdapter;
use CodePrimer\Helper\FieldType;
use CodePrimer\Model\BusinessBundle;
use CodePrimer\Model\BusinessModel;
use CodePrimer\Model\Constraint;
use CodePrimer\Model\Data\Data;
use CodePrimer\Model\Database\Index;
use CodePrimer\Model\Dataset;
use CodePrimer\Model\Field;
use CodePrimer\Model\RelationshipSide;
use CodePrimer\Tests\Helper\RelationshipTestHelper;
use CodePrimer\Tests\Helper\TestHelper;
use CodePrimer\Twig\SqlTwigExtension;
use Exception;
use InvalidArgumentException;
use RuntimeException;

class SqlTwigExtensionTest extends TwigExtensionTest
{
    /** @var SqlTwigExtension */
    private $twigExtension;

    public function setUp(): void
    {
        parent::setUp();
        $this->twigExtension = new SqlTwigExtension();
    }

    public function testGetFiltersShouldPass()
    {
        $filters = $this->twigExtension->getFilters();

        self::assertNotNull($filters);

        $this->assertTwigFilter('database', $filters);
        $this->assertTwigFilter('table', $filters);
        $this->assertTwigFilter('column', $filters);
        $this->assertTwigFilter('foreignKey', $filters);
        $this->assertTwigFilter('user', $filters);
    }

    public function testGetFunctionsShouldPass()
    {
        $functions = $this->twigExtension->getFunctions();

        self::assertNotNull($functions);

        $this->assertTwigFunction('databaseFields', $functions);
        $this->assertTwigFunction('auditedFields', $functions);
        $this->assertTwigFunction('indexes', $functions);
    }

    public function testGetTestsShouldPass()
    {
        $tests = $this->twigExtension->getTests();

        self::assertNotNull($tests);

        $this->assertTwigFunction('foreignKey', $tests);
    }

    /**
     * @dataProvider foreignKeyProvider
     *
     * @throws Exception
     */
    public function testForeignKeyFilter($obj, string $expected, Field $sourceField = null, Dataset $destination = null)
    {
        self::assertEquals($expected, $this->twigExtension->foreignKeyFilter($obj, $sourceField, $destination));
    }

    public function foreignKeyProvider()
    {
        $businessBundle = TestHelper::getSampleBusinessBundle();
        // Generate the missing fields
        $adapter = new RelationalDatabaseAdapter();
        $adapter->generateRelationalFields($businessBundle);

        $user = $businessBundle->getBusinessModel('User');
        $subscription = $businessBundle->getBusinessModel('Subscription');
        $metadata = $businessBundle->getBusinessModel('Metadata');
        $post = $businessBundle->getBusinessModel('Post');
        $topic = $businessBundle->getBusinessModel('Topic');

        return [
            'User->UserStat' => [
                $user->getField('stats')->getRelation(),
                'fk_users_user_stats_stats_id',
            ],
            'User->Subscription' => [
                $user->getField('subscription')->getRelation(),
                'fk_users_subscriptions_subscription_id',
            ],
            'Subscription->User' => [
                $subscription->getField('user')->getRelation(),
                'fk_subscriptions_users_user_id',
            ],
            'Metadata->User' => [
                $metadata->getField('user')->getRelation(),
                'fk_metadata_users_user_id',
            ],
            'Post->User' => [
                $post->getField('author')->getRelation(),
                'fk_posts_users_author_id',
            ],
            'Dataset' => [
                $user,
                'fk_users_user_statuses_status',
                $user->getField('status'),
                $businessBundle->getDataset('UserStatus'),
            ],
        ];
    }

    /**
     * @dataProvider foreignKeyExceptionProvider
     *
     * @throws Exception
     */
    public function testForeignKeyFilterOnInvalidRelationshipShouldThrowException(RelationshipSide $obj)
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('foreignKey filter can only be used against a One-To-One or the right side of a One-To-Many relationship');
        $this->twigExtension->foreignKeyFilter($obj);
    }

    public function foreignKeyExceptionProvider()
    {
        $businessBundle = TestHelper::getSampleBusinessBundle();
        // Generate the missing fields
        $adapter = new RelationalDatabaseAdapter();
        $adapter->generateRelationalFields($businessBundle);

        $user = $businessBundle->getBusinessModel('User');
        $subscription = $businessBundle->getBusinessModel('Subscription');
        $metadata = $businessBundle->getBusinessModel('Metadata');
        $post = $businessBundle->getBusinessModel('Post');
        $topic = $businessBundle->getBusinessModel('Topic');

        return [
            'User->Post' => [$user->getField('posts')->getRelation()],
            'User->Metadata' => [$user->getField('metadata')->getRelation()],
            'User->Topic' => [$user->getField('topics')->getRelation()],
            'Topic->Post' => [$topic->getField('posts')->getRelation()],
            'Topic->User' => [$topic->getField('authors')->getRelation()],
        ];
    }

    /**
     * @dataProvider foreignKeyFilterInvalidParamProvider
     *
     * @param BusinessModel $businessModel
     * @param Field         $sourceField
     * @param Dataset       $destination
     *
     * @throws Exception
     */
    public function testForeignKeyFilterOnInvalidParamThrowsException($businessModel, ?Field $sourceField, ?Dataset $destination, string $expectedMessage)
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage($expectedMessage);
        $this->twigExtension->foreignKeyFilter($businessModel, $sourceField, $destination);
    }

    public function foreignKeyFilterInvalidParamProvider()
    {
        $businessBundle = TestHelper::getSampleBusinessBundle();
        $user = $businessBundle->getBusinessModel('User');

        return [
            'No sourceField' => [
                $user,
                null,
                $businessBundle->getDataset('UserStatus'),
                'foreignKey filter on a BusinessModel object must provide a valid sourceField and destination values',
            ],
            'No dataset' => [
                $user,
                $user->getField('status'),
                null,
                'foreignKey filter on a BusinessModel object must provide a valid sourceField and destination values',
            ],
            'InvalidType' => [
                $businessBundle->getDataset('UserStatus'),
                $user->getField('status'),
                $businessBundle->getDataset('UserStatus'),
                'foreignKey filter only accepts RelationshipSide and BusinessModel sources',
            ],
        ];
    }

    /**
     * @dataProvider databaseNameProvider
     */
    public function testDatabaseFilter(BusinessBundle $businessBundle, string $expected)
    {
        self::assertEquals($expected, $this->twigExtension->databaseFilter($businessBundle));
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
    public function testTableFilter($obj, string $expected)
    {
        self::assertEquals($expected, $this->twigExtension->tableFilter($obj));
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
            'Dataset' => [new Dataset('Sample-Name'), 'sample_names'],
        ];
    }

    /**
     * @dataProvider userProvider
     */
    public function testUserFilter(BusinessBundle $businessBundle, string $expected)
    {
        self::assertEquals($expected, $this->twigExtension->userFilter($businessBundle));
    }

    public function userProvider()
    {
        return [
            'Name' => [new BusinessBundle('Namespace', 'Name'), 'name'],
            'sampleName' => [new BusinessBundle('Namespace', 'sampleName'), 'sample_name'],
            'SampleName' => [new BusinessBundle('Namespace', 'SampleName'), 'sample_name'],
            'Sample Name' => [new BusinessBundle('Namespace', 'Sample Name'), 'sample_name'],
            'Samples Names' => [new BusinessBundle('Namespace', 'Samples Names'), 'samples_names'],
            'Sample-Name' => [new BusinessBundle('Namespace', 'Sample-Name'), 'sample_name'],
            'TestPackage' => [TestHelper::getSampleBusinessBundle(), 'functional_test'],
        ];
    }

    /**
     * @dataProvider relationTableNameProvider
     */
    public function testRelationTableFilter(RelationshipSide $relation, string $expected)
    {
        self::assertEquals($expected, $this->twigExtension->tableFilter($relation));
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
     * @param mixed $obj
     *
     * @throws RuntimeException
     */
    public function testRelationTableFilterShouldThrowException($obj)
    {
        $this->expectException(RuntimeException::class);
        $this->twigExtension->tableFilter($obj);
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
            'Field' => [
                new Field('Test Field', FieldType::STRING),
            ],
        ];
    }

    /**
     * @dataProvider auditTableNameProvider
     */
    public function testAuditTableFilter(BusinessModel $businessModel, string $expected)
    {
        self::assertEquals($expected, $this->twigExtension->auditTableFilter($businessModel));
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
     * @dataProvider fieldColumnNameProvider
     */
    public function testFieldColumnFilter(Field $field, string $expected)
    {
        self::assertEquals($expected, $this->twigExtension->columnFilter($field));
    }

    public function fieldColumnNameProvider()
    {
        $businessBundle = TestHelper::getSampleBusinessBundle();
        // Generate the missing fields
        $adapter = new RelationalDatabaseAdapter();
        $adapter->generateRelationalFields($businessBundle);

        $user = $businessBundle->getBusinessModel('User');
        $metadata = $businessBundle->getBusinessModel('Metadata');

        return [
            'Name' => [new Field('Name', FieldType::STRING), 'name'],
            'sampleName' => [new Field('sampleName', FieldType::STRING), 'sample_name'],
            'SampleName' => [new Field('SampleName', FieldType::STRING), 'sample_name'],
            'Sample Name' => [new Field('Sample Name', FieldType::STRING), 'sample_name'],
            'Samples Names' => [new Field('Samples Names', FieldType::STRING), 'samples_names'],
            'Sample-Name' => [new Field('Sample-Name', FieldType::STRING), 'sample_name'],
            'User->UserStat' => [$user->getField('stats'), 'stats_id'],
            'User->Subscription' => [$user->getField('subscription'), 'subscription_id'],
            'Metadata->User' => [$metadata->getField('user'), 'user_id'],
        ];
    }

    public function testColumnFilterForArrayShouldPass()
    {
        $businessBundle = TestHelper::getSampleBusinessBundle();

        $fields = $this->twigExtension->databaseFieldsFunction($businessBundle->getDataset('UserStatus'));
        self::assertEquals('`name`, `description`, `login_allowed`', $this->twigExtension->columnFilter($fields));
    }

    /**
     * @dataProvider businessModelColumnNameProvider
     */
    public function testBusinessModelColumnFilter(BusinessModel $businessModel, string $expected)
    {
        self::assertEquals($expected, $this->twigExtension->columnFilter($businessModel));
    }

    public function businessModelColumnNameProvider()
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

    /**
     * @dataProvider indexColumnNameProvider
     */
    public function testIndexColumnFilter(Index $index, string $expected)
    {
        self::assertEquals($expected, $this->twigExtension->columnFilter($index));
    }

    public function indexColumnNameProvider(): array
    {
        $businessBundle = TestHelper::getSampleBusinessBundle();
        $user = $businessBundle->getBusinessModel('User');

        $field1 = new Field('firstName', FieldType::STRING);
        $field1->setSearchable(true);
        $field2 = new Field('lastName', FieldType::STRING);
        $field2->setSearchable(true);

        return [
            'one-field index' => [
                new Index('test_index', [$field1]),
                'first_name',
            ],
            'two-field index' => [
                new Index('test_index', [$field1, $field2]),
                'first_name, last_name',
            ],
        ];
    }

    /**
     * @dataProvider columnFilterExceptionProvider
     *
     * @param mixed $obj
     *
     * @throws Exception
     */
    public function testColumnFilterShouldThrowException($obj)
    {
        $this->expectException(Exception::class);
        $this->twigExtension->columnFilter($obj);
    }

    public function columnFilterExceptionProvider()
    {
        $businessBundle = TestHelper::getSampleBusinessBundle();
        $user = $businessBundle->getBusinessModel('User');

        return [
            'Package' => [$businessBundle],
            'RelationshipSide' => [$user->getField('metadata')->getRelation()],
        ];
    }

    /**
     * @dataProvider getDatabaseFieldsProvider
     *
     * @param Field[] $expectedFields
     */
    public function testDatabaseFieldsFunction($model, array $expectedFields)
    {
        $fields = $this->twigExtension->databaseFieldsFunction($model);
        self::assertCount(count($expectedFields), $fields);
        foreach ($expectedFields as $field) {
            self::assertContains($field, $fields);
        }
    }

    public function getDatabaseFieldsProvider(): array
    {
        $businessBundle = TestHelper::getSampleBusinessBundle();

        $user = $businessBundle->getBusinessModel('User');
        $userStatus = $businessBundle->getDataset('UserStatus');

        return [
            'User' => [
                $user,
                [
                    $user->getField('id'),
                    $user->getField('firstName'),
                    $user->getField('lastName'),
                    $user->getField('nickname'),
                    $user->getField('email'),
                    $user->getField('password'),
                    $user->getField('created'),
                    $user->getField('updated'),
                    $user->getField('crmId'),
                    $user->getField('activationCode'),
                    $user->getField('stats'),
                    $user->getField('subscription'),
                    $user->getField('status'),
                ],
            ],
            'Dataset' => [
                $userStatus,
                [
                    $userStatus->getField('name'),
                    $userStatus->getField('description'),
                    $userStatus->getField('loginAllowed'),
                ],
            ],
            'Field' => [
                $userStatus->getField('name'),
                [],
            ],
        ];
    }

    /**
     * @dataProvider foreignKeyTestProvider
     *
     * @throws Exception
     */
    public function testForeignKeyTest(RelationshipSide $obj, bool $expected)
    {
        self::assertEquals($expected, $this->twigExtension->foreignKeyTest($obj));
    }

    public function foreignKeyTestProvider()
    {
        $helper = new RelationshipTestHelper();

        return [
            'One-To-One unidirectional' => [
                $helper->getOneToOneUnidirectionalRelationship(),
                true,
            ],
            'One-To-One birectional - left' => [
                $helper->getOneToOneBidirectionalLeftRelationship(),
                true,
            ],
            'One-To-One birectional - right' => [
                $helper->getOneToOneBidirectionalRightRelationship(),
                true,
            ],
            'Many-To-One' => [
                $helper->getManytoOneRelationship(),
                true,
            ],
            'One-To-Many' => [
                $helper->getOneToManyRelationship(),
                false,
            ],
            'Many-To-Many - Left' => [
                $helper->getManyToManyLeftRelationship(),
                false,
            ],
            'Many-To-Many - Right' => [
                $helper->getManyToManyRightRelationship(),
                false,
            ],
        ];
    }

    /**
     * @dataProvider auditedFieldsProvider
     *
     * @param Field[] $expectedFields
     */
    public function testAuditedFieldsFunction(BusinessModel $businessModel, bool $includeId, array $expectedFields)
    {
        $fields = $this->twigExtension->auditedFieldsFunction($businessModel, $includeId);
        self::assertCount(count($expectedFields), $fields);
        foreach ($expectedFields as $field) {
            self::assertContains($field, $fields);
        }
    }

    public function auditedFieldsProvider(): array
    {
        $helper = new RelationshipTestHelper();
        $businessBundle = $helper->getBusinessBundle();

        $user = $businessBundle->getBusinessModel('User');
        $metadata = $businessBundle->getBusinessModel('Metadata');

        return [
            'User with id' => [
                $user,
                true,
                [
                    $user->getField('id'),
                    $user->getField('firstName'),
                    $user->getField('lastName'),
                    $user->getField('nickname'),
                    $user->getField('email'),
                    $user->getField('password'),
                    $user->getField('crmId'),
                    $user->getField('activationCode'),
                    $user->getField('stats'),
                    $user->getField('subscription'),
                    $user->getField('status'),
                ],
            ],
            'User without id' => [
                $user,
                false,
                [
                    $user->getField('firstName'),
                    $user->getField('lastName'),
                    $user->getField('nickname'),
                    $user->getField('email'),
                    $user->getField('password'),
                    $user->getField('crmId'),
                    $user->getField('activationCode'),
                    $user->getField('stats'),
                    $user->getField('subscription'),
                    $user->getField('status'),
                ],
            ],
            'Metadata generated field' => [
                $businessBundle->getBusinessModel('Metadata'),
                false,
                [
                    $metadata->getField('name'),
                    $metadata->getField('value'),
                ],
            ],
        ];
    }

    /**
     * @dataProvider indexesFunctionProvider
     *
     * @param Index[] $expected
     */
    public function testIndexesFunction($model, array $expected)
    {
        $actual = $this->twigExtension->indexesFunction($model);

        self::assertCount(count($expected), $actual);
        foreach ($expected as $index) {
            $this->assertIndex($index, $actual);
        }
    }

    public function indexesFunctionProvider(): array
    {
        $businessBundle = TestHelper::getSampleBusinessBundle();
        $user = $businessBundle->getBusinessModel('User');

        $field1 = new Field('name', FieldType::STRING);
        $index1 = new Field('firstName', FieldType::STRING);
        $index1->setSearchable(true);
        $index2 = new Field('lastName', FieldType::STRING);
        $index2->setSearchable(true);

        return [
            'no index' => [
                (new BusinessModel('SampleEntity'))
                    ->addField($field1)
                    ->addUniqueConstraint(new Constraint('uniqueName', Constraint::TYPE_UNIQUE, [$field1])),
                [],
            ],
            'one searchable field' => [
                (new BusinessModel('SampleEntity'))
                    ->addField($index1),
                [
                    (new Index('first_name_idx', [$index1]))
                        ->setDescription('To optimize search queries'),
                ],
            ],
            'two searchable fields' => [
                (new BusinessModel('SampleEntity'))
                    ->addField($index1)
                    ->addField($index2),
                [
                    (new Index('first_name_idx', [$index1]))
                        ->setDescription('To optimize search queries'),
                    (new Index('last_name_idx', [$index2]))
                        ->setDescription('To optimize search queries'),
                ],
            ],
            'User indexes' => [
                $user,
                [
                    (new Index('first_name_idx', [$user->getField('firstName')]))
                        ->setDescription('To optimize search queries'),
                    (new Index('last_name_idx', [$user->getField('lastName')]))
                        ->setDescription('To optimize search queries'),
                    (new Index('email_idx', [$user->getField('email')]))
                        ->setDescription('To optimize search queries'),
                    (new Index('nickname_idx', [$user->getField('nickname')]))
                        ->setDescription('To optimize search queries'),
                    (new Index('stats_id_idx', [$user->getField('stats')]))
                        ->setDescription('UserStats foreign key'),
                    (new Index('subscription_id_idx', [$user->getField('subscription')]))
                        ->setDescription('Subscription foreign key'),
                ],
            ],
            'Dataset' => [
                (new Dataset('SampleEntity'))
                    ->addField($field1),
                [],
            ],
        ];
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
            'DATE - string' => [new Field('Test', FieldType::DATE), '2020-12-25', "'2020-12-25'"],
            'DATE - DateTime' => [new Field('Test', FieldType::DATE), new \DateTime('2020-12-25'), "'2020-12-25'"],
            'TIME - string' => [new Field('Test', FieldType::TIME), '23:59:59', "'23:59:59'"],
            'TIME - DateTime' => [new Field('Test', FieldType::TIME), new \DateTime('23:59:59'), "'23:59:59'"],
            'DATETIME - string' => [new Field('Test', FieldType::DATETIME), '2020-12-25T23:59:59Z', "'2020-12-25T23:59:59Z'"],
            'DATETIME - DateTime' => [new Field('Test', FieldType::DATETIME), new \DateTime('2020-12-25T23:59:59Z'), "'2020-12-25 23:59:59'"],
            'BOOLEAN - true' => [new Field('Test', FieldType::BOOLEAN), true, 'TRUE'],
            'BOOLEAN - false' => [new Field('Test', FieldType::BOOLEAN), 'no', 'FALSE'],
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
            'DATASET Field' => [new Field('Test', 'UserStatus'), 'active', "'active'"],
            'DATASET element' => [$bundle->getDataset('UserStatus')->getElement('active'), '', "'active', 'User is fully registered and allowed to user our application', TRUE"],
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

    /**
     * @param Index[] $actual
     */
    private function assertIndex(Index $index, array $actual)
    {
        $found = false;

        foreach ($actual as $value) {
            if ($value == $index) {
                $found = true;
                break;
            }
        }

        self::assertTrue($found, 'Index '.$index->getName().' not found');
    }
}
