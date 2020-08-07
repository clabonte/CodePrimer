<?php

namespace CodePrimer\Tests\Model\Derived;

use CodePrimer\Model\Derived\Message;
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
}
