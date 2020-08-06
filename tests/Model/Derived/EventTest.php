<?php

namespace CodePrimer\Tests\Model;

use CodePrimer\Model\Derived\Event;
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
        self::assertEmpty($this->event->getBusinessModels());
        self::assertEmpty($this->event->getMandatoryFields());
        self::assertEmpty($this->event->getOptionalFields());

        self::assertFalse($this->event->isBusinessModelPresent('User'));
        self::assertEmpty($this->event->getBusinessModelMandatoryFields('User'));
        self::assertEmpty($this->event->getBusinessModelOptionalFields('User'));
    }
}
