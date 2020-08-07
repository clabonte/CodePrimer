<?php

namespace CodePrimer\Tests\Model;

use CodePrimer\Model\BusinessProcess;
use CodePrimer\Model\Derived\Event;
use PHPUnit\Framework\TestCase;

class BusinessProcessTest extends TestCase
{
    /** @var BusinessProcess */
    private $businessProcess;

    public function setUp(): void
    {
        parent::setUp();
        $this->businessProcess = new BusinessProcess('Test Name', 'Test Description', new Event('Test Event', 'code'));
    }

    public function testDefaultValues()
    {
        self::assertEquals('Test Name', $this->businessProcess->getName());
        self::assertEquals('Test Description', $this->businessProcess->getDescription());
        self::assertTrue($this->businessProcess->isSynchronous());
        self::assertFalse($this->businessProcess->isAsynchronous());
        self::assertFalse($this->businessProcess->isPeriodic());
        self::assertFalse($this->businessProcess->isExternalAccess());
        self::assertFalse($this->businessProcess->isRestricted());
        self::assertFalse($this->businessProcess->isProducingExternalUpdates());
        self::assertFalse($this->businessProcess->isProducingInternalUpdates());
        self::assertFalse($this->businessProcess->isProducingMessages());

        self::assertNotNull($this->businessProcess->getTrigger());

        self::assertNotNull($this->businessProcess->getPeriodicTriggers());
        self::assertEmpty($this->businessProcess->getPeriodicTriggers());

        self::assertNotNull($this->businessProcess->getRoles());
        self::assertEmpty($this->businessProcess->getRoles());

        self::assertNotNull($this->businessProcess->getExternalUpdates());
        self::assertEmpty($this->businessProcess->getExternalUpdates());

        self::assertNotNull($this->businessProcess->getInternalUpdates());
        self::assertEmpty($this->businessProcess->getInternalUpdates());

        self::assertNotNull($this->businessProcess->getMessages());
        self::assertEmpty($this->businessProcess->getMessages());
    }

    public function testRoleMethods()
    {
        // By default, there is no access restriction placed on a given process
        self::assertFalse($this->businessProcess->isRestricted());
        self::assertNotNull($this->businessProcess->getRoles());
        self::assertEmpty($this->businessProcess->getRoles());
        self::assertTrue($this->businessProcess->isAllowed('admin'));
        self::assertTrue($this->businessProcess->isAllowed('user'));
        self::assertTrue($this->businessProcess->isAllowed('anonymous'));

        // Adding a 1st role restrict access to this business process for this role only
        $this->businessProcess->addRole('admin');
        self::assertTrue($this->businessProcess->isRestricted());
        self::assertNotEmpty($this->businessProcess->getRoles());
        self::assertCount(1, $this->businessProcess->getRoles());
        self::assertTrue($this->businessProcess->containsRole('admin'));
        self::assertFalse($this->businessProcess->containsRole('user'));
        self::assertFalse($this->businessProcess->containsRole('anonymous'));

        // Adding the same role twice does not change anything
        $this->businessProcess->addRole('admin');
        self::assertTrue($this->businessProcess->isRestricted());
        self::assertNotEmpty($this->businessProcess->getRoles());
        self::assertCount(1, $this->businessProcess->getRoles());
        self::assertTrue($this->businessProcess->containsRole('admin'));
        self::assertFalse($this->businessProcess->containsRole('user'));
        self::assertFalse($this->businessProcess->containsRole('anonymous'));

        // Adding a 2nd role is working as expected
        $this->businessProcess->addRole('user');
        self::assertTrue($this->businessProcess->isRestricted());
        self::assertNotEmpty($this->businessProcess->getRoles());
        self::assertCount(2, $this->businessProcess->getRoles());
        self::assertTrue($this->businessProcess->containsRole('admin'));
        self::assertTrue($this->businessProcess->containsRole('user'));
        self::assertFalse($this->businessProcess->containsRole('anonymous'));
    }
}
