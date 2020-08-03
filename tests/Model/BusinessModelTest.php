<?php

namespace CodePrimer\Tests\Model;

use CodePrimer\Helper\FieldType;
use CodePrimer\Model\Constraint;
use CodePrimer\Model\BusinessModel;
use CodePrimer\Model\Field;
use CodePrimer\Model\StateMachine;
use PHPUnit\Framework\TestCase;

class BusinessModelTest extends TestCase
{
    /** @var BusinessModel */
    private $businessModel;

    public function setUp(): void
    {
        parent::setUp();
        $this->businessModel = new BusinessModel('Test Name', 'Test Description');
    }

    public function testBasicSetters()
    {
        self::assertEquals('Test Name', $this->businessModel->getName());
        self::assertEquals('Test Description', $this->businessModel->getDescription());

        $this->businessModel
            ->setName('NewName')
            ->setDescription('New Description')
            ->setStateMachine(new StateMachine('State Machine'));

        self::assertEquals('NewName', $this->businessModel->getName());
        self::assertEquals('New Description', $this->businessModel->getDescription());
        self::assertEquals('State Machine', $this->businessModel->getStateMachine()->getName());
    }

    public function testAddField()
    {
        $this->businessModel
            ->addField(new Field('TestField1', FieldType::STRING))
            ->addField(new Field('TestField2', FieldType::INT));

        self::assertCount(2, $this->businessModel->getFields());

        $field = $this->businessModel->getField('TestField1');
        self::assertNotNull($field);
        self::assertEquals('TestField1', $field->getName());
        self::assertEquals(FieldType::STRING, $field->getType());
        self::assertEmpty($field->getDescription());

        $field = $this->businessModel->getField('TestField2');
        self::assertNotNull($field);
        self::assertEquals('TestField2', $field->getName());
        self::assertEquals(FieldType::INT, $field->getType());

        $this->businessModel->addField(new Field('TestField1', FieldType::URL, 'Test description'));

        self::assertCount(2, $this->businessModel->getFields());

        $field = $this->businessModel->getField('TestField1');
        self::assertNotNull($field);
        self::assertEquals('TestField1', $field->getName());
        self::assertEquals(FieldType::URL, $field->getType());
        self::assertEquals('Test description', $field->getDescription());
    }

    public function testSetFields()
    {
        $this->businessModel
            ->addField(new Field('TestField1', FieldType::STRING))
            ->addField(new Field('TestField2', FieldType::INT))
            ->addField(new Field('TestField3', FieldType::INT));

        self::assertCount(3, $this->businessModel->getFields());

        $fields = [
            new Field('TestField4', FieldType::STRING),
            new Field('TestField5', FieldType::STRING),
        ];

        $this->businessModel->setFields($fields);

        self::assertCount(2, $this->businessModel->getFields());
        self::assertNotNull($this->businessModel->getField('TestField4'));
        self::assertNotNull($this->businessModel->getField('TestField5'));
        self::assertNull($this->businessModel->getField('TestField1'));
    }

    public function testAddUniqueConstraint()
    {
        $constraint = new Constraint('testConstraint 1');
        $constraint
            ->addField(new Field('TestField1', FieldType::STRING))
            ->addField(new Field('TestField2', FieldType::INT));

        $this->businessModel->addUniqueConstraint($constraint);

        self::assertCount(1, $this->businessModel->getUniqueConstraints());

        $constraint = new Constraint('testConstraint 2');
        $constraint
            ->addField(new Field('TestField2', FieldType::STRING))
            ->addField(new Field('TestField3', FieldType::INT));

        $this->businessModel->addUniqueConstraint($constraint);

        self::assertCount(2, $this->businessModel->getUniqueConstraints());
    }

    public function testSetUniqueConstraints()
    {
        $constraint = new Constraint('testConstraint 1');
        $constraint
            ->addField(new Field('TestField1', FieldType::STRING))
            ->addField(new Field('TestField2', FieldType::INT));

        $this->businessModel->addUniqueConstraint($constraint);

        $constraint = new Constraint('testConstraint 2');
        $constraint
            ->addField(new Field('TestField2', FieldType::STRING))
            ->addField(new Field('TestField3', FieldType::INT));

        $this->businessModel->addUniqueConstraint($constraint);

        self::assertCount(2, $this->businessModel->getUniqueConstraints());

        $constraints = [];
        $constraint = new Constraint('testConstraint 2');
        $constraint
            ->addField(new Field('TestField2', FieldType::STRING))
            ->addField(new Field('TestField3', FieldType::INT));

        $constraints[] = $constraint;

        $this->businessModel->setUniqueConstraints($constraints);
        self::assertCount(1, $this->businessModel->getUniqueConstraints());
    }

    public function testGetIdentifier()
    {
        self::assertNull($this->businessModel->getIdentifier());

        $this->businessModel
            ->addField(new Field('TestField1', FieldType::STRING))
            ->addField(new Field('TestField2', FieldType::INT));

        self::assertNull($this->businessModel->getIdentifier());

        $idField = new Field('NonMandatoryField', FieldType::ID);
        $idField->setManaged(true);
        $idField->setMandatory(false);

        $this->businessModel->addField($idField);
        self::assertNull($this->businessModel->getIdentifier());

        $idField = new Field('NonManagedField', FieldType::ID);
        $idField->setManaged(false);
        $idField->setMandatory(true);

        $this->businessModel->addField($idField);
        self::assertNull($this->businessModel->getIdentifier());

        $idField = new Field('IdField', FieldType::ID);
        $idField->setManaged(true);
        $idField->setMandatory(true);

        $this->businessModel->addField($idField);
        $field = $this->businessModel->getIdentifier();
        self::assertNotNull($field);
        self::assertEquals('IdField', $field->getName());
    }
}
