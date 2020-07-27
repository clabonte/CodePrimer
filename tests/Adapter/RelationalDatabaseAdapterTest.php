<?php

namespace CodePrimer\Tests\Adapter;

use CodePrimer\Adapter\RelationalDatabaseAdapter;
use CodePrimer\Helper\FieldType;
use CodePrimer\Model\Constraint;
use CodePrimer\Model\Database\Index;
use CodePrimer\Model\Entity;
use CodePrimer\Model\Field;
use CodePrimer\Model\Package;
use CodePrimer\Model\RelationshipSide;
use CodePrimer\Tests\Helper\RelationshipTestHelper;
use CodePrimer\Tests\Helper\TestHelper;
use PHPUnit\Framework\TestCase;

class RelationalDatabaseAdapterTest extends TestCase
{
    /** @var RelationalDatabaseAdapter */
    private $adapter;

    public function setUp(): void
    {
        parent::setUp();
        $this->adapter = new RelationalDatabaseAdapter();
    }

    /**
     * @dataProvider indexesProvider
     *
     * @param Index[] $expected
     */
    public function testGetIndexes(Entity $entity, array $expected)
    {
        $actual = $this->adapter->getIndexes($entity);

        self::assertCount(count($expected), $actual);
        foreach ($expected as $index) {
            $this->assertIndex($index, $actual);
        }
    }

    public function indexesProvider(): array
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
                (new Entity('SampleEntity'))
                    ->addField($field1)
                    ->addUniqueConstraint(new Constraint('uniqueName', Constraint::TYPE_UNIQUE, [$field1])),
                [],
            ],
            'one searchable field' => [
                (new Entity('SampleEntity'))
                    ->addField($index1),
                [
                    (new Index('first_name_idx', [$index1]))
                        ->setDescription('To optimize search queries'),
                ],
            ],
            'two searchable fields' => [
                (new Entity('SampleEntity'))
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

    public function testGenerateRelationalIdentifierFields()
    {
        $package = TestHelper::getSamplePackage();

        // Make sure there are a few fields which are missing ID fields
        $entity = $package->getEntity('UserStats');
        self::assertNotNull($entity);
        self::assertNull($entity->getIdentifier());

        $entity = $package->getEntity('Metadata');
        self::assertNotNull($entity);
        self::assertNull($entity->getIdentifier());

        $entity = $package->getEntity('Post');
        self::assertNotNull($entity);
        self::assertNull($entity->getIdentifier());

        // Add a new Entity with an 'id' field that does not qualify as an identifier
        $entity = new Entity('TestEntity');
        $entity->addField(new Field('id', FieldType::STRING));
        $package->addEntity($entity);

        // Generate the missing fields
        $this->adapter->generateRelationalFields($package);

        $entity = $package->getEntity('UserStats');
        self::assertNotNull($entity);
        $field = $entity->getIdentifier();
        self::assertNotNull($field);
        self::assertEquals(FieldType::UUID, $field->getType());
        self::assertEquals('id', $field->getName());
        self::assertTrue($field->isGenerated());
        self::assertTrue($field->isManaged());
        self::assertTrue($field->isMandatory());

        $entity = $package->getEntity('Metadata');
        self::assertNotNull($entity);
        $field = $entity->getIdentifier();
        self::assertNotNull($field);
        self::assertEquals(FieldType::UUID, $field->getType());
        self::assertEquals('id', $field->getName());
        self::assertTrue($field->isGenerated());
        self::assertTrue($field->isManaged());
        self::assertTrue($field->isMandatory());

        $entity = $package->getEntity('Post');
        self::assertNotNull($entity);
        $field = $entity->getIdentifier();
        self::assertNotNull($field);
        self::assertEquals(FieldType::UUID, $field->getType());
        self::assertEquals('id', $field->getName());
        self::assertTrue($field->isGenerated());
        self::assertTrue($field->isManaged());
        self::assertTrue($field->isMandatory());

        $entity = $package->getEntity('TestEntity');
        self::assertNotNull($entity);
        $field = $entity->getIdentifier();
        self::assertNotNull($field);
        self::assertEquals(FieldType::UUID, $field->getType());
        self::assertEquals('testEntityId', $field->getName());
        self::assertTrue($field->isGenerated());
        self::assertTrue($field->isManaged());
        self::assertTrue($field->isMandatory());
    }

    /**
     * Failure scenario: If an entity already has both an 'id' and '<entity>Id' fields that do not qualify
     * as identifier, the generateIdentifier method should throw an exception.
     */
    public function testGenerateIdentifierFieldWithExistingPotentialIdentifierFieldsShouldThrowException()
    {
        $package = new Package('Test', 'TestPackage');

        $entity = new Entity('TestEntity');
        $entity->addField(new Field('id', FieldType::STRING));
        $entity->addField(new Field('testEntityId', FieldType::STRING));
        self::assertNull($entity->getIdentifier(), 'Entity has an unexpected Identifier field');

        $package->addEntity($entity);

        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('Cannot generate ID field for entity TestEntity: "id" and "testEntityId" fields are already defined. Did you forget to specify an identifier for this entity?');

        // Generate the missing fields
        $this->adapter->generateRelationalFields($package);
    }

    public function testGenerateRelationalForeignFields()
    {
        $package = TestHelper::getSamplePackage();

        // Make sure we are missing the link between Metadata and User
        $entity = $package->getEntity('Metadata');
        self::assertNotNull($entity);
        self::assertNull($entity->getField('userId'));

        // Generate the missing fields
        $this->adapter->generateRelationalFields($package);

        // Make sure the missing field has been properly added
        $entity = $package->getEntity('Metadata');
        self::assertNotNull($entity);
        $field = $entity->getField('user');
        self::assertNotNull($field);
        self::assertEquals('User', $field->getType());
        self::assertEquals('user', $field->getName());
        self::assertTrue($field->isGenerated());
        self::assertFalse($field->isManaged());
        self::assertFalse($field->isMandatory());
        self::assertNotNull($field->getRelation());
        self::assertEquals(RelationshipSide::RIGHT, $field->getRelation()->getSide());
        self::assertEquals('User', $field->getRelation()->getRemoteSide()->getEntity()->getName());
        self::assertEquals('metadata', $field->getRelation()->getRemoteSide()->getField()->getName());
    }

    /**
     * Failure scenario: If a foreign entity does not have an identifier field, we should not be able to generate a
     * foreign key field.
     */
    public function testGenerateForeignKeyFieldWithoutIdentifierShouldThrowException()
    {
        $entity = new Entity('TestEntity');
        $entity->addField(
            (new Field('id', FieldType::UUID))
                ->setManaged(true)
                ->setMandatory(true)
        );
        self::assertNotNull($entity->getIdentifier());

        $foreignEntity = new Entity('ForeignEntity');
        $foreignEntity->addField(new Field('id', FieldType::STRING));
        $foreignEntity->addField(new Field('testEntityId', FieldType::STRING));
        self::assertNull($foreignEntity->getIdentifier());

        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('No identifier available for foreign entity ForeignEntity');

        // Generate the missing fields
        $this->adapter->generateForeignKeyField($entity, $foreignEntity);
    }

    /**
     * Failure scenario: If a foreign entity already has a field named with the foreign key, we should not be able to
     * generate a foreign key field.
     */
    public function testGenerateForeignKeyFieldWithExistingFieldNameShouldThrowException()
    {
        $entity = new Entity('TestEntity');
        $entity->addField(
            (new Field('id', FieldType::UUID))
                ->setManaged(true)
                ->setMandatory(true)
        );
        $entity->addField(new Field('foreignEntity', FieldType::STRING));
        self::assertNotNull($entity->getIdentifier());

        $foreignEntity = new Entity('ForeignEntity');
        $foreignEntity->addField(
            (new Field('id', FieldType::UUID))
                ->setManaged(true)
                ->setMandatory(true)
        );
        self::assertNotNull($foreignEntity->getIdentifier());

        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('Cannot generate foreign key field on entity TestEntity: Field "foreignEntity" field is already defined. Did you provide the right type for this field?');

        // Generate the missing fields
        $this->adapter->generateForeignKeyField($entity, $foreignEntity);
    }

    /**
     * @dataProvider getDatabaseFieldsProvider
     *
     * @param Field[] $expectedFields
     */
    public function testGetDatabaseFields(Entity $entity, array $expectedFields)
    {
        $fields = $this->adapter->getDatabaseFields($entity);
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
     * @dataProvider getAuditedFieldsProvider
     *
     * @param Field[] $expectedFields
     */
    public function testGetAuditedFields(Entity $entity, bool $includeId, array $expectedFields)
    {
        $fields = $this->adapter->getAuditedFields($entity, $includeId);
        self::assertCount(count($expectedFields), $fields);
        foreach ($expectedFields as $field) {
            self::assertContains($field, $fields);
        }
    }

    public function getAuditedFieldsProvider(): array
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
}
