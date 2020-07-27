<?php

namespace CodePrimer\Tests\Model;

use CodePrimer\Helper\FieldType;
use CodePrimer\Model\Constraint;
use CodePrimer\Model\Entity;
use CodePrimer\Model\Field;
use CodePrimer\Model\StateMachine;
use PHPUnit\Framework\TestCase;

class EntityTest extends TestCase
{
    /** @var Entity */
    private $entity;

    public function setUp(): void
    {
        parent::setUp();
        $this->entity = new Entity('Test Name', 'Test Description');
    }

    public function testBasicSetters()
    {
        self::assertEquals('Test Name', $this->entity->getName());
        self::assertEquals('Test Description', $this->entity->getDescription());

        $this->entity
            ->setName('NewName')
            ->setDescription('New Description')
            ->setStateMachine(new StateMachine('State Machine'));

        self::assertEquals('NewName', $this->entity->getName());
        self::assertEquals('New Description', $this->entity->getDescription());
        self::assertEquals('State Machine', $this->entity->getStateMachine()->getName());
    }

    public function testAddField()
    {
        $this->entity
            ->addField(new Field('TestField1', FieldType::STRING))
            ->addField(new Field('TestField2', FieldType::INT));

        self::assertCount(2, $this->entity->getFields());

        $field = $this->entity->getField('TestField1');
        self::assertNotNull($field);
        self::assertEquals('TestField1', $field->getName());
        self::assertEquals(FieldType::STRING, $field->getType());
        self::assertEmpty($field->getDescription());

        $field = $this->entity->getField('TestField2');
        self::assertNotNull($field);
        self::assertEquals('TestField2', $field->getName());
        self::assertEquals(FieldType::INT, $field->getType());

        $this->entity->addField(new Field('TestField1', FieldType::URL, 'Test description'));

        self::assertCount(2, $this->entity->getFields());

        $field = $this->entity->getField('TestField1');
        self::assertNotNull($field);
        self::assertEquals('TestField1', $field->getName());
        self::assertEquals(FieldType::URL, $field->getType());
        self::assertEquals('Test description', $field->getDescription());
    }

    public function testSetFields()
    {
        $this->entity
            ->addField(new Field('TestField1', FieldType::STRING))
            ->addField(new Field('TestField2', FieldType::INT))
            ->addField(new Field('TestField3', FieldType::INT));

        self::assertCount(3, $this->entity->getFields());

        $fields = [
            new Field('TestField4', FieldType::STRING),
            new Field('TestField5', FieldType::STRING),
        ];

        $this->entity->setFields($fields);

        self::assertCount(2, $this->entity->getFields());
        self::assertNotNull($this->entity->getField('TestField4'));
        self::assertNotNull($this->entity->getField('TestField5'));
        self::assertNull($this->entity->getField('TestField1'));
    }

    public function testAddUniqueConstraint()
    {
        $constraint = new Constraint('testConstraint 1');
        $constraint
            ->addField(new Field('TestField1', FieldType::STRING))
            ->addField(new Field('TestField2', FieldType::INT));

        $this->entity->addUniqueConstraint($constraint);

        self::assertCount(1, $this->entity->getUniqueConstraints());

        $constraint = new Constraint('testConstraint 2');
        $constraint
            ->addField(new Field('TestField2', FieldType::STRING))
            ->addField(new Field('TestField3', FieldType::INT));

        $this->entity->addUniqueConstraint($constraint);

        self::assertCount(2, $this->entity->getUniqueConstraints());
    }

    public function testSetUniqueConstraints()
    {
        $constraint = new Constraint('testConstraint 1');
        $constraint
            ->addField(new Field('TestField1', FieldType::STRING))
            ->addField(new Field('TestField2', FieldType::INT));

        $this->entity->addUniqueConstraint($constraint);

        $constraint = new Constraint('testConstraint 2');
        $constraint
            ->addField(new Field('TestField2', FieldType::STRING))
            ->addField(new Field('TestField3', FieldType::INT));

        $this->entity->addUniqueConstraint($constraint);

        self::assertCount(2, $this->entity->getUniqueConstraints());

        $constraints = [];
        $constraint = new Constraint('testConstraint 2');
        $constraint
            ->addField(new Field('TestField2', FieldType::STRING))
            ->addField(new Field('TestField3', FieldType::INT));

        $constraints[] = $constraint;

        $this->entity->setUniqueConstraints($constraints);
        self::assertCount(1, $this->entity->getUniqueConstraints());
    }

    public function testGetIdentifier()
    {
        self::assertNull($this->entity->getIdentifier());

        $this->entity
            ->addField(new Field('TestField1', FieldType::STRING))
            ->addField(new Field('TestField2', FieldType::INT));

        self::assertNull($this->entity->getIdentifier());

        $idField = new Field('NonMandatoryField', FieldType::ID);
        $idField->setManaged(true);
        $idField->setMandatory(false);

        $this->entity->addField($idField);
        self::assertNull($this->entity->getIdentifier());

        $idField = new Field('NonManagedField', FieldType::ID);
        $idField->setManaged(false);
        $idField->setMandatory(true);

        $this->entity->addField($idField);
        self::assertNull($this->entity->getIdentifier());

        $idField = new Field('IdField', FieldType::ID);
        $idField->setManaged(true);
        $idField->setMandatory(true);

        $this->entity->addField($idField);
        $field = $this->entity->getIdentifier();
        self::assertNotNull($field);
        self::assertEquals('IdField', $field->getName());
    }
}
