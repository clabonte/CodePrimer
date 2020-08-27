<?php

namespace CodePrimer\Tests\Model;

use CodePrimer\Model\BusinessBundle;
use CodePrimer\Model\BusinessModel;
use CodePrimer\Model\BusinessProcess;
use CodePrimer\Model\DataSet;
use CodePrimer\Model\Derived\Event;
use CodePrimer\Tests\Helper\TestHelper;
use PHPUnit\Framework\TestCase;

class BusinessBundleTest extends TestCase
{
    /** @var BusinessBundle */
    private $businessBundle;

    public function setUp(): void
    {
        parent::setUp();
        $this->businessBundle = new BusinessBundle('TestNamespace', 'TestName');
    }

    public function testBasicSetters()
    {
        self::assertEquals('TestName', $this->businessBundle->getName());

        $this->businessBundle
            ->setName('NewName')
            ->setDescription('New Description');

        self::assertEquals('NewName', $this->businessBundle->getName());
        self::assertEquals('New Description', $this->businessBundle->getDescription());
    }

    /**
     * @param $expected
     * @dataProvider namespaceProvider
     */
    public function testSetNamespace(string $namespace, $expected)
    {
        $this->businessBundle->setNamespace($namespace);
        self::assertEquals($expected, $this->businessBundle->getNamespace());

        $businessBundle = new BusinessBundle($namespace, $namespace);
        self::assertEquals($expected, $businessBundle->getNamespace());
        self::assertEquals($namespace, $businessBundle->getName());
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
        $this->businessBundle
            ->addBusinessModel(new BusinessModel('TestData1', 'description1'))
            ->addBusinessModel(new BusinessModel('TestData2', 'description2'))
            ->addBusinessModel(new BusinessModel('TestData3', 'description3'))
            ->addBusinessModel(new BusinessModel('TestData4', 'description4'));

        // Make sure all data is present and unaltered
        self::assertCount(4, $this->businessBundle->getBusinessModels());

        $businessModel = $this->businessBundle->getBusinessModel('TestData1');
        self::assertNotNull($businessModel, 'TestData1 not found');
        self::assertEquals('TestData1', $businessModel->getName());
        self::assertEquals('description1', $businessModel->getDescription());

        $businessModel = $this->businessBundle->getBusinessModel('TestData2');
        self::assertNotNull($businessModel, 'TestData2 not found');
        self::assertEquals('TestData2', $businessModel->getName());
        self::assertEquals('description2', $businessModel->getDescription());

        $businessModel = $this->businessBundle->getBusinessModel('TestData3');
        self::assertNotNull($businessModel, 'TestData3 not found');
        self::assertEquals('TestData3', $businessModel->getName());
        self::assertEquals('description3', $businessModel->getDescription());

        $businessModel = $this->businessBundle->getBusinessModel('TestData4');
        self::assertNotNull($businessModel, 'TestData4 not found');
        self::assertEquals('TestData4', $businessModel->getName());
        self::assertEquals('description4', $businessModel->getDescription());
    }

    public function testAddDuplicateBusinessModelKeepsLast()
    {
        $this->businessBundle
            ->addBusinessModel(new BusinessModel('TestData1', 'description1'))
            ->addBusinessModel(new BusinessModel('TestData2', 'description2'))
            ->addBusinessModel(new BusinessModel('TestData3', 'description3'))
            ->addBusinessModel(new BusinessModel('TestData4', 'description4'));

        $this->businessBundle->addBusinessModel(new BusinessModel('TestData1', 'description5'));

        // Make sure TestData1 has been replaced
        self::assertCount(4, $this->businessBundle->getBusinessModels());

        $businessModel = $this->businessBundle->getBusinessModel('TestData1');
        self::assertNotNull($businessModel, 'TestData1 not found');
        self::assertEquals('TestData1', $businessModel->getName());
        self::assertEquals('description5', $businessModel->getDescription());
    }

    public function testAddBusinessProcess()
    {
        $this->businessBundle
            ->addBusinessProcess(new BusinessProcess('TestData1', 'description1', new Event('TestEvent1')))
            ->addBusinessProcess(new BusinessProcess('TestData2', 'description2', new Event('TestEvent2')))
            ->addBusinessProcess(new BusinessProcess('TestData3', 'description3', new Event('TestEvent3')))
            ->addBusinessProcess(new BusinessProcess('TestData4', 'description4', new Event('TestEvent4')));

        // Make sure all data is present and unaltered
        self::assertCount(4, $this->businessBundle->getBusinessProcesses());

        $businessProcess = $this->businessBundle->getBusinessProcess('TestData1');
        self::assertNotNull($businessProcess, 'TestData1 not found');
        self::assertEquals('TestData1', $businessProcess->getName());
        self::assertEquals('description1', $businessProcess->getDescription());

        $businessProcess = $this->businessBundle->getBusinessProcess('TestData2');
        self::assertNotNull($businessProcess, 'TestData2 not found');
        self::assertEquals('TestData2', $businessProcess->getName());
        self::assertEquals('description2', $businessProcess->getDescription());

        $businessProcess = $this->businessBundle->getBusinessProcess('TestData3');
        self::assertNotNull($businessProcess, 'TestData3 not found');
        self::assertEquals('TestData3', $businessProcess->getName());
        self::assertEquals('description3', $businessProcess->getDescription());

        $businessProcess = $this->businessBundle->getBusinessProcess('TestData4');
        self::assertNotNull($businessProcess, 'TestData4 not found');
        self::assertEquals('TestData4', $businessProcess->getName());
        self::assertEquals('description4', $businessProcess->getDescription());

        $this->businessBundle->setBusinessProcesses([new BusinessProcess('TestData5', 'description5', new Event('TestEvent5'))]);
        $businessProcess = $this->businessBundle->getBusinessProcess('TestData5');
        self::assertNotNull($businessProcess, 'TestData5 not found');
        self::assertEquals('TestData5', $businessProcess->getName());
        self::assertEquals('description5', $businessProcess->getDescription());

        self::assertCount(1, $this->businessBundle->getBusinessProcesses());
        self::assertNull($this->businessBundle->getBusinessProcess('TestData1'));
        self::assertNull($this->businessBundle->getBusinessProcess('TestData2'));
        self::assertNull($this->businessBundle->getBusinessProcess('TestData3'));
        self::assertNull($this->businessBundle->getBusinessProcess('TestData4'));
    }

    public function testAddDuplicateBusinessProcessKeepsLast()
    {
        $this->businessBundle
            ->addBusinessProcess(new BusinessProcess('TestData1', 'description1', new Event('TestEvent1')))
            ->addBusinessProcess(new BusinessProcess('TestData2', 'description2', new Event('TestEvent2')))
            ->addBusinessProcess(new BusinessProcess('TestData3', 'description3', new Event('TestEvent3')))
            ->addBusinessProcess(new BusinessProcess('TestData4', 'description4', new Event('TestEvent4')));

        $this->businessBundle->addBusinessProcess(new BusinessProcess('TestData1', 'description5', new Event('TestEvent5')));

        // Make sure TestData1 has been replaced
        self::assertCount(4, $this->businessBundle->getBusinessProcesses());

        $businessProcess = $this->businessBundle->getBusinessProcess('TestData1');
        self::assertNotNull($businessProcess, 'TestData1 not found');
        self::assertEquals('TestData1', $businessProcess->getName());
        self::assertEquals('description5', $businessProcess->getDescription());
    }

    public function testBusinessCategoriesShouldWork()
    {
        self::assertEmpty($this->businessBundle->getBusinessProcessCategories());
        self::assertEmpty($this->businessBundle->getBusinessProcessesForCategory('Test Category 1'));
        self::assertEmpty($this->businessBundle->getBusinessProcessesForCategory('Test Category 2'));

        $bp1 = new BusinessProcess('TestData1', 'description1', new Event('TestEvent1'));
        $bp1->setCategory('Test Category 1');

        $bp2 = new BusinessProcess('TestData2', 'description2', new Event('TestEvent2'));
        $bp2->setCategory('Test Category 1');

        $bp3 = new BusinessProcess('TestData3', 'description3', new Event('TestEvent3'));
        $bp3->setCategory('Test Category 1');

        $bp4 = new BusinessProcess('TestData4', 'description4', new Event('TestEvent4'));
        $bp4->setCategory('Test Category 2');

        $this->businessBundle->setBusinessProcesses([$bp1, $bp2, $bp3, $bp4]);

        self::assertCount(2, $this->businessBundle->getBusinessProcessCategories());
        $cat1 = $this->businessBundle->getBusinessProcessesForCategory('Test Category 1');
        self::assertCount(3, $cat1);
        self::assertContains($bp1, $cat1);
        self::assertContains($bp2, $cat1);
        self::assertContains($bp3, $cat1);
        self::assertNotContains($bp4, $cat1);

        $cat2 = $this->businessBundle->getBusinessProcessesForCategory('Test Category 2');
        self::assertCount(1, $cat2);
        self::assertNotContains($bp1, $cat2);
        self::assertNotContains($bp2, $cat2);
        self::assertNotContains($bp3, $cat2);
        self::assertContains($bp4, $cat2);
    }

    public function testSetEntities()
    {
        $this->businessBundle
            ->addBusinessModel(new BusinessModel('TestData1', 'description1'))
            ->addBusinessModel(new BusinessModel('TestData2', 'description2'))
            ->addBusinessModel(new BusinessModel('TestData3', 'description3'))
            ->addBusinessModel(new BusinessModel('TestData4', 'description4'));

        $entities = [
            new BusinessModel('TestData5', 'description5'),
            new BusinessModel('TestData6', 'description6'),
        ];

        $this->businessBundle->setBusinessModels($entities);
        self::assertCount(2, $this->businessBundle->getBusinessModels());

        $businessModel = $this->businessBundle->getBusinessModel('TestData5');
        self::assertNotNull($businessModel, 'TestData5 not found');
        self::assertEquals('TestData5', $businessModel->getName());
        self::assertEquals('description5', $businessModel->getDescription());

        $businessModel = $this->businessBundle->getBusinessModel('TestData6');
        self::assertNotNull($businessModel, 'TestData6 not found');
        self::assertEquals('TestData6', $businessModel->getName());
        self::assertEquals('description6', $businessModel->getDescription());

        self::assertNull($this->businessBundle->getBusinessModel('TestData1'));
        self::assertNull($this->businessBundle->getBusinessModel('TestData2'));
        self::assertNull($this->businessBundle->getBusinessModel('TestData3'));
        self::assertNull($this->businessBundle->getBusinessModel('TestData4'));
    }

    public function testGetEventsIncludeBusinessProcessEvents()
    {
        $businessBundle = TestHelper::getSampleBusinessBundle();
        self::assertCount(4, $businessBundle->getEvents());
    }

    public function testAddEvent()
    {
        $this->businessBundle
            ->addEvent(new Event('TestEvent1', 'description1'))
            ->addEvent(new Event('TestEvent2', 'description2'))
            ->addEvent(new Event('TestEvent3', 'description3'));

        self::assertCount(3, $this->businessBundle->getEvents());

        $event = $this->businessBundle->getEvent('TestEvent1');
        self::assertNotNull($event, 'TestEvent1 not found');
        self::assertEquals('TestEvent1', $event->getName());
        self::assertEquals('description1', $event->getDescription());

        $event = $this->businessBundle->getEvent('TestEvent2');
        self::assertNotNull($event, 'TestEvent2 not found');
        self::assertEquals('TestEvent2', $event->getName());
        self::assertEquals('description2', $event->getDescription());

        $event = $this->businessBundle->getEvent('TestEvent3');
        self::assertNotNull($event, 'TestEvent3 not found');
        self::assertEquals('TestEvent3', $event->getName());
        self::assertEquals('description3', $event->getDescription());

        self::assertNull($this->businessBundle->getEvent('TestEvent4'));
    }

    public function testAddSameEventNameOverridesFirstOne()
    {
        $this->businessBundle
            ->addEvent(new Event('TestEvent1', 'description1'))
            ->addEvent(new Event('TestEvent2', 'description2'));

        self::assertCount(2, $this->businessBundle->getEvents());

        $event = $this->businessBundle->getEvent('TestEvent1');
        self::assertNotNull($event, 'TestEvent1 not found');
        self::assertEquals('TestEvent1', $event->getName());
        self::assertEquals('description1', $event->getDescription());

        $event = $this->businessBundle->getEvent('TestEvent2');
        self::assertNotNull($event, 'TestEvent2 not found');
        self::assertEquals('TestEvent2', $event->getName());
        self::assertEquals('description2', $event->getDescription());

        $this->businessBundle
            ->addEvent(new Event('TestEvent1', 'description3'));

        self::assertCount(2, $this->businessBundle->getEvents());

        $event = $this->businessBundle->getEvent('TestEvent1');
        self::assertNotNull($event, 'TestEvent1 not found');
        self::assertEquals('TestEvent1', $event->getName());
        self::assertEquals('description3', $event->getDescription());

        $event = $this->businessBundle->getEvent('TestEvent2');
        self::assertNotNull($event, 'TestEvent2 not found');
        self::assertEquals('TestEvent2', $event->getName());
        self::assertEquals('description2', $event->getDescription());
    }

    public function testAddSet()
    {
        $this->businessBundle
            ->addDataSet(new DataSet('TestSet1', 'description1'))
            ->addDataSet(new DataSet('TestSet2', 'description2'))
            ->addDataSet(new DataSet('TestSet3', 'description3'));

        self::assertCount(3, $this->businessBundle->getDataSets());

        $set = $this->businessBundle->getDataSet('TestSet1');
        self::assertNotNull($set, 'TestSet1 not found');
        self::assertEquals('TestSet1', $set->getName());
        self::assertEquals('description1', $set->getDescription());

        $set = $this->businessBundle->getDataSet('TestSet2');
        self::assertNotNull($set, 'TestSet2 not found');
        self::assertEquals('TestSet2', $set->getName());
        self::assertEquals('description2', $set->getDescription());

        $set = $this->businessBundle->getDataSet('TestSet3');
        self::assertNotNull($set, 'TestSet3 not found');
        self::assertEquals('TestSet3', $set->getName());
        self::assertEquals('description3', $set->getDescription());

        self::assertNull($this->businessBundle->getDataSet('TestSet4'));
    }

    public function testSetSets()
    {
        $this->businessBundle
            ->addDataSet(new DataSet('TestSet1', 'description1'))
            ->addDataSet(new DataSet('TestSet2', 'description2'))
            ->addDataSet(new DataSet('TestSet3', 'description3'));

        $sets = [
            new DataSet('TestSet4', 'description4'),
            new DataSet('TestSet5', 'description5'),
        ];

        $this->businessBundle->setDataSets($sets);

        self::assertCount(2, $this->businessBundle->getDataSets());

        $set = $this->businessBundle->getDataSet('TestSet4');
        self::assertNotNull($set, 'TestSet4 not found');
        self::assertEquals('TestSet4', $set->getName());
        self::assertEquals('description4', $set->getDescription());

        $set = $this->businessBundle->getDataSet('TestSet5');
        self::assertNotNull($set, 'TestSet5 not found');
        self::assertEquals('TestSet5', $set->getName());
        self::assertEquals('description5', $set->getDescription());

        self::assertNull($this->businessBundle->getDataSet('TestSet1'));
        self::assertNull($this->businessBundle->getDataSet('TestSet2'));
        self::assertNull($this->businessBundle->getDataSet('TestSet3'));
    }
}
