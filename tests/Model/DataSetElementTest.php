<?php

namespace CodePrimer\Tests\Model;

use CodePrimer\Helper\FieldType;
use CodePrimer\Model\DataSet;
use CodePrimer\Model\DataSetElement;
use CodePrimer\Model\Field;
use CodePrimer\Tests\Helper\TestHelper;
use PHPUnit\Framework\TestCase;

class DataSetElementTest extends TestCase
{
    public function testConstructorWithEmptyArrayShouldWork()
    {
        $element = new DataSetElement();
        self::assertEmpty($element->getValues());

        self::assertNull($element->getValue('url'));
        $element->addValue('url', 'http://element1.test.com');
        self::assertEquals('http://element1.test.com', $element->getValue('url'));
    }

    public function testConstructorWithAssociativeArrayShouldWork()
    {
        $element = new DataSetElement(['url' => 'http://element1.test.com']);
        self::assertCount(1, $element->getValues());

        self::assertEquals('http://element1.test.com', $element->getValue('url'));
    }

    /**
     * @dataProvider invalidArrayProvider
     */
    public function testConstructorWithInvalidArrayThrowsException(array $values)
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid array type passed. Must be an associative array of type 'name' (string) => 'value' (mixed)");
        new DataSetElement($values);
    }

    /**
     * @dataProvider invalidArrayProvider
     */
    public function testSetValuesWithInvalidArrayThrowsException(array $values)
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid array type passed. Must be an associative array of type 'name' (string) => 'value' (mixed)");
        $element = new DataSetElement();
        $element->setValues($values);
    }

    public function invalidArrayProvider()
    {
        return [
            'Standard array' => [['http://element.test.com', 'element@email.com']],
            'Mixed array' => [['url' => 'http://element.test.com', 'element@email.com']],
        ];
    }

    public function testGetIdentifierValueWithoutDatasetThrowsException()
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('You must assign a Dataset to an element before retrieving its identifier value');
        $element = new DataSetElement(['url' => 'http://element1.test.com']);
        $element->getIdentifierValue();
    }

    public function testGetUniqueNameWithoutDatasetThrowsException()
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('You must assign a Dataset to an element before retrieving its unique name');
        $element = new DataSetElement(['url' => 'http://element1.test.com']);
        $element->getUniqueName();
    }

    /**
     * @dataProvider uniqueNameProvider
     */
    public function testGetUniqueNameShouldPass(DataSetElement $element, string $expectedName)
    {
        $value = $element->getUniqueName();
        self::assertEquals($expectedName, $value);
    }

    public function uniqueNameProvider()
    {
        $bundle = TestHelper::getSampleBusinessBundle();
        $plan = $bundle->getDataSet('Plan');
        $userStatus = $bundle->getDataSet('UserStatus');

        $noNameSet = new DataSet('Test', 'Test');
        $noNameSet->setFields([
            (new Field('id', FieldType::INTEGER))->setIdentifier(true),
            new Field('description', FieldType::STRING),
        ]);

        $noNameSet->addElement(new DataSetElement([
            'id' => 123,
            'description' => 'User is registered but has not confirmed his email address yet',
        ]));
        $noNameSet->addElement(new DataSetElement([
            'id' => 124,
            'description' => 'User is fully registered and allowed to user our application',
        ]));

        $duplicateNameSet = new DataSet('Test', 'Test');
        $duplicateNameSet->setFields([
            (new Field('id', FieldType::INTEGER))->setIdentifier(true),
            new Field('name', FieldType::STRING),
        ]);

        $duplicateNameSet->addElement(new DataSetElement([
            'id' => 123,
            'name' => 'Name 1',
        ]));
        $duplicateNameSet->addElement(new DataSetElement([
            'id' => 124,
            'name' => 'Name 2',
        ]));
        $duplicateNameSet->addElement(new DataSetElement([
            'id' => 125,
            'name' => 'Name 2',
        ]));

        return [
            'Plan' => [$plan->getElements()[1], 'Admin'],
            'UserStatus' => [$userStatus->getElements()['active'], 'active'],
            'Plan with no name field' => [$noNameSet->getElements()[123], 'Test_123'],
            'Plan with duplicate name' => [$duplicateNameSet->getElements()[123], 'Test_123'],
        ];
    }
}
