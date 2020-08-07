<?php

namespace CodePrimer\Tests\Twig;

use CodePrimer\Adapter\RelationalDatabaseAdapter;
use CodePrimer\Helper\FieldType;
use CodePrimer\Model\BusinessModel;
use CodePrimer\Model\Constraint;
use CodePrimer\Model\Field;
use CodePrimer\Tests\Helper\TestHelper;
use CodePrimer\Twig\DoctrineOrmTwigExtension;

class DoctrineOrmTwigExtensionTest extends TwigExtensionTest
{
    /** @var DoctrineOrmTwigExtension */
    private $twigExtension;

    public function setUp(): void
    {
        parent::setUp();
        $this->twigExtension = new DoctrineOrmTwigExtension();
    }

    public function testGetFunctionsShouldPass()
    {
        $functions = $this->twigExtension->getFunctions();

        self::assertNotNull($functions);

        $this->assertTwigFunction('annotations', $functions);
    }

    public function testGetTestsShouldPass()
    {
        $tests = $this->twigExtension->getTests();

        self::assertNotNull($tests);

        $this->assertTwigTest('collectionUsed', $tests);
    }

    /**
     * @dataProvider entityAnnotationsProvider
     *
     * @param string[] $expected
     */
    public function testAnnotationsFunctionForBusinessModelShouldPass(BusinessModel $businessModel, array $expected)
    {
        $actual = $this->twigExtension->annotationsFunction($this->context, $businessModel);

        self::assertCount(count($expected), $actual);

        foreach ($expected as $value) {
            self::assertContains($value, $actual, 'Annotations found: '.implode("\n", $actual));
        }
    }

    public function entityAnnotationsProvider()
    {
        $field1 = new Field('name', FieldType::STRING);
        $field2 = new Field('email', FieldType::EMAIL);
        $index1 = new Field('firstName', FieldType::STRING);
        $index1->setSearchable(true);
        $index2 = new Field('lastName', FieldType::STRING);
        $index2->setSearchable(true);

        return [
            'Entity without constraints' => [
                new BusinessModel('SampleEntity'),
                [
                    '@ORM\Entity(repositoryClass="App\Repository\SampleEntityRepository")',
                    '@ORM\Table(name="sample_entities")',
                ],
            ],
            'Entity with 1 simple unique constraint' => [
                (new BusinessModel('SampleEntity'))
                    ->addField($field1)
                    ->addUniqueConstraint(new Constraint('uniqueName', Constraint::TYPE_UNIQUE, [$field1])),
                [
                    '@ORM\Entity(repositoryClass="App\Repository\SampleEntityRepository")',
                    '@ORM\Table(name="sample_entities", uniqueConstraints={@ORM\UniqueConstraint(name="uniqueName", columns={"name"})})',
                ],
            ],
            'Entity with 1 complex unique constraint' => [
                (new BusinessModel('SampleEntity'))
                    ->addField($field1)
                    ->addField($field2)
                    ->addUniqueConstraint(new Constraint('uniqueName', Constraint::TYPE_UNIQUE, [$field1, $field2])),
                [
                    '@ORM\Entity(repositoryClass="App\Repository\SampleEntityRepository")',
                    '@ORM\Table(name="sample_entities", uniqueConstraints={@ORM\UniqueConstraint(name="uniqueName", columns={"name","email"})})',
                ],
            ],
            'Entity with 2 simple unique constraints' => [
                (new BusinessModel('SampleEntity'))
                    ->addField($field1)
                    ->addField($field2)
                    ->addUniqueConstraint(new Constraint('uniqueName', Constraint::TYPE_UNIQUE, [$field1]))
                    ->addUniqueConstraint(new Constraint('uniqueEmail', Constraint::TYPE_UNIQUE, [$field2])),
                [
                    '@ORM\Entity(repositoryClass="App\Repository\SampleEntityRepository")',
                    '@ORM\Table(name="sample_entities", uniqueConstraints={@ORM\UniqueConstraint(name="uniqueName", columns={"name"}), @ORM\UniqueConstraint(name="uniqueEmail", columns={"email"})})',
                ],
            ],
            'Entity with 1 simple index' => [
                (new BusinessModel('SampleEntity'))
                    ->addField($index1),
                [
                    '@ORM\Entity(repositoryClass="App\Repository\SampleEntityRepository")',
                    '@ORM\Table(name="sample_entities", indexes={@ORM\Index(name="first_name_idx", columns={"first_name"})})',
                ],
            ],
            'Entity with 2 simple indexes' => [
                (new BusinessModel('SampleEntity'))
                    ->addField($index1)
                    ->addField($index2),
                [
                    '@ORM\Entity(repositoryClass="App\Repository\SampleEntityRepository")',
                    '@ORM\Table(name="sample_entities", indexes={@ORM\Index(name="first_name_idx", columns={"first_name"}), @ORM\Index(name="last_name_idx", columns={"last_name"})})',
                ],
            ],
            'Entity with 2 simple indexes and 2 unique constraints' => [
                (new BusinessModel('SampleEntity'))
                    ->addField($index1)
                    ->addField($index2)
                    ->addField($field1)
                    ->addField($field2)
                    ->addUniqueConstraint(new Constraint('uniqueName', Constraint::TYPE_UNIQUE, [$field1]))
                    ->addUniqueConstraint(new Constraint('uniqueEmail', Constraint::TYPE_UNIQUE, [$field2])),
                [
                    '@ORM\Entity(repositoryClass="App\Repository\SampleEntityRepository")',
                    '@ORM\Table(name="sample_entities", uniqueConstraints={@ORM\UniqueConstraint(name="uniqueName", columns={"name"}), @ORM\UniqueConstraint(name="uniqueEmail", columns={"email"})}, indexes={@ORM\Index(name="first_name_idx", columns={"first_name"}), @ORM\Index(name="last_name_idx", columns={"last_name"})})',
                ],
            ],
        ];
    }

    /**
     * @dataProvider fieldAnnotationsProvider
     *
     * @param string[] $expected
     */
    public function testAnnotationsFunctionForFieldShouldPass(Field $field, array $expected)
    {
        $actual = $this->twigExtension->annotationsFunction($this->context, $field);

        self::assertCount(count($expected), $actual);

        foreach ($expected as $value) {
            self::assertContains($value, $actual, 'Annotations found: '.implode("\n", $actual));
        }
    }

    public function fieldAnnotationsProvider()
    {
        $businessBundle = TestHelper::getSampleBusinessBundle();
        // Generate the missing fields
        $adapter = new RelationalDatabaseAdapter();
        $adapter->generateRelationalFields($businessBundle);

        $user = $businessBundle->getBusinessModel('User');
        $subscription = $businessBundle->getBusinessModel('Subscription');
        $metadata = $businessBundle->getBusinessModel('Metadata');
        $topic = $businessBundle->getBusinessModel('Topic');

        return [
            'Optional String field' => [
                new Field('SampleField', FieldType::STRING, 'description'),
                [
                    '@ORM\Column(name="sample_field", type="string", length=255, nullable=true)',
                ],
            ],
            'Mandatory String field' => [
                new Field('SampleField', FieldType::STRING, 'description', true),
                [
                    '@ORM\Column(name="sample_field", type="string", length=255)',
                ],
            ],
            'Mandatory Email field' => [
                new Field('SampleField', FieldType::EMAIL, 'description', true),
                [
                    '@ORM\Column(name="sample_field", type="string", length=255)',
                ],
            ],
            'Mandatory URL field' => [
                new Field('SampleField', FieldType::URL, 'description', true),
                [
                    '@ORM\Column(name="sample_field", type="string", length=255)',
                ],
            ],
            'Mandatory Text field' => [
                new Field('SampleField', FieldType::TEXT, 'description', true),
                [
                    '@ORM\Column(name="sample_field", type="text")',
                ],
            ],
            'Mandatory Phone field' => [
                new Field('SampleField', FieldType::PHONE, 'description', true),
                [
                    '@ORM\Column(name="sample_field", type="string", length=15)',
                ],
            ],
            'Mandatory Price field' => [
                new Field('SampleField', FieldType::PRICE, 'description', true),
                [
                    '@ORM\Column(name="sample_field", type="decimal", precision=9, scale=2)',
                ],
            ],
            'Mandatory Decimal field' => [
                new Field('SampleField', FieldType::DECIMAL, 'description', true),
                [
                    '@ORM\Column(name="sample_field", type="decimal")',
                ],
            ],
            'Mandatory Double field' => [
                new Field('SampleField', FieldType::DOUBLE, 'description', true),
                [
                    '@ORM\Column(name="sample_field", type="float")',
                ],
            ],
            'Mandatory Float field' => [
                new Field('SampleField', FieldType::FLOAT, 'description', true),
                [
                    '@ORM\Column(name="sample_field", type="float")',
                ],
            ],
            'Mandatory Long field' => [
                new Field('SampleField', FieldType::LONG, 'description', true),
                [
                    '@ORM\Column(name="sample_field", type="bigint")',
                ],
            ],
            'Mandatory Integer field' => [
                new Field('SampleField', FieldType::INTEGER, 'description', true),
                [
                    '@ORM\Column(name="sample_field", type="integer")',
                ],
            ],
            'Mandatory DateTime field' => [
                new Field('SampleField', FieldType::DATETIME, 'description', true),
                [
                    '@ORM\Column(name="sample_field", type="datetime")',
                ],
            ],
            'Mandatory Time field' => [
                new Field('SampleField', FieldType::TIME, 'description', true),
                [
                    '@ORM\Column(name="sample_field", type="time")',
                ],
            ],
            'Mandatory Date field' => [
                new Field('SampleField', FieldType::DATE, 'description', true),
                [
                    '@ORM\Column(name="sample_field", type="date")',
                ],
            ],
            'Mandatory Boolean field' => [
                new Field('SampleField', FieldType::BOOLEAN, 'description', true),
                [
                    '@ORM\Column(name="sample_field", type="boolean")',
                ],
            ],
            'Mandatory ID field' => [
                new Field('SampleField', FieldType::ID, 'description', true),
                [
                    '@ORM\Id()',
                    '@ORM\GeneratedValue()',
                    '@ORM\Column(name="sample_field", type="bigint")',
                ],
            ],
            'Mandatory UUID field' => [
                new Field('SampleField', FieldType::UUID, 'description', true),
                [
                    '@ORM\Id()',
                    '@ORM\GeneratedValue(strategy="UUID")',
                    '@ORM\Column(name="sample_field", type="string", length=36)',
                ],
            ],
            'User.id' => [
                $user->getField('id'),
                [
                    '@ORM\Id()',
                    '@ORM\GeneratedValue(strategy="UUID")',
                    '@ORM\Column(name="id", type="string", length=36)',
                ],
            ],
            'User.firstName' => [
                $user->getField('firstName'),
                [
                    '@ORM\Column(name="first_name", type="string", length=255, nullable=true)',
                ],
            ],
            'User.email' => [
                $user->getField('email'),
                [
                    '@ORM\Column(name="email", type="string", length=255)',
                ],
            ],
            'User.created' => [
                $user->getField('created'),
                [
                    '@ORM\Column(name="created", type="datetime", nullable=true)',
                ],
            ],
            'OneToOne Entity - Unidirectional' => [
                $user->getField('stats'),
                [
                    '@ORM\OneToOne(targetEntity="CodePrimer\Tests\UserStats", cascade={"persist", "remove"})',
                ],
            ],
            'OneToOne Entity - Bidirectional - Left side' => [
                $user->getField('subscription'),
                [
                    '@ORM\OneToOne(targetEntity="CodePrimer\Tests\Subscription", inversedBy="user")',
                ],
            ],
            'OneToOne Entity - Bidirectional - Right side' => [
                $subscription->getField('user'),
                [
                    '@ORM\OneToOne(targetEntity="CodePrimer\Tests\User", inversedBy="subscription")',
                ],
            ],
            'OneToMany Entities' => [
                $user->getField('metadata'),
                [
                    '@ORM\OneToMany(targetEntity="CodePrimer\Tests\Metadata", mappedBy="user", cascade={"persist", "remove", "merge"}, orphanRemoval=true)',
                ],
            ],
            'ManyToOne Entities' => [
                $metadata->getField('user'),
                [
                    '@ORM\ManyToOne(targetEntity="CodePrimer\Tests\User", inversedBy="metadata")',
                ],
            ],
            'ManyToMany Entities - Side 1' => [
                $user->getField('topics'),
                [
                    '@ORM\ManyToMany(targetEntity="CodePrimer\Tests\Topic", mappedBy="authors")',
                ],
            ],
            'ManyToMany Entities - Side 2' => [
                $topic->getField('authors'),
                [
                    '@ORM\ManyToMany(targetEntity="CodePrimer\Tests\User", inversedBy="topics")',
                ],
            ],
        ];
    }

    /**
     * @dataProvider collectionUsedProvider
     *
     * @param BusinessModel|Field $obj
     */
    public function testCollectionUsedShouldPass($obj, bool $expected)
    {
        self::assertEquals($expected, $this->twigExtension->collectionUsedTest($obj));
    }

    public function collectionUsedProvider()
    {
        $businessBundle = TestHelper::getSampleBusinessBundle();
        $user = $businessBundle->getBusinessModel('User');

        return [
            'User' => [$user, true],
            'UserStats' => [$businessBundle->getBusinessModel('UserStats'), false],
            'Metadata' => [$businessBundle->getBusinessModel('Metadata'), false],
            'Post' => [$businessBundle->getBusinessModel('Post'), false],
            'Topic' => [$businessBundle->getBusinessModel('Topic'), true],
            'Field' => [new Field('SampleField', FieldType::UUID), false],
            'List Field without relation' => [
                (new Field('SampleField', FieldType::STRING))
                    ->setList(true),
                false,
            ],
            'Field with relation' => [$user->getField('stats'), false],
            'List wield with relation' => [$user->getField('metadata'), true],
        ];
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
        $user = $businessBundle->getBusinessModel('User');

        return [
            'id' => [$user->getField('id'), 'string'],
            'FirstName' => [$user->getField('firstName'), '?string'],
            'Email' => [$user->getField('email'), 'string'],
            'DateTime' => [$user->getField('created'), '?DateTimeInterface'],
            'Optional Entity' => [$user->getField('stats'), '?UserStats'],
            'OneToMany Entities' => [$user->getField('metadata'), 'Collection'],
            'ManyToMany Entities' => [$user->getField('topics'), 'Collection'],
        ];
    }

    /**
     * @dataProvider hintDataProvider
     *
     * @param mixed  $obj           Object to filter
     * @param string $expectedValue expected filtered value
     */
    public function testHintFilterShouldPass($obj, $expectedValue)
    {
        $value = $this->twigExtension->hintFilter($this->context, $obj);

        self::assertEquals($expectedValue, $value);
    }

    public function hintDataProvider()
    {
        $businessBundle = TestHelper::getSampleBusinessBundle();
        $user = $businessBundle->getBusinessModel('User');

        return [
            'id' => [$user->getField('id'), 'string'],
            'FirstName' => [$user->getField('firstName'), 'string|null'],
            'Email' => [$user->getField('email'), 'string'],
            'DateTime' => [$user->getField('created'), 'DateTimeInterface|null'],
            'Optional Entity' => [$user->getField('stats'), 'UserStats|null'],
            'OneToMany Entities' => [$user->getField('metadata'), 'Collection|Metadata[]|null'],
            'ManyToMany Entities' => [$user->getField('topics'), 'Collection|Topic[]|null'],
        ];
    }
}
