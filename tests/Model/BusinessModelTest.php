<?php

namespace CodePrimer\Tests\Model;

use CodePrimer\Helper\FieldType;
use CodePrimer\Model\BusinessModel;
use CodePrimer\Model\Constraint;
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

    public function testManageIdentifiers()
    {
        self::assertNull($this->businessModel->getIdentifier());

        // BusinessModel should accept ID type fields as identifier
        $idField = new Field('idFieldId', FieldType::ID);
        $idField->setIdentifier(true);
        self::assertFalse($idField->isMandatory());
        self::assertFalse($idField->isManaged());

        $this->businessModel->addField($idField);
        $identifierId = $this->businessModel->getIdentifier();
        self::assertNotNull($identifierId);
        self::assertTrue($identifierId->isManaged(), 'BusinessModel should force isManaged to true for its ids');
        self::assertTrue($identifierId->isMandatory(), 'BusinessModel should force isMandatory to true for its ids');

        // BusinessModel should accept UID type fields as identifier
        $idField = new Field('IdFieldUID', FieldType::UUID);
        $idField->setIdentifier(true);
        self::assertFalse($idField->isMandatory());
        self::assertFalse($idField->isManaged());

        $this->businessModel->addField($idField);
        $identifierUuid = $this->businessModel->getIdentifier();
        self::assertNotNull($identifierUuid);
        self::assertTrue($identifierUuid->isManaged(), 'BusinessModel should force isManaged to true for its ids');
        self::assertTrue($identifierUuid->isMandatory(), 'BusinessModel should force isMandatory to true for its ids');
    }

    /**
     * @dataProvider provideWrongTypeForIdentifiers
     * @param string $type
     */
    public function testIdentifierShouldBeIDorUUIDFieldType(string $type)
    {
        self::assertNull($this->businessModel->getIdentifier());

        $idField = new Field('idFieldId', $type);
        $idField->setIdentifier(true);

        $this->expectExceptionMessage('Invalid identifier type provided: ' . $type);
        $this->expectException(\InvalidArgumentException::class);

        $this->businessModel->addField($idField);
    }

    /** @return string[] */
    public function provideWrongTypeForIdentifiers(): array
    {
        return [
            'STRING' => [FieldType::STRING],
            'TEXT' => [FieldType::TEXT],
            'EMAIL' => [FieldType::EMAIL],
            'URL' => [FieldType::URL],
            'PASSWORD' => [FieldType::PASSWORD],
            'PHONE' => [FieldType::PHONE],
            'DATE' => [FieldType::DATE],
            'TIME' => [FieldType::TIME],
            'DATETIME' => [FieldType::DATETIME],
            'BOOL' => [FieldType::BOOL],
            'BOOLEAN' => [FieldType::BOOLEAN],
            'INT' => [FieldType::INT],
            'INTEGER' => [FieldType::INTEGER],
            'LONG' => [FieldType::LONG],
            'FLOAT' => [FieldType::FLOAT],
            'DOUBLE' => [FieldType::DOUBLE],
            'DECIMAL' => [FieldType::DECIMAL],
            'PRICE' => [FieldType::PRICE],
            'RANDOM_STRING' => [FieldType::RANDOM_STRING],
        ];
    }

    public function testShouldOverrideOldIdentifierWithNewOne()
    {
        self::assertNull($this->businessModel->getIdentifier());

        $idField = new Field('myFirstId', FieldType::UUID);
        $idField->setIdentifier(true);
        $idField->setMandatory(false);
        $idField->setManaged(false);
        $idField->setGenerated(false);

        $this->businessModel->addField($idField);
        self::assertTrue($this->businessModel->getField('myFirstId')->isIdentifier());
        self::assertTrue($this->businessModel->getField('myFirstId')->isManaged(),
            'businessModel should have force is managed property to true');
        self::assertTrue($this->businessModel->getField('myFirstId')->isMandatory(),
            'businessModel should have force is mandatory property to true');

        $idField2 = new Field('mySecondId', FieldType::UUID);
        $idField2->setIdentifier(true);
        $this->businessModel->addField($idField2);
        self::assertTrue($this->businessModel->getField('mySecondId')->isIdentifier());

        // first id should be overridden now
        self::assertFalse($this->businessModel->getField('myFirstId')->isIdentifier(),
            'Overridden field should not be identifier anymore');
        self::assertFalse($this->businessModel->getField('myFirstId')->isMandatory(),
            'Overridden field should have its mandatory property reset');
        self::assertFalse($this->businessModel->getField('myFirstId')->isManaged(),
            'Overridden field should have its managed property reset');
        self::assertFalse($this->businessModel->getField('myFirstId')->isGenerated(),
            'Overridden field should have its generated property reset');
    }
}
