<?php

namespace CodePrimer\Tests\Model;

use CodePrimer\Model\BusinessBundle;
use CodePrimer\Model\BusinessModel;
use CodePrimer\Model\Derived\Event;
use CodePrimer\Model\Set;
use PHPUnit\Framework\TestCase;

class PackageTest extends TestCase
{
    /** @var BusinessBundle */
    private $package;

    public function setUp(): void
    {
        parent::setUp();
        $this->package = new BusinessBundle('TestNamespace', 'TestName');
    }

    public function testBasicSetters()
    {
        self::assertEquals('TestName', $this->package->getName());

        $this->package
            ->setName('NewName')
            ->setDescription('New Description');

        self::assertEquals('NewName', $this->package->getName());
        self::assertEquals('New Description', $this->package->getDescription());
    }

    /**
     * @param $expected
     * @dataProvider namespaceProvider
     */
    public function testSetNamespace(string $namespace, $expected)
    {
        $this->package->setNamespace($namespace);
        self::assertEquals($expected, $this->package->getNamespace());

        $package = new BusinessBundle($namespace, $namespace);
        self::assertEquals($expected, $package->getNamespace());
        self::assertEquals($namespace, $package->getName());
    }

    public function namespaceProvider()
    {
        return [
            'Backslash' => ['Test\Namespace', 'Test\Namespace'],
            'Slash' => ['Test/Namespace', 'Test/Namespace'],
            'Dot' => ['Test.Namespace', 'Test.Namespace'],
            'Space' => ['Test Namespace', 'Test Namespace'],
            'No space' => ['TestNamespace', 'TestNamespace'],
            'Trim backslash' => ['\Test\Namespace\\', '\Test\Namespace'],
            'Trim slash' => ['/Test/Namespace/', '/Test/Namespace'],
        ];
    }

    public function testAddBusinessModel()
    {
        $this->package
            ->addBusinessModel(new BusinessModel('TestData1', 'description1'))
            ->addBusinessModel(new BusinessModel('TestData2', 'description2'))
            ->addBusinessModel(new BusinessModel('TestData3', 'description3'))
            ->addBusinessModel(new BusinessModel('TestData4', 'description4'));

        // Make sure all data is present and unaltered
        self::assertCount(4, $this->package->getBusinessModels());

        $businessModel = $this->package->getBusinessModel('TestData1');
        self::assertNotNull($businessModel, 'TestData1 not found');
        self::assertEquals('TestData1', $businessModel->getName());
        self::assertEquals('description1', $businessModel->getDescription());

        $businessModel = $this->package->getBusinessModel('TestData2');
        self::assertNotNull($businessModel, 'TestData2 not found');
        self::assertEquals('TestData2', $businessModel->getName());
        self::assertEquals('description2', $businessModel->getDescription());

        $businessModel = $this->package->getBusinessModel('TestData3');
        self::assertNotNull($businessModel, 'TestData3 not found');
        self::assertEquals('TestData3', $businessModel->getName());
        self::assertEquals('description3', $businessModel->getDescription());

        $businessModel = $this->package->getBusinessModel('TestData4');
        self::assertNotNull($businessModel, 'TestData4 not found');
        self::assertEquals('TestData4', $businessModel->getName());
        self::assertEquals('description4', $businessModel->getDescription());
    }

    public function testAddDuplicateBusinessModelKeepsLast()
    {
        $this->package
            ->addBusinessModel(new BusinessModel('TestData1', 'description1'))
            ->addBusinessModel(new BusinessModel('TestData2', 'description2'))
            ->addBusinessModel(new BusinessModel('TestData3', 'description3'))
            ->addBusinessModel(new BusinessModel('TestData4', 'description4'));

        $this->package->addBusinessModel(new BusinessModel('TestData1', 'description5'));

        // Make sure TestData1 has been replaced
        self::assertCount(4, $this->package->getBusinessModels());

        $businessModel = $this->package->getBusinessModel('TestData1');
        self::assertNotNull($businessModel, 'TestData1 not found');
        self::assertEquals('TestData1', $businessModel->getName());
        self::assertEquals('description5', $businessModel->getDescription());
    }

    public function testSetEntities()
    {
        $this->package
            ->addBusinessModel(new BusinessModel('TestData1', 'description1'))
            ->addBusinessModel(new BusinessModel('TestData2', 'description2'))
            ->addBusinessModel(new BusinessModel('TestData3', 'description3'))
            ->addBusinessModel(new BusinessModel('TestData4', 'description4'));

        $entities = [
            new BusinessModel('TestData5', 'description5'),
            new BusinessModel('TestData6', 'description6'),
        ];

        $this->package->setBusinessModels($entities);
        self::assertCount(2, $this->package->getBusinessModels());

        $businessModel = $this->package->getBusinessModel('TestData5');
        self::assertNotNull($businessModel, 'TestData5 not found');
        self::assertEquals('TestData5', $businessModel->getName());
        self::assertEquals('description5', $businessModel->getDescription());

        $businessModel = $this->package->getBusinessModel('TestData6');
        self::assertNotNull($businessModel, 'TestData6 not found');
        self::assertEquals('TestData6', $businessModel->getName());
        self::assertEquals('description6', $businessModel->getDescription());

        self::assertNull($this->package->getBusinessModel('TestData1'));
        self::assertNull($this->package->getBusinessModel('TestData2'));
        self::assertNull($this->package->getBusinessModel('TestData3'));
        self::assertNull($this->package->getBusinessModel('TestData4'));
    }

    public function testAddEvent()
    {
        $this->package
            ->addEvent(new Event('TestEvent1', 'description1'))
            ->addEvent(new Event('TestEvent2', 'description2'))
            ->addEvent(new Event('TestEvent3', 'description3'));

        self::assertCount(3, $this->package->getEvents());

        $event = $this->package->getEvent('TestEvent1');
        self::assertNotNull($event, 'TestEvent1 not found');
        self::assertEquals('TestEvent1', $event->getName());
        self::assertEquals('description1', $event->getDescription());

        $event = $this->package->getEvent('TestEvent2');
        self::assertNotNull($event, 'TestEvent2 not found');
        self::assertEquals('TestEvent2', $event->getName());
        self::assertEquals('description2', $event->getDescription());

        $event = $this->package->getEvent('TestEvent3');
        self::assertNotNull($event, 'TestEvent3 not found');
        self::assertEquals('TestEvent3', $event->getName());
        self::assertEquals('description3', $event->getDescription());
    }

    public function testSetEvents()
    {
        $this->package
            ->addEvent(new Event('TestEvent1', 'description1'))
            ->addEvent(new Event('TestEvent2', 'description2'))
            ->addEvent(new Event('TestEvent3', 'description3'));

        $events = [
            new Event('TestEvent4', 'description4'),
            new Event('TestEvent5', 'description5'),
        ];

        $this->package->setEvents($events);

        self::assertCount(2, $this->package->getEvents());

        $event = $this->package->getEvent('TestEvent4');
        self::assertNotNull($event, 'TestEvent4 not found');
        self::assertEquals('TestEvent4', $event->getName());
        self::assertEquals('description4', $event->getDescription());

        $event = $this->package->getEvent('TestEvent5');
        self::assertNotNull($event, 'TestEvent5 not found');
        self::assertEquals('TestEvent5', $event->getName());
        self::assertEquals('description5', $event->getDescription());

        self::assertNull($this->package->getEvent('TestEvent1'));
        self::assertNull($this->package->getEvent('TestEvent2'));
        self::assertNull($this->package->getEvent('TestEvent3'));
    }

    public function testAddSet()
    {
        $this->package
            ->addSet(new Set('TestSet1', 'description1'))
            ->addSet(new Set('TestSet2', 'description2'))
            ->addSet(new Set('TestSet3', 'description3'));

        self::assertCount(3, $this->package->listSets());

        $set = $this->package->getSet('TestSet1');
        self::assertNotNull($set, 'TestSet1 not found');
        self::assertEquals('TestSet1', $set->getName());
        self::assertEquals('description1', $set->getDescription());

        $set = $this->package->getSet('TestSet2');
        self::assertNotNull($set, 'TestSet2 not found');
        self::assertEquals('TestSet2', $set->getName());
        self::assertEquals('description2', $set->getDescription());

        $set = $this->package->getSet('TestSet3');
        self::assertNotNull($set, 'TestSet3 not found');
        self::assertEquals('TestSet3', $set->getName());
        self::assertEquals('description3', $set->getDescription());

        self::assertNull($this->package->getSet('TestSet4'));
    }

    public function testSetSets()
    {
        $this->package
            ->addSet(new Set('TestSet1', 'description1'))
            ->addSet(new Set('TestSet2', 'description2'))
            ->addSet(new Set('TestSet3', 'description3'));

        $sets = [
            new Set('TestSet4', 'description4'),
            new Set('TestSet5', 'description5'),
        ];

        $this->package->setSets($sets);

        self::assertCount(2, $this->package->listSets());

        $set = $this->package->getSet('TestSet4');
        self::assertNotNull($set, 'TestSet4 not found');
        self::assertEquals('TestSet4', $set->getName());
        self::assertEquals('description4', $set->getDescription());

        $set = $this->package->getSet('TestSet5');
        self::assertNotNull($set, 'TestSet5 not found');
        self::assertEquals('TestSet5', $set->getName());
        self::assertEquals('description5', $set->getDescription());

        self::assertNull($this->package->getSet('TestSet1'));
        self::assertNull($this->package->getSet('TestSet2'));
        self::assertNull($this->package->getSet('TestSet3'));
    }
}
