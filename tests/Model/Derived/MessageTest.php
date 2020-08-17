<?php

namespace CodePrimer\Tests\Model\Derived;

use CodePrimer\Model\Data\MessageDataBundle;
use CodePrimer\Model\Derived\Event;
use CodePrimer\Model\Derived\Message;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class MessageTest extends TestCase
{
    /** @var Message */
    private $message;

    public function setUp(): void
    {
        parent::setUp();
        $this->message = new Message('test.message');
    }

    public function testDefaultValues()
    {
        self::assertEquals('test.message', $this->message->getId());
        self::assertEquals('test.message', $this->message->getName());
        self::assertEquals('', $this->message->getDescription());
        self::assertEmpty($this->message->getDataBundles());
    }

    public function testDefaultValuesForNamedMessage()
    {
        $message = new Message('test.message', 'Test Message', 'Test Description');
        self::assertEquals('test.message', $message->getId());
        self::assertEquals('Test Message', $message->getName());
        self::assertEquals('Test Description', $message->getDescription());
        self::assertEmpty($this->message->getDataBundles());
    }

    public function testAddDefaultDataBundleShouldWork()
    {
        $bundle = new MessageDataBundle();

        $this->message->addDataBundle($bundle);
        self::assertCount(1, $this->message->getDataBundles());
        self::assertNotNull($this->message->getDataBundle());
    }

    public function testAddNamedDataBundleShouldWork()
    {
        $bundle1 = new MessageDataBundle('bundle 1', 'description 1');
        $bundle2 = new MessageDataBundle('bundle 2', 'description 2');

        $this->message->addDataBundle($bundle1);
        self::assertCount(1, $this->message->getDataBundles());
        self::assertNull($this->message->getDataBundle());
        self::assertEquals($bundle1, $this->message->getDataBundle('bundle 1'));
        self::assertNull($this->message->getDataBundle('bundle 2'));

        $this->message->addDataBundle($bundle2);
        self::assertCount(2, $this->message->getDataBundles());
        self::assertNull($this->message->getDataBundle());
        self::assertEquals($bundle1, $this->message->getDataBundle('bundle 1'));
        self::assertEquals($bundle2, $this->message->getDataBundle('bundle 2'));
    }

    public function testAddingDefaultDataBundleTwiceThrowsException()
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('DataBundle already present: '.Event::DEFAULT_BUNDLE.', please use a unique name for your bundle');

        $bundle = new MessageDataBundle();
        $bundle2 = new MessageDataBundle();

        $this->message->addDataBundle($bundle);
        $this->message->addDataBundle($bundle2);
    }

    public function testAddingDuplicateDataBundleThrowsException()
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('DataBundle already present: bundle 1, please use a unique name for your bundle');

        $bundle1 = new MessageDataBundle('bundle 1');
        $bundle2 = new MessageDataBundle('bundle 1');

        $this->message->addDataBundle($bundle1);
        $this->message->addDataBundle($bundle2);
    }
}
