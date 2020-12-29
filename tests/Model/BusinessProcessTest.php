<?php

namespace CodePrimer\Tests\Model;

use CodePrimer\Helper\ProcessType;
use CodePrimer\Model\BusinessProcess;
use CodePrimer\Model\Data\ContextDataBundle;
use CodePrimer\Model\Data\ExternalDataBundle;
use CodePrimer\Model\Data\InternalDataBundle;
use CodePrimer\Model\Data\ReturnedDataBundle;
use CodePrimer\Model\Derived\Event;
use CodePrimer\Model\Derived\Message;
use InvalidArgumentException;
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
        self::assertEquals(ProcessType::CUSTOM, $this->businessProcess->getType());
        self::assertEmpty($this->businessProcess->getCategory());
        self::assertTrue($this->businessProcess->isSynchronous());
        self::assertFalse($this->businessProcess->isAsynchronous());
        self::assertFalse($this->businessProcess->isPeriodic());
        self::assertFalse($this->businessProcess->isExternalAccess());
        self::assertFalse($this->businessProcess->isRestricted());

        $this->assertNoRequiredData($this->businessProcess);
        $this->assertNoProducedData($this->businessProcess);

        self::assertFalse($this->businessProcess->isDataReturned());
        self::assertEmpty($this->businessProcess->getReturnedData());

        self::assertFalse($this->businessProcess->isMessageProduced());
        self::assertEmpty($this->businessProcess->getMessages());

        self::assertNotNull($this->businessProcess->getEvent());

        self::assertEmpty($this->businessProcess->getPeriodicTriggers());

        self::assertEmpty($this->businessProcess->getRoles());
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
        self::assertTrue($this->businessProcess->isAllowed('admin'));
        self::assertFalse($this->businessProcess->isAllowed('user'));
        self::assertFalse($this->businessProcess->isAllowed('anonymous'));

        // Adding the same role twice does not change anything
        $this->businessProcess->addRole('admin');
        self::assertTrue($this->businessProcess->isRestricted());
        self::assertNotEmpty($this->businessProcess->getRoles());
        self::assertCount(1, $this->businessProcess->getRoles());
        self::assertTrue($this->businessProcess->containsRole('admin'));
        self::assertFalse($this->businessProcess->containsRole('user'));
        self::assertFalse($this->businessProcess->containsRole('anonymous'));
        self::assertTrue($this->businessProcess->isAllowed('admin'));
        self::assertFalse($this->businessProcess->isAllowed('user'));
        self::assertFalse($this->businessProcess->isAllowed('anonymous'));

        // Adding a 2nd role is working as expected
        $this->businessProcess->addRole('user');
        self::assertTrue($this->businessProcess->isRestricted());
        self::assertNotEmpty($this->businessProcess->getRoles());
        self::assertCount(2, $this->businessProcess->getRoles());
        self::assertTrue($this->businessProcess->containsRole('admin'));
        self::assertTrue($this->businessProcess->containsRole('user'));
        self::assertFalse($this->businessProcess->containsRole('anonymous'));
        self::assertTrue($this->businessProcess->isAllowed('admin'));
        self::assertTrue($this->businessProcess->isAllowed('user'));
        self::assertFalse($this->businessProcess->isAllowed('anonymous'));
    }

    public function testAddRequiredContextDataShouldWork()
    {
        $this->assertNoRequiredData($this->businessProcess);

        $this->businessProcess->addRequiredData(new ContextDataBundle());
        self::assertTrue($this->businessProcess->isDataRequired());
        self::assertTrue($this->businessProcess->isContextDataRequired());
        self::assertFalse($this->businessProcess->isInternalDataRequired());
        self::assertFalse($this->businessProcess->isExternalDataRequired());

        self::assertCount(1, $this->businessProcess->getRequiredData());
        self::assertArrayHasKey(ContextDataBundle::class, $this->businessProcess->getRequiredData());
        self::assertCount(1, $this->businessProcess->getRequiredContextData());
        self::assertEmpty($this->businessProcess->getRequiredInternalData());
        self::assertEmpty($this->businessProcess->getRequiredExternalData());
    }

    public function testAddRequiredInternalDataShouldWork()
    {
        $this->assertNoRequiredData($this->businessProcess);

        $this->businessProcess->addRequiredData(new InternalDataBundle());
        self::assertTrue($this->businessProcess->isDataRequired());
        self::assertFalse($this->businessProcess->isContextDataRequired());
        self::assertTrue($this->businessProcess->isInternalDataRequired());
        self::assertFalse($this->businessProcess->isExternalDataRequired());

        self::assertCount(1, $this->businessProcess->getRequiredData());
        self::assertArrayHasKey(InternalDataBundle::class, $this->businessProcess->getRequiredData());
        self::assertEmpty($this->businessProcess->getRequiredContextData());
        self::assertCount(1, $this->businessProcess->getRequiredInternalData());
        self::assertEmpty($this->businessProcess->getRequiredExternalData());
    }

    public function testAddRequiredExternalDataShouldWork()
    {
        $this->assertNoRequiredData($this->businessProcess);

        $this->businessProcess->addRequiredData(new ExternalDataBundle());
        self::assertTrue($this->businessProcess->isDataRequired());
        self::assertFalse($this->businessProcess->isContextDataRequired());
        self::assertFalse($this->businessProcess->isInternalDataRequired());
        self::assertTrue($this->businessProcess->isExternalDataRequired());

        self::assertCount(1, $this->businessProcess->getRequiredData());
        self::assertArrayHasKey(ExternalDataBundle::class, $this->businessProcess->getRequiredData());
        self::assertEmpty($this->businessProcess->getRequiredContextData());
        self::assertEmpty($this->businessProcess->getRequiredInternalData());
        self::assertCount(1, $this->businessProcess->getRequiredExternalData());
    }

    public function testAddProducedContextDataShouldWork()
    {
        $this->assertNoProducedData($this->businessProcess);

        $this->businessProcess->addProducedData(new ContextDataBundle());
        self::assertTrue($this->businessProcess->isDataProduced());
        self::assertTrue($this->businessProcess->isContextDataProduced());
        self::assertFalse($this->businessProcess->isInternalDataProduced());
        self::assertFalse($this->businessProcess->isExternalDataProduced());

        self::assertCount(1, $this->businessProcess->getProducedData());
        self::assertArrayHasKey(ContextDataBundle::class, $this->businessProcess->getProducedData());
        self::assertCount(1, $this->businessProcess->getProducedContextData());
        self::assertEmpty($this->businessProcess->getProducedInternalData());
        self::assertEmpty($this->businessProcess->getProducedExternalData());
    }

    public function testAddProducedInternalDataShouldWork()
    {
        $this->assertNoProducedData($this->businessProcess);

        $this->businessProcess->addProducedData(new InternalDataBundle());
        self::assertTrue($this->businessProcess->isDataProduced());
        self::assertFalse($this->businessProcess->isContextDataProduced());
        self::assertTrue($this->businessProcess->isInternalDataProduced());
        self::assertFalse($this->businessProcess->isExternalDataProduced());

        self::assertCount(1, $this->businessProcess->getProducedData());
        self::assertArrayHasKey(InternalDataBundle::class, $this->businessProcess->getProducedData());
        self::assertEmpty($this->businessProcess->getProducedContextData());
        self::assertCount(1, $this->businessProcess->getProducedInternalData());
        self::assertEmpty($this->businessProcess->getProducedExternalData());
    }

    public function testAddProducedExternalDataShouldWork()
    {
        $this->assertNoProducedData($this->businessProcess);

        $this->businessProcess->addProducedData(new ExternalDataBundle());
        self::assertTrue($this->businessProcess->isDataProduced());
        self::assertFalse($this->businessProcess->isContextDataProduced());
        self::assertFalse($this->businessProcess->isInternalDataProduced());
        self::assertTrue($this->businessProcess->isExternalDataProduced());

        self::assertCount(1, $this->businessProcess->getProducedData());
        self::assertArrayHasKey(ExternalDataBundle::class, $this->businessProcess->getProducedData());
        self::assertEmpty($this->businessProcess->getProducedContextData());
        self::assertEmpty($this->businessProcess->getProducedInternalData());
        self::assertCount(1, $this->businessProcess->getProducedExternalData());
    }

    public function testAddMessageShouldWork()
    {
        self::assertFalse($this->businessProcess->isMessageProduced());
        self::assertEmpty($this->businessProcess->getMessages());

        $this->businessProcess->addMessage(new Message('message.id'));

        self::assertTrue($this->businessProcess->isMessageProduced());
        self::assertCount(1, $this->businessProcess->getMessages());
        self::assertArrayHasKey('message.id', $this->businessProcess->getMessages());
    }

    public function testSetReturnedDataShouldWork()
    {
        self::assertFalse($this->businessProcess->isDataReturned());
        self::assertEmpty($this->businessProcess->getReturnedData());

        $this->businessProcess->setReturnedData(new ReturnedDataBundle('Test'));

        self::assertTrue($this->businessProcess->isDataReturned());
        self::assertNotNull($this->businessProcess->getReturnedData());
    }

    public function testConstructorWithInvalidTypeThrowsException()
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Invalid type provided: unknown. Must be one of the ProcessType constants');

        new BusinessProcess('Test Name', 'Test Description', new Event('Test Event', 'code'), 'unknown');
    }

    public function testSetInvalidTypeThrowsException()
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Invalid type provided: unknown. Must be one of the ProcessType constants');

        self::assertEquals(ProcessType::CUSTOM, $this->businessProcess->getType());
        $this->businessProcess->setType(ProcessType::CREATE);
        self::assertEquals(ProcessType::CREATE, $this->businessProcess->getType());

        $this->businessProcess->setType('unknown');
    }

    private function assertNoRequiredData($businessProcess)
    {
        self::assertFalse($businessProcess->isDataRequired());
        self::assertFalse($businessProcess->isContextDataRequired());
        self::assertFalse($businessProcess->isInternalDataRequired());
        self::assertFalse($businessProcess->isExternalDataRequired());

        self::assertEmpty($businessProcess->getRequiredData());
        self::assertEmpty($businessProcess->getRequiredContextData());
        self::assertEmpty($businessProcess->getRequiredInternalData());
        self::assertEmpty($businessProcess->getRequiredExternalData());
    }

    private function assertNoProducedData($businessProcess)
    {
        self::assertFalse($businessProcess->isDataProduced());
        self::assertFalse($businessProcess->isContextDataProduced());
        self::assertFalse($businessProcess->isInternalDataProduced());
        self::assertFalse($businessProcess->isExternalDataProduced());

        self::assertEmpty($businessProcess->getProducedData());
        self::assertEmpty($businessProcess->getProducedContextData());
        self::assertEmpty($businessProcess->getProducedInternalData());
        self::assertEmpty($businessProcess->getProducedExternalData());
    }
}
