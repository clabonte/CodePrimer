<?php

namespace CodePrimer\Tests\Helper;

use CodePrimer\Helper\BusinessModelHelper;
use CodePrimer\Helper\FieldType;
use CodePrimer\Model\BusinessModel;
use CodePrimer\Model\Field;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class BusinessModelHelperTest extends TestCase
{
    /** @var BusinessModelHelper */
    private $businessModelHelper;

    public function setUp(): void
    {
        parent::setUp();
        $this->businessModelHelper = new BusinessModelHelper();
    }

    /**
     * @dataProvider repositoryClassProvider
     */
    public function testGetRepositoryClass(BusinessModel $businessModel, string $expected)
    {
        self::assertEquals($expected, $this->businessModelHelper->getRepositoryClass($businessModel));
    }

    public function repositoryClassProvider()
    {
        return [
            'Sample Name' => [new BusinessModel('Sample Name'), 'SampleNameRepository'],
            'SampleName' => [new BusinessModel('SampleName'), 'SampleNameRepository'],
            'SampleNames' => [new BusinessModel('SampleNames'), 'SampleNamesRepository'],
            'Sample Names' => [new BusinessModel('Sample Names'), 'SampleNamesRepository'],
            'Samples Names' => [new BusinessModel('Samples Names'), 'SamplesNamesRepository'],
        ];
    }

    /**
     * @dataProvider getCreatedTimestampFieldProvider
     */
    public function testGetCreatedTimestampField(BusinessModel $businessModel, ?Field $expected)
    {
        self::assertEquals($expected, $this->businessModelHelper->getCreatedTimestampField($businessModel));
    }

    public function getCreatedTimestampFieldProvider()
    {
        $unmanagedField = new Field('created', FieldType::DATETIME);
        $managedField = new Field('created', FieldType::DATETIME);
        $managedField->setManaged(true);
        $updatedField = new Field('updated', FieldType::DATETIME);
        $updatedField->setManaged(true);

        return [
            'unmanaged created field' => [
                (new BusinessModel('Sample Entity'))
                    ->addField($unmanagedField),
                null,
            ],
            'managed created field' => [
                (new BusinessModel('Sample Entity'))
                    ->addField($managedField),
                $managedField,
            ],
            'managed updated field' => [
                (new BusinessModel('Sample Entity'))
                    ->addField($updatedField),
                null,
            ],
        ];
    }

    /**
     * @dataProvider getUpdatedTimestampFieldProvider
     */
    public function testGetUpdatedTimestampField(BusinessModel $businessModel, ?Field $expected)
    {
        self::assertEquals($expected, $this->businessModelHelper->getUpdatedTimestampField($businessModel));
    }

    public function getUpdatedTimestampFieldProvider()
    {
        $unmanagedField = new Field('updated', FieldType::DATETIME);
        $managedField = new Field('updated', FieldType::DATETIME);
        $managedField->setManaged(true);
        $createdField = new Field('created', FieldType::DATETIME);
        $createdField->setManaged(true);

        return [
            'unmanaged updated field' => [
                (new BusinessModel('Sample Entity'))
                    ->addField($unmanagedField),
                null,
            ],
            'managed updated field' => [
                (new BusinessModel('Sample Entity'))
                    ->addField($managedField),
                $managedField,
            ],
            'managed created field' => [
                (new BusinessModel('Sample Entity'))
                    ->addField($createdField),
                null,
            ],
        ];
    }

    /**
     * @dataProvider isManagedTimestampProvider
     */
    public function testIsManagedTimestamp(BusinessModel $businessModel, bool $expected)
    {
        self::assertEquals($expected, $this->businessModelHelper->isManagedTimestamp($businessModel));
    }

    public function isManagedTimestampProvider()
    {
        $unmanagedCreatedField = new Field('created', FieldType::DATETIME);

        $unmanagedUpdatedField = new Field('updated', FieldType::DATETIME);

        $managedUpdatedField = new Field('updated', FieldType::DATETIME);
        $managedUpdatedField->setManaged(true);

        $managedCreatedField = new Field('created', FieldType::DATETIME);
        $managedCreatedField->setManaged(true);

        return [
            'unmanaged fields' => [
                (new BusinessModel('Sample Entity'))
                    ->addField($unmanagedUpdatedField)
                    ->addField($unmanagedCreatedField),
                false,
            ],
            'managed updated field' => [
                (new BusinessModel('Sample Entity'))
                    ->addField($managedUpdatedField)
                    ->addField($unmanagedCreatedField),
                true,
            ],
            'managed created field' => [
                (new BusinessModel('Sample Entity'))
                    ->addField($managedCreatedField)
                    ->addField($unmanagedUpdatedField),
                true,
            ],
            'managed created and updated fields' => [
                (new BusinessModel('Sample Entity'))
                    ->addField($managedCreatedField)
                    ->addField($managedUpdatedField),
                true,
            ],
        ];
    }

    public function testGetLinkedEntitiesShouldPass()
    {
        $businessBundle = TestHelper::getSampleBusinessBundle();
        $user = $businessBundle->getBusinessModel('User');
        $stats = $businessBundle->getBusinessModel('UserStats');
        $topic = $businessBundle->getBusinessModel('Topic');
        $post = $businessBundle->getBusinessModel('Post');
        $metadata = $businessBundle->getBusinessModel('Metadata');
        $subscription = $businessBundle->getBusinessModel('Subscription');

        $linkedEntities = $this->businessModelHelper->getLinkedBusinessModels($user);
        self::assertCount(5, $linkedEntities);
        self::assertContains($stats, $linkedEntities);
        self::assertContains($topic, $linkedEntities);
        self::assertContains($post, $linkedEntities);
        self::assertContains($metadata, $linkedEntities);
        self::assertContains($subscription, $linkedEntities);
    }

    public function testListBusinessAttributesFieldsShouldPass()
    {
        $businessBundle = TestHelper::getSampleBusinessBundle();
        $user = $businessBundle->getBusinessModel('User');
        $user->addField(new Field('bizAttribute', 'NotAModel'));

        $fields = $this->businessModelHelper->listBusinessAttributeFields($user, $businessBundle);
        self::assertCount(7, $fields);
        self::assertContains($user->getField('firstName'), $fields);
        self::assertContains($user->getField('lastName'), $fields);
        self::assertContains($user->getField('nickname'), $fields);
        self::assertContains($user->getField('email'), $fields);
        self::assertContains($user->getField('password'), $fields);
        self::assertContains($user->getField('crmId'), $fields);
        self::assertContains($user->getField('bizAttribute'), $fields);
    }

    /**
     * @dataProvider identifierFieldProvider
     */
    public function testGenerateIdentifierFieldShouldPass(BusinessModel $businessModel, string $expectedName, string $expectedType)
    {
        self::assertNull($businessModel->getField($expectedName));

        $field = $this->businessModelHelper->generateIdentifierField($businessModel, $expectedType);
        self::assertNotNull($field);
        self::assertEquals($expectedName, $field->getName());
        self::assertEquals($expectedType, $field->getType());
        self::assertTrue($field->isManaged());
        self::assertTrue($field->isMandatory());
        self::assertTrue($field->isGenerated());
        self::assertNotEmpty($field->getExample());
        self::assertNotNull($businessModel->getField($expectedName));
    }

    public function identifierFieldProvider()
    {
        $businessBundle = TestHelper::getSampleBusinessBundle();
        $user = $businessBundle->getBusinessModel('User');
        $topic = $businessBundle->getBusinessModel('Topic');

        return [
            'Topic - UUID' => [$topic, 'id', FieldType::UUID],
            'User - ID' => [$user, 'userId', FieldType::ID],
        ];
    }

    public function testGenerateIdentifierFieldWithUnavailableNameThrowsException()
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Cannot generate ID field for business model User: "id" and "userId" fields are already defined. Did you forget to specify an identifier for this model?');

        $businessBundle = TestHelper::getSampleBusinessBundle();
        $user = $businessBundle->getBusinessModel('User');
        $user->addField(new Field('userId', FieldType::STRING));
        $this->businessModelHelper->generateIdentifierField($user);
    }

    /**
     * @dataProvider timestampFieldsProvider
     */
    public function testGenerateTimestampFieldsShouldWork(BusinessModel $businessModel, bool $created, bool $updated)
    {
        self::assertNull($businessModel->getField('created'));
        self::assertNull($businessModel->getField('updated'));

        $this->businessModelHelper->generateTimestampFields($businessModel, $created, $updated);
        if ($created) {
            $field = $businessModel->getField('created');
            self::assertNotNull($field);
            self::assertEquals(FieldType::DATETIME, $field->getType());
            self::assertTrue($field->isManaged());
            self::assertFalse($field->isMandatory());
            self::assertTrue($field->isGenerated());
            self::assertNotEmpty($field->getExample());
        }
        if ($updated) {
            $field = $businessModel->getField('updated');
            self::assertNotNull($field);
            self::assertEquals(FieldType::DATETIME, $field->getType());
            self::assertTrue($field->isManaged());
            self::assertFalse($field->isMandatory());
            self::assertTrue($field->isGenerated());
            self::assertNotEmpty($field->getExample());
        }
    }

    public function timestampFieldsProvider()
    {
        return [
            'Both' => [new BusinessModel('TestModel'), true, true],
            'Created only' => [new BusinessModel('TestModel'), true, false],
            'Updated only' => [new BusinessModel('TestModel'), false, true],
            'None' => [new BusinessModel('TestModel'), false, false],
        ];
    }
}
