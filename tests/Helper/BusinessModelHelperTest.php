<?php

namespace CodePrimer\Tests\Helper;

use CodePrimer\Helper\BusinessModelHelper;
use CodePrimer\Helper\FieldType;
use CodePrimer\Model\BusinessModel;
use CodePrimer\Model\Field;
use PHPUnit\Framework\TestCase;

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
        $package = TestHelper::getSamplePackage();
        $user = $package->getBusinessModel('User');
        $stats = $package->getBusinessModel('UserStats');
        $topic = $package->getBusinessModel('Topic');
        $post = $package->getBusinessModel('Post');
        $metadata = $package->getBusinessModel('Metadata');
        $subscription = $package->getBusinessModel('Subscription');

        $linkedEntities = $this->businessModelHelper->getLinkedBusinessModels($user);
        self::assertCount(5, $linkedEntities);
        self::assertContains($stats, $linkedEntities);
        self::assertContains($topic, $linkedEntities);
        self::assertContains($post, $linkedEntities);
        self::assertContains($metadata, $linkedEntities);
        self::assertContains($subscription, $linkedEntities);
    }
}
