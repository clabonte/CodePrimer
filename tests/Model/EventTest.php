<?php

namespace CodePrimer\Tests\Model;

use CodePrimer\Helper\FieldType;
use CodePrimer\Model\BusinessModel;
use CodePrimer\Model\Derived\Event;
use CodePrimer\Model\Field;
use PHPUnit\Framework\TestCase;

class EventTest extends TestCase
{
    /** @var Event */
    private $event;

    public function setUp(): void
    {
        parent::setUp();
        $this->event = new Event('TestEvent', 'test.event', 'Test Description');
    }

    public function testBasicSetters()
    {
        self::assertEquals('TestEvent', $this->event->getName());
        self::assertEquals('test.event', $this->event->getCode());
        self::assertEquals('Test Description', $this->event->getDescription());
        self::assertNull($this->event->getBusinessModel());

        $this->event
            ->setName('NewName')
            ->setCode('new.code')
            ->setDescription('New Description')
            ->setBusinessModel(new BusinessModel('Test Entity'));

        self::assertEquals('NewName', $this->event->getName());
        self::assertEquals('new.code', $this->event->getCode());
        self::assertEquals('New Description', $this->event->getDescription());

        $businessModel = $this->event->getBusinessModel();
        self::assertNotNull($businessModel);
        self::assertEquals('Test Entity', $businessModel->getName());
    }

    public function testSetFields()
    {
        $this->event
            ->addField(new Field('TestField1', FieldType::STRING))
            ->addField(new Field('TestField2', FieldType::INT))
            ->addField(new Field('TestField3', FieldType::INT));

        self::assertCount(3, $this->event->getFields());

        $fields = [
            new Field('TestField4', FieldType::STRING),
            new Field('TestField5', FieldType::STRING),
        ];

        $this->event->setFields($fields);

        self::assertCount(2, $this->event->getFields());
        self::assertNotNull($this->event->getField('TestField4'));
        self::assertNotNull($this->event->getField('TestField5'));
        self::assertNull($this->event->getField('TestField1'));
    }

    public function testAddField()
    {
        $this->event
            ->addField(new Field('TestField1', FieldType::STRING))
            ->addField(new Field('TestField2', FieldType::INT));

        self::assertCount(2, $this->event->getFields());

        $field = $this->event->getField('TestField1');
        self::assertNotNull($field);
        self::assertEquals('TestField1', $field->getName());
        self::assertEquals(FieldType::STRING, $field->getType());
        self::assertEmpty($field->getDescription());

        $field = $this->event->getField('TestField2');
        self::assertNotNull($field);
        self::assertEquals('TestField2', $field->getName());
        self::assertEquals(FieldType::INT, $field->getType());

        $this->event->addField(new Field('TestField1', FieldType::URL, 'Test description'));

        self::assertCount(2, $this->event->getFields());

        $field = $this->event->getField('TestField1');
        self::assertNotNull($field);
        self::assertEquals('TestField1', $field->getName());
        self::assertEquals(FieldType::URL, $field->getType());
        self::assertEquals('Test description', $field->getDescription());
    }
}
