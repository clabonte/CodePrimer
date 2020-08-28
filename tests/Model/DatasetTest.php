<?php

namespace CodePrimer\Tests\Model;

use CodePrimer\Helper\FieldType;
use CodePrimer\Model\Dataset;
use CodePrimer\Model\DatasetElement;
use CodePrimer\Model\Field;
use CodePrimer\Tests\Helper\TestHelper;
use PHPUnit\Framework\TestCase;

class DatasetTest extends TestCase
{
    /** @var Dataset */
    private $dataset;

    public function setUp(): void
    {
        parent::setUp();
        $this->dataset = new Dataset('Test DataSet', 'Test Description');
    }

    /**
     * @dataProvider validFieldProvider
     */
    public function testAddValidFieldShouldWork(Field $field)
    {
        self::assertNull($this->dataset->getField($field->getName()));
        $this->dataset->addField($field);
        self::assertNotNull($this->dataset->getField($field->getName()));
    }

    public function validFieldProvider()
    {
        return [
            'UUID' => [new Field('Test', FieldType::UUID)],
            'STRING' => [new Field('Test', FieldType::STRING)],
            'TEXT' => [new Field('Test', FieldType::TEXT)],
            'EMAIL' => [new Field('Test', FieldType::EMAIL)],
            'URL' => [new Field('Test', FieldType::URL)],
            'PASSWORD' => [new Field('Test', FieldType::PASSWORD)],
            'PHONE' => [new Field('Test', FieldType::PHONE)],
            'DATE' => [new Field('Test', FieldType::DATE)],
            'TIME' => [new Field('Test', FieldType::TIME)],
            'DATETIME' => [new Field('Test', FieldType::DATETIME)],
            'BOOL' => [new Field('Test', FieldType::BOOL)],
            'BOOLEAN' => [new Field('Test', FieldType::BOOLEAN)],
            'INT' => [new Field('Test', FieldType::INT)],
            'INTEGER' => [new Field('Test', FieldType::INTEGER)],
            'ID' => [new Field('Test', FieldType::ID)],
            'LONG' => [new Field('Test', FieldType::LONG)],
            'FLOAT' => [new Field('Test', FieldType::FLOAT)],
            'DOUBLE' => [new Field('Test', FieldType::DOUBLE)],
            'DECIMAL' => [new Field('Test', FieldType::DECIMAL)],
            'PRICE' => [new Field('Test', FieldType::PRICE)],
            'RANDOM_STRING' => [new Field('Test', FieldType::RANDOM_STRING)],
        ];
    }

    /**
     * @dataProvider invalidFieldProvider
     */
    public function testAddInvalidFieldShouldThrowException(Field $field, string $expectedMessage)
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedMessage);
        $this->dataset->addField($field);
    }

    public function invalidFieldProvider()
    {
        return [
            'List field' => [
                (new Field('Test', FieldType::STRING))->setList(true),
                'Test has an unsupported field type: DataSet does not support list fields.',
            ],
            'Non native' => [
                new Field('Test', 'Model'),
                'Test has an unsupported field type: Model. DataSet only support native fields right now.',
            ],
        ];
    }

    public function testAddTwoIdentifiersShouldThrowException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('There is already an identifier field defined for DataSet Test DataSet: id. A DataSet cannot have more than 1 identifier field');

        self::assertNull($this->dataset->getIdentifier());
        $field = new Field('id', FieldType::STRING, 'ID');
        $field->setIdentifier(true);

        $this->dataset->addField($field);
        self::assertEquals($field, $this->dataset->getIdentifier());

        $field = new Field('otherId', FieldType::STRING, 'ID');
        $field->setIdentifier(true);
        $this->dataset->addField($field);
    }

    public function testSetFieldsShouldWork()
    {
        $name = 'Test';
        $field = new Field($name, FieldType::STRING, 'Description 1');

        self::assertEmpty($this->dataset->getFields());
        self::assertNull($this->dataset->getField($name));

        $this->dataset->setFields([$field]);
        self::assertCount(1, $this->dataset->getFields());
        $actual = $this->dataset->getField($name);
        self::assertNotNull($actual);
        self::assertEquals(FieldType::STRING, $actual->getType());
        self::assertEquals('Description 1', $actual->getDescription());

        // Adding 2 fields
        $field2 = new Field('Second', FieldType::PASSWORD, 'Test Description 2');

        $this->dataset->setFields([$field, $field2]);
        self::assertCount(2, $this->dataset->getFields());
        $actual = $this->dataset->getField($name);
        self::assertNotNull($actual);
        self::assertEquals(FieldType::STRING, $actual->getType());
        self::assertEquals('Description 1', $actual->getDescription());
        $actual = $this->dataset->getField('Second');
        self::assertNotNull($actual);
        self::assertEquals(FieldType::PASSWORD, $actual->getType());
        self::assertEquals('Test Description 2', $actual->getDescription());

        // Overriding field with same name works
        $field = new Field($name, FieldType::INTEGER, 'Description 2');

        $this->dataset->setFields([$field]);
        self::assertCount(1, $this->dataset->getFields());
        $actual = $this->dataset->getField($name);
        self::assertNotNull($actual);
        self::assertEquals(FieldType::INTEGER, $actual->getType());
        self::assertEquals('Description 2', $actual->getDescription());
    }

    /**
     * @dataProvider validElementProvider
     *
     * @param Field[] $fields
     */
    public function testAddValidElementShouldWork(array $fields, DatasetElement $element)
    {
        $this->dataset->setFields($fields);
        self::assertEmpty($this->dataset->getElements());
        $idFieldName = $this->dataset->getIdentifier()->getName();

        self::assertNull($this->dataset->getElement($element->getValue($idFieldName)));

        $this->dataset->addElement($element);
        self::assertCount(1, $this->dataset->getElements());

        // Make sure the element added matches the expected one
        $actual = $this->dataset->getElement($element->getValue($idFieldName));
        self::assertEquals($element, $actual);
        self::assertEquals($element->getValue($idFieldName), $element->getIdentifierValue());
        $values = $element->getValues();
        foreach ($values as $name => $value) {
            self::assertEquals($value, $actual->getValue($name));
            self::assertEquals($this->dataset, $element->getDataset());
        }
    }

    public function validElementProvider()
    {
        return [
            'Test 1' => [
                [
                    (new Field('id', FieldType::ID))->setIdentifier(true),
                    new Field('name', FieldType::STRING),
                    new Field('email', FieldType::EMAIL),
                    new Field('url', FieldType::URL),
                ],
                (new DatasetElement())
                    ->addValue('id', 1)
                    ->addValue('name', 'Test')
                    ->addValue('email', 'test@test.com')
                    ->addValue('url', 'https://test.com'),
            ],
        ];
    }

    /**
     * @dataProvider invalidElementProvider
     *
     * @param Field[] $fields
     */
    public function testAddInvalidElementShouldThrowException(array $fields, DatasetElement $element, string $expectedMessage, string $expectedException = \InvalidArgumentException::class)
    {
        $this->expectException($expectedException);
        $this->expectExceptionMessage($expectedMessage);
        $this->dataset->setFields($fields);
        $this->dataset->addElement($element);
    }

    public function invalidElementProvider()
    {
        return [
            'Missing Identifier Value' => [
                [
                    (new Field('id', FieldType::ID))->setIdentifier(true),
                    new Field('name', FieldType::STRING),
                    new Field('email', FieldType::EMAIL),
                    new Field('url', FieldType::URL),
                ],
                (new DatasetElement())
                    ->addValue('url', 'http://test.com')
                    ->addValue('name', 'Test')
                    ->addValue('email', 'test@test.com'),
                "Invalid element for DataSet Test DataSet. It is missing a value for Identifier 'id' field.",
            ],
            'Missing 1 Field' => [
                [
                    (new Field('id', FieldType::ID))->setIdentifier(true),
                    new Field('name', FieldType::STRING),
                    new Field('email', FieldType::EMAIL),
                    new Field('url', FieldType::URL),
                ],
                (new DatasetElement())
                    ->addValue('id', 1)
                    ->addValue('name', 'Test')
                    ->addValue('email', 'test@test.com'),
                'Invalid element for DataSet Test DataSet. Missing Fields: url',
            ],
            'Missing Mulitple Fields' => [
                [
                    (new Field('id', FieldType::ID))->setIdentifier(true),
                    new Field('name', FieldType::STRING),
                    new Field('email', FieldType::EMAIL),
                    new Field('url', FieldType::URL),
                ],
                (new DatasetElement())
                    ->addValue('id', 1)
                    ->addValue('name', 'Test'),
                'Invalid element for DataSet Test DataSet. Missing Fields: email,url',
            ],
            'Extra Field' => [
                [
                    (new Field('id', FieldType::ID))->setIdentifier(true),
                    new Field('name', FieldType::STRING),
                    new Field('email', FieldType::EMAIL),
                    new Field('url', FieldType::URL),
                ],
                (new DatasetElement())
                    ->addValue('id', 1)
                    ->addValue('name', 'Test')
                    ->addValue('email', 'test@test.com')
                    ->addValue('url', 'http://test.com')
                    ->addValue('unknown', 'unknown value'),
                'Invalid element for DataSet Test DataSet. Unknown Fields: unknown',
            ],
            'Extra Fields' => [
                [
                    (new Field('id', FieldType::ID))->setIdentifier(true),
                    new Field('name', FieldType::STRING),
                    new Field('email', FieldType::EMAIL),
                    new Field('url', FieldType::URL),
                ],
                (new DatasetElement())
                    ->addValue('id', 1)
                    ->addValue('name', 'Test')
                    ->addValue('email', 'test@test.com')
                    ->addValue('url', 'http://test.com')
                    ->addValue('invalid', 'http://test.com')
                    ->addValue('unknown', 'unknown value'),
                'Invalid element for DataSet Test DataSet. Unknown Fields: invalid,unknown',
            ],
            'Missing and Extra Fields' => [
                [
                    (new Field('id', FieldType::ID))->setIdentifier(true),
                    new Field('name', FieldType::STRING),
                    new Field('email', FieldType::EMAIL),
                    new Field('url', FieldType::URL),
                ],
                (new DatasetElement())
                    ->addValue('id', 1)
                    ->addValue('name', 'Test')
                    ->addValue('invalid', 'http://test.com')
                    ->addValue('unknown', 'unknown value'),
                'Invalid element for DataSet Test DataSet. Missing Fields: email,url. Unknown Fields: invalid,unknown',
            ],
            'Invalid Values' => [
                [
                    (new Field('id', FieldType::ID))->setIdentifier(true),
                    new Field('name', FieldType::STRING),
                    new Field('email', FieldType::EMAIL),
                    new Field('url', FieldType::URL),
                ],
                (new DatasetElement())
                    ->addValue('id', 1)
                    ->addValue('name', 'Test')
                    ->addValue('email', 'http://test.com')
                    ->addValue('url', 'unknown value'),
                'Invalid element for DataSet Test DataSet. The following values are not compatible with their associated field: email (http://test.com is not a valid email),url (unknown value is not a valid url)',
            ],
            'Missing Identifier Field' => [
                [
                    new Field('id', FieldType::ID),
                    new Field('name', FieldType::STRING),
                    new Field('email', FieldType::EMAIL),
                    new Field('url', FieldType::URL),
                ],
                (new DatasetElement())
                    ->addValue('id', 1)
                    ->addValue('name', 'Test')
                    ->addValue('email', 'test@test.com')
                    ->addValue('url', 'http://test.com'),
                'You must define an Identifier field for DataSet Test DataSet before adding elements to it.',
                \LogicException::class,
            ],
        ];
    }

    public function testSetElementsShouldWork()
    {
        $this->dataset->setFields([
            (new Field('id', FieldType::ID))->setIdentifier(true),
            new Field('name', FieldType::STRING),
            new Field('email', FieldType::EMAIL),
            new Field('url', FieldType::URL),
        ]);

        $element1 = new DatasetElement();
        $element1
            ->addValue('id', 1)
            ->addValue('name', 'Element 1')
            ->addValue('email', 'element1@test.com')
            ->addValue('url', 'http://element1.test.com');

        self::assertEmpty($this->dataset->getElements());

        $this->dataset->setElements([$element1]);
        self::assertCount(1, $this->dataset->getElements());
        $idFieldName = $this->dataset->getIdentifier()->getName();
        $actual = $this->dataset->getElements()[$element1->getValue($idFieldName)];
        self::assertNotNull($actual);
        $values = $element1->getValues();
        foreach ($values as $name => $value) {
            self::assertEquals($value, $actual->getValue($name));
        }

        // Adding 2 Elements
        $element2 = new DatasetElement([
            'id' => 2,
            'name' => 'Element 2',
            'email' => 'element2@test.com',
            'url' => 'http://element2.test.com',
        ]);

        $this->dataset->setElements([$element1, $element2]);
        self::assertCount(2, $this->dataset->getElements());

        $actual = $this->dataset->getElements()[$element1->getValue($idFieldName)];
        self::assertNotNull($actual);
        $values = $element1->getValues();
        foreach ($values as $name => $value) {
            self::assertEquals($value, $actual->getValue($name));
        }

        $actual = $this->dataset->getElements()[$element2->getValue($idFieldName)];
        self::assertNotNull($actual);
        $values = $element2->getValues();
        foreach ($values as $name => $value) {
            self::assertEquals($value, $actual->getValue($name));
        }

        // Overriding elements
        $element3 = new DatasetElement();
        $element3
            ->addValue('id', 3)
            ->addValue('name', 'Element 3')
            ->addValue('email', 'element3@test.com')
            ->addValue('url', 'http://element3.test.com');

        $this->dataset->setElements([$element3]);
        self::assertCount(1, $this->dataset->getElements());
        $actual = $this->dataset->getElements()[$element3->getValue($idFieldName)];
        self::assertNotNull($actual);
        $values = $element3->getValues();
        foreach ($values as $name => $value) {
            self::assertEquals($value, $actual->getValue($name));
        }
    }

    public function testAddSameElementTwiceThrowsException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Element '1' has already been added to this DataSet. Make sure to assign a unique 'id' value to each element of your DataSet.");

        $this->dataset->setFields([
            (new Field('id', FieldType::ID))->setIdentifier(true),
            new Field('name', FieldType::STRING),
            new Field('email', FieldType::EMAIL),
            new Field('url', FieldType::URL),
        ]);

        $element1 = new DatasetElement([
            'id' => 1,
            'name' => 'Element 1',
            'email' => 'element1@test.com',
            'url' => 'http://element1.test.com',
        ]);

        self::assertEmpty($this->dataset->getElements());

        $this->dataset->addElement($element1);
        self::assertCount(1, $this->dataset->getElements());

        $element2 = new DatasetElement([
            'id' => 1,
            'name' => 'Element 2',
            'email' => 'element2@test.com',
            'url' => 'http://element2.test.com',
        ]);

        $this->dataset->addElement($element2);
    }

    /**
     * @dataProvider isUniqueFieldProvider
     */
    public function testIsUniqueFieldShouldPass(Dataset $dataset, string $fieldName, bool $expectedValue)
    {
        $value = $dataset->isUniqueField($fieldName);
        self::assertEquals($expectedValue, $value);
    }

    public function isUniqueFieldProvider()
    {
        $bundle = TestHelper::getSampleBusinessBundle();
        $plan = $bundle->getDataset('Plan');

        return [
            'Unknown Field' => [$plan, 'unknownField', false],
            'Unique Field' => [$plan, 'name', true],
            'Non Unique Field' => [$plan, 'active', false],
        ];
    }
}
