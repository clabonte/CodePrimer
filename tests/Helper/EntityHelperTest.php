<?php

namespace CodePrimer\Tests\Helper;

use CodePrimer\Helper\EntityHelper;
use CodePrimer\Helper\FieldType;
use CodePrimer\Model\BusinessModel;
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
     */
    public function testGetRepositoryClass(BusinessModel $businessModel, string $expected)
    {
        self::assertEquals($expected, $this->entityHelper->getRepositoryClass($businessModel));
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
     * @dataProvider getEntityCreatedTimestampFieldProvider
     */
    public function testGetEntityCreatedTimestampField(BusinessModel $businessModel, ?Field $expected)
    {
        self::assertEquals($expected, $this->entityHelper->getEntityCreatedTimestampField($businessModel));
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
     * @dataProvider getEntityUpdatedTimestampFieldProvider
     */
    public function testGetEntityUpdatedTimestampField(BusinessModel $businessModel, ?Field $expected)
    {
        self::assertEquals($expected, $this->entityHelper->getEntityUpdatedTimestampField($businessModel));
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
        self::assertEquals($expected, $this->entityHelper->isManagedTimestamp($businessModel));
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
