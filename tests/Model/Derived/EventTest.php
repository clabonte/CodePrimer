<?php

namespace CodePrimer\Tests\Model\Derived;

use CodePrimer\Model\Data\EventDataBundle;
use CodePrimer\Model\Derived\Event;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class EventTest extends TestCase
{
    /** @var Event */
    private $event;

    public function setUp(): void
    {
        parent::setUp();
        $this->event = new Event('TestEvent', 'Test Description');
    }

    public function testDefaultValues()
    {
        self::assertEquals('TestEvent', $this->event->getName());
        self::assertEquals('Test Description', $this->event->getDescription());
        self::assertEmpty($this->event->getDataBundles());
    }

    public function testAddDefaultDataBundleShouldWork()
    {
        $bundle = new EventDataBundle();

        $this->event->addDataBundle($bundle);
        self::assertCount(1, $this->event->getDataBundles());
        self::assertNotNull($this->event->getDataBundle());
    }

    public function testAddNamedDataBundleShouldWork()
    {
        $bundle1 = new EventDataBundle('bundle 1', 'description 1');
        $bundle2 = new EventDataBundle('bundle 2', 'description 2');

        $this->event->addDataBundle($bundle1);
        self::assertCount(1, $this->event->getDataBundles());
        self::assertNull($this->event->getDataBundle());
        self::assertEquals($bundle1, $this->event->getDataBundle('bundle 1'));
        self::assertNull($this->event->getDataBundle('bundle 2'));

        $this->event->addDataBundle($bundle2);
        self::assertCount(2, $this->event->getDataBundles());
        self::assertNull($this->event->getDataBundle());
        self::assertEquals($bundle1, $this->event->getDataBundle('bundle 1'));
        self::assertEquals($bundle2, $this->event->getDataBundle('bundle 2'));
    }

    public function testAddingDefaultDataBundleTwiceThrowsException()
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('DataBundle already present: '.Event::DEFAULT_BUNDLE.', please use a unique name for your bundle');

        $bundle = new EventDataBundle();
        $bundle2 = new EventDataBundle();

        $this->event->addDataBundle($bundle);
        $this->event->addDataBundle($bundle2);
    }

    public function testAddingDuplicateDataBundleThrowsException()
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('DataBundle already present: bundle 1, please use a unique name for your bundle');

        $bundle1 = new EventDataBundle('bundle 1');
        $bundle2 = new EventDataBundle('bundle 1');

        $this->event->addDataBundle($bundle1);
        $this->event->addDataBundle($bundle2);
    }
}
