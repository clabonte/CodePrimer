<?php

namespace CodePrimer\Tests\Twig;

use CodePrimer\Adapter\RelationalDatabaseAdapter;
use CodePrimer\Helper\FieldType;
use CodePrimer\Model\Constraint;
use CodePrimer\Model\Database\Index;
use CodePrimer\Model\BusinessModel;
use CodePrimer\Model\Field;
use CodePrimer\Model\Package;
use CodePrimer\Model\RelationshipSide;
use CodePrimer\Tests\Helper\RelationshipTestHelper;
use CodePrimer\Tests\Helper\TestHelper;
use CodePrimer\Twig\SqlTwigExtension;
use Exception;
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
    public function testForeignKeyFilter(RelationshipSide $obj, string $expected)
    {
        self::assertEquals($expected, $this->twigExtension->foreignKeyFilter($obj));
    }

    public function foreignKeyProvider()
    {
        $package = TestHelper::getSamplePackage();
        // Generate the missing fields
        $adapter = new RelationalDatabaseAdapter();
        $adapter->generateRelationalFields($package);

        $user = $package->getEntity('User');
        $subscription = $package->getEntity('Subscription');
        $metadata = $package->getEntity('Metadata');
        $post = $package->getEntity('Post');
        $topic = $package->getEntity('Topic');

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
        ];
    }

    /**
     * @dataProvider foreignKeyExceptionProvider
     *
     * @throws Exception
     */
    public function testForeignKeyFilterShouldThrowException(RelationshipSide $obj)
    {
        $this->expectException(Exception::class);
        $this->twigExtension->foreignKeyFilter($obj);
    }

    public function foreignKeyExceptionProvider()
    {
        $package = TestHelper::getSamplePackage();
        // Generate the missing fields
        $adapter = new RelationalDatabaseAdapter();
        $adapter->generateRelationalFields($package);

        $user = $package->getEntity('User');
        $subscription = $package->getEntity('Subscription');
        $metadata = $package->getEntity('Metadata');
        $post = $package->getEntity('Post');
        $topic = $package->getEntity('Topic');

        return [
            'User->Post' => [$user->getField('posts')->getRelation()],
            'User->Metadata' => [$user->getField('metadata')->getRelation()],
            'User->Topic' => [$user->getField('topics')->getRelation()],
            'Topic->Post' => [$topic->getField('posts')->getRelation()],
            'Topic->User' => [$topic->getField('authors')->getRelation()],
        ];
    }

    /**
     * @dataProvider databaseNameProvider
     */
    public function testDatabaseFilter(Package $package, string $expected)
    {
        self::assertEquals($expected, $this->twigExtension->databaseFilter($package));
    }

    public function databaseNameProvider()
    {
        return [
            'Name' => [new Package('Namespace', 'Name'), 'namespace_name'],
            'Namespace Space Name' => [new Package('Namespace Space', 'Name'), 'namespace_space_name'],
            'Namespace Spaces Name' => [new Package('Namespace Spaces', 'Name'), 'namespace_spaces_name'],
            'sampleName' => [new Package('Namespace', 'sampleName'), 'namespace_sample_name'],
            'SampleName' => [new Package('Namespace', 'SampleName'), 'namespace_sample_name'],
            'Sample Name' => [new Package('Namespace', 'Sample Name'), 'namespace_sample_name'],
            'Samples Names' => [new Package('Namespace', 'Samples Names'), 'namespace_samples_name'],
            'Sample-Name' => [new Package('Namespace', 'Sample-Name'), 'namespace_sample_name'],
            'TestPackage' => [TestHelper::getSamplePackage(), 'code_primer_tests_functional_test'],
        ];
    }

    /**
     * @dataProvider tableNameProvider
     */
    public function testTableFilter(BusinessModel $businessModel, string $expected)
    {
        self::assertEquals($expected, $this->twigExtension->tableFilter($businessModel));
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
     * @dataProvider userProvider
     */
    public function testUserFilter(Package $package, string $expected)
    {
        self::assertEquals($expected, $this->twigExtension->userFilter($package));
    }

    public function userProvider()
    {
        return [
            'Name' => [new Package('Namespace', 'Name'), 'name'],
            'sampleName' => [new Package('Namespace', 'sampleName'), 'sample_name'],
            'SampleName' => [new Package('Namespace', 'SampleName'), 'sample_name'],
            'Sample Name' => [new Package('Namespace', 'Sample Name'), 'sample_name'],
            'Samples Names' => [new Package('Namespace', 'Samples Names'), 'samples_names'],
            'Sample-Name' => [new Package('Namespace', 'Sample-Name'), 'sample_name'],
            'TestPackage' => [TestHelper::getSamplePackage(), 'functional_test'],
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
        $package = TestHelper::getSamplePackage();
        // Generate the missing fields
        $adapter = new RelationalDatabaseAdapter();
        $adapter->generateRelationalFields($package);

        $user = $package->getEntity('User');
        $metadata = $package->getEntity('Metadata');

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

    /**
     * @dataProvider entityColumnNameProvider
     */
    public function testEntityColumnFilter(BusinessModel $businessModel, string $expected)
    {
        self::assertEquals($expected, $this->twigExtension->columnFilter($businessModel));
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
        $user = $package->getEntity('User');

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
                'first_name,last_name',
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
        $package = TestHelper::getSamplePackage();
        $user = $package->getEntity('User');

        return [
            'Package' => [$package],
            'RelationshipSide' => [$user->getField('metadata')->getRelation()],
        ];
    }

    /**
     * @dataProvider getDatabaseFieldsProvider
     *
     * @param Field[] $expectedFields
     */
    public function testDatabaseFieldsFunction(BusinessModel $businessModel, array $expectedFields)
    {
        $fields = $this->twigExtension->databaseFieldsFunction($businessModel);
        self::assertCount(count($expectedFields), $fields);
        foreach ($expectedFields as $field) {
            self::assertContains($field, $fields);
        }
    }

    public function getDatabaseFieldsProvider(): array
    {
        $package = TestHelper::getSamplePackage();

        $user = $package->getEntity('User');

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
                ],
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
        $package = $helper->getPackage();

        $user = $package->getEntity('User');
        $metadata = $package->getEntity('Metadata');

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
                ],
            ],
            'Metadata generated field' => [
                $package->getEntity('Metadata'),
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
    public function testIndexesFunction(BusinessModel $businessModel, array $expected)
    {
        $actual = $this->twigExtension->indexesFunction($businessModel);

        self::assertCount(count($expected), $actual);
        foreach ($expected as $index) {
            $this->assertIndex($index, $actual);
        }
    }

    public function indexesFunctionProvider(): array
    {
        $package = TestHelper::getSamplePackage();
        $user = $package->getEntity('User');

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
