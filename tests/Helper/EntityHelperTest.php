<?php

namespace CodePrimer\Tests\Helper;

use CodePrimer\Helper\EntityHelper;
use CodePrimer\Helper\FieldType;
use CodePrimer\Model\Entity;
use CodePrimer\Model\Field;
use PHPUnit\Framework\TestCase;

class EntityHelperTest extends TestCase
{
    /** @var EntityHelper */
    private $entityHelper;

    public function setUp(): void
    {
        parent::setUp();
        $this->entityHelper = new EntityHelper();
    }

    /**
     * @dataProvider repositoryClassProvider
     * @param Entity $entity
     * @param string $expected
     */
    public function testGetRepositoryClass(Entity $entity, string $expected)
    {
        self::assertEquals($expected, $this->entityHelper->getRepositoryClass($entity));
    }

    public function repositoryClassProvider()
    {
        return [
            'Sample Name' => [new Entity('Sample Name'), 'SampleNameRepository'],
            'SampleName' => [new Entity('SampleName'), 'SampleNameRepository'],
            'SampleNames' => [new Entity('SampleNames'), 'SampleNamesRepository'],
            'Sample Names' => [new Entity('Sample Names'), 'SampleNamesRepository'],
            'Samples Names' => [new Entity('Samples Names'), 'SamplesNamesRepository']
        ];
    }

    /**
     * @dataProvider getEntityCreatedTimestampFieldProvider
     * @param Entity $entity
     * @param Field|null $expected
     */
    public function testGetEntityCreatedTimestampField(Entity $entity, ?Field $expected)
    {
        self::assertEquals($expected, $this->entityHelper->getEntityCreatedTimestampField($entity));
    }

    public function getEntityCreatedTimestampFieldProvider()
    {
        $unmanagedField = new Field('created', FieldType::DATETIME);
        $managedField = new Field('created', FieldType::DATETIME);
        $managedField->setManaged(true);
        $updatedField = new Field('updated', FieldType::DATETIME);
        $updatedField->setManaged(true);

        return [
            'unmanaged created field' => [
                (new Entity('Sample Entity'))
                    ->addField($unmanagedField),
                null
            ],
            'managed created field' => [
                (new Entity('Sample Entity'))
                    ->addField($managedField),
                $managedField
            ],
            'managed updated field' => [
                (new Entity('Sample Entity'))
                    ->addField($updatedField),
                null
            ],
        ];
    }

    /**
     * @dataProvider getEntityUpdatedTimestampFieldProvider
     * @param Entity $entity
     * @param Field|null $expected
     */
    public function testGetEntityUpdatedTimestampField(Entity $entity, ?Field $expected)
    {
        self::assertEquals($expected, $this->entityHelper->getEntityUpdatedTimestampField($entity));
    }

    public function getEntityUpdatedTimestampFieldProvider()
    {
        $unmanagedField = new Field('updated', FieldType::DATETIME);
        $managedField = new Field('updated', FieldType::DATETIME);
        $managedField->setManaged(true);
        $createdField = new Field('created', FieldType::DATETIME);
        $createdField->setManaged(true);

        return [
            'unmanaged updated field' => [
                (new Entity('Sample Entity'))
                    ->addField($unmanagedField),
                null
            ],
            'managed updated field' => [
                (new Entity('Sample Entity'))
                    ->addField($managedField),
                $managedField
            ],
            'managed created field' => [
                (new Entity('Sample Entity'))
                    ->addField($createdField),
                null
            ],
        ];
    }

    /**
     * @dataProvider isManagedTimestampProvider
     * @param Entity $entity
     * @param bool $expected
     */
    public function testIsManagedTimestamp(Entity $entity, bool $expected)
    {
        self::assertEquals($expected, $this->entityHelper->isManagedTimestamp($entity));
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
                (new Entity('Sample Entity'))
                    ->addField($unmanagedUpdatedField)
                    ->addField($unmanagedCreatedField),
                false
            ],
            'managed updated field' => [
                (new Entity('Sample Entity'))
                    ->addField($managedUpdatedField)
                    ->addField($unmanagedCreatedField),
                true
            ],
            'managed created field' => [
                (new Entity('Sample Entity'))
                    ->addField($managedCreatedField)
                    ->addField($unmanagedUpdatedField),
                true
            ],
            'managed created and updated fields' => [
                (new Entity('Sample Entity'))
                    ->addField($managedCreatedField)
                    ->addField($managedUpdatedField),
                true
            ],
        ];
    }

    public function testGetLinkedEntitiesShouldPass()
    {
        $package = TestHelper::getSamplePackage();
        $user = $package->getEntity('User');
        $stats = $package->getEntity('UserStats');
        $topic = $package->getEntity('Topic');
        $post = $package->getEntity('Post');
        $metadata = $package->getEntity('Metadata');
        $subscription = $package->getEntity('Subscription');

        $linkedEntities = $this->entityHelper->getLinkedEntities($user);
        self::assertCount(5, $linkedEntities);
        self::assertContains($stats, $linkedEntities);
        self::assertContains($topic, $linkedEntities);
        self::assertContains($post, $linkedEntities);
        self::assertContains($metadata, $linkedEntities);
        self::assertContains($subscription, $linkedEntities);
    }
}
