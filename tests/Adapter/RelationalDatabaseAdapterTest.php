<?php

namespace CodePrimer\Tests\Adapter;

use CodePrimer\Adapter\RelationalDatabaseAdapter;
use CodePrimer\Helper\FieldType;
use CodePrimer\Model\BusinessBundle;
use CodePrimer\Model\BusinessModel;
use CodePrimer\Model\Constraint;
use CodePrimer\Model\Database\Index;
use CodePrimer\Model\Field;
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
    public function testGetIndexes(BusinessModel $businessModel, array $expected)
    {
        $actual = $this->adapter->getBusinessModelIndexes($businessModel);

        self::assertCount(count($expected), $actual);
        foreach ($expected as $index) {
            $this->assertIndex($index, $actual);
        }
    }

    public function indexesProvider(): array
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
        $businessBundle = TestHelper::getSampleBusinessBundle();

        // Make sure there are a few fields which are missing ID fields
        $businessModel = $businessBundle->getBusinessModel('UserStats');
        self::assertNotNull($businessModel);
        self::assertNull($businessModel->getIdentifier());

        $businessModel = $businessBundle->getBusinessModel('Metadata');
        self::assertNotNull($businessModel);
        self::assertNull($businessModel->getIdentifier());

        // Add a new BusinessModel with an 'id' field that does not qualify as an identifier
        $businessModel = new BusinessModel('TestEntity');
        $businessModel->addField(new Field('id', FieldType::STRING));
        $businessBundle->addBusinessModel($businessModel);

        // Generate the missing fields
        $this->adapter->generateRelationalFields($businessBundle);

        $businessModel = $businessBundle->getBusinessModel('UserStats');
        self::assertNotNull($businessModel);
        $field = $businessModel->getIdentifier();
        self::assertNotNull($field);
        self::assertEquals(FieldType::UUID, $field->getType());
        self::assertEquals('id', $field->getName());
        self::assertTrue($field->isGenerated());
        self::assertTrue($field->isManaged());
        self::assertTrue($field->isMandatory());

        $businessModel = $businessBundle->getBusinessModel('Metadata');
        self::assertNotNull($businessModel);
        $field = $businessModel->getIdentifier();
        self::assertNotNull($field);
        self::assertEquals(FieldType::UUID, $field->getType());
        self::assertEquals('id', $field->getName());
        self::assertTrue($field->isGenerated());
        self::assertTrue($field->isManaged());
        self::assertTrue($field->isMandatory());

        $businessModel = $businessBundle->getBusinessModel('TestEntity');
        self::assertNotNull($businessModel);
        $field = $businessModel->getIdentifier();
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
        $businessBundle = new BusinessBundle('Test', 'TestPackage');

        $businessModel = new BusinessModel('TestEntity');
        $businessModel->addField(new Field('id', FieldType::STRING));
        $businessModel->addField(new Field('testEntityId', FieldType::STRING));
        self::assertNull($businessModel->getIdentifier(), 'BusinessModel has an unexpected Identifier field');

        $businessBundle->addBusinessModel($businessModel);

        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('Cannot generate ID field for entity TestEntity: "id" and "testEntityId" fields are already defined. Did you forget to specify an identifier for this entity?');

        // Generate the missing fields
        $this->adapter->generateRelationalFields($businessBundle);
    }

    public function testGenerateRelationalForeignFields()
    {
        $businessBundle = TestHelper::getSampleBusinessBundle();

        // Make sure we are missing the link between Metadata and User
        $businessModel = $businessBundle->getBusinessModel('Metadata');
        self::assertNotNull($businessModel);
        self::assertNull($businessModel->getField('userId'));

        // Generate the missing fields
        $this->adapter->generateRelationalFields($businessBundle);

        // Make sure the missing field has been properly added
        $businessModel = $businessBundle->getBusinessModel('Metadata');
        self::assertNotNull($businessModel);
        $field = $businessModel->getField('user');
        self::assertNotNull($field);
        self::assertEquals('User', $field->getType());
        self::assertEquals('user', $field->getName());
        self::assertTrue($field->isGenerated());
        self::assertFalse($field->isManaged());
        self::assertFalse($field->isMandatory());
        self::assertNotNull($field->getRelation());
        self::assertEquals(RelationshipSide::RIGHT, $field->getRelation()->getSide());
        self::assertEquals('User', $field->getRelation()->getRemoteSide()->getBusinessModel()->getName());
        self::assertEquals('metadata', $field->getRelation()->getRemoteSide()->getField()->getName());
    }

    /**
     * Failure scenario: If a foreign entity does not have an identifier field, we should not be able to generate a
     * foreign key field.
     */
    public function testGenerateForeignKeyFieldWithoutIdentifierShouldThrowException()
    {
        $businessModel = new BusinessModel('TestEntity');
        $businessModel->addField(
            (new Field('id', FieldType::UUID))
                ->setManaged(true)
                ->setMandatory(true)
                ->setIdentifier(true)
        );
        self::assertNotNull($businessModel->getIdentifier());

        $foreignBusinessModel = new BusinessModel('ForeignEntity');
        $foreignBusinessModel->addField(new Field('id', FieldType::STRING));
        $foreignBusinessModel->addField(new Field('testEntityId', FieldType::STRING));
        self::assertNull($foreignBusinessModel->getIdentifier());

        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('No identifier available for foreign entity ForeignEntity');

        // Generate the missing fields
        $this->adapter->generateForeignKeyField($businessModel, $foreignBusinessModel);
    }

    /**
     * Failure scenario: If a foreign entity already has a field named with the foreign key, we should not be able to
     * generate a foreign key field.
     */
    public function testGenerateForeignKeyFieldWithExistingFieldNameShouldThrowException()
    {
        $businessModel = new BusinessModel('TestEntity');
        $businessModel->addField(
            (new Field('id', FieldType::UUID))
                ->setManaged(true)
                ->setMandatory(true)
                ->setIdentifier(true)
        );
        $businessModel->addField(new Field('foreignEntity', FieldType::STRING));
        self::assertNotNull($businessModel->getIdentifier());

        $foreignBusinessModel = new BusinessModel('ForeignEntity');
        $foreignBusinessModel->addField(
            (new Field('id', FieldType::UUID))
                ->setManaged(true)
                ->setMandatory(true)
                ->setIdentifier(true)
        );
        self::assertNotNull($foreignBusinessModel->getIdentifier());

        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('Cannot generate foreign key field on entity TestEntity: Field "foreignEntity" field is already defined. Did you provide the right type for this field?');

        // Generate the missing fields
        $this->adapter->generateForeignKeyField($businessModel, $foreignBusinessModel);
    }

    /**
     * @dataProvider getDatabaseFieldsProvider
     *
     * @param Field[] $expectedFields
     */
    public function testGetDatabaseFields(BusinessModel $businessModel, array $expectedFields)
    {
        $fields = $this->adapter->getDatabaseFields($businessModel);
        self::assertCount(count($expectedFields), $fields);
        foreach ($expectedFields as $field) {
            self::assertContains($field, $fields);
        }
    }

    public function getDatabaseFieldsProvider(): array
    {
        $businessBundle = TestHelper::getSampleBusinessBundle();

        $user = $businessBundle->getBusinessModel('User');

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
        ];
    }

    /**
     * @dataProvider getAuditedFieldsProvider
     *
     * @param Field[] $expectedFields
     */
    public function testGetAuditedFields(BusinessModel $businessModel, bool $includeId, array $expectedFields)
    {
        $fields = $this->adapter->getAuditedFields($businessModel, $includeId);
        self::assertCount(count($expectedFields), $fields);
        foreach ($expectedFields as $field) {
            self::assertContains($field, $fields);
        }
    }

    public function getAuditedFieldsProvider(): array
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
}
