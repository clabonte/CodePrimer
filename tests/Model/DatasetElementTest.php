<?php

namespace CodePrimer\Tests\Model;

use CodePrimer\Helper\FieldType;
use CodePrimer\Model\Dataset;
use CodePrimer\Model\DatasetElement;
use CodePrimer\Model\Field;
use CodePrimer\Tests\Helper\TestHelper;
use PHPUnit\Framework\TestCase;

class DatasetElementTest extends TestCase
{
    public function testConstructorWithEmptyArrayShouldWork()
    {
        $element = new DatasetElement();
        self::assertEmpty($element->getValues());

        self::assertNull($element->getValue('url'));
        $element->addValue('url', 'http://element1.test.com');
        self::assertEquals('http://element1.test.com', $element->getValue('url'));
    }

    public function testConstructorWithAssociativeArrayShouldWork()
    {
        $element = new DatasetElement(['url' => 'http://element1.test.com']);
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
        new DatasetElement($values);
    }

    /**
     * @dataProvider invalidArrayProvider
     */
    public function testSetValuesWithInvalidArrayThrowsException(array $values)
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid array type passed. Must be an associative array of type 'name' (string) => 'value' (mixed)");
        $element = new DatasetElement();
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
        $this->expectExceptionMessage('You must assign a Dataset (with an identifier) to an element before retrieving its identifier value');
        $element = new DatasetElement(['url' => 'http://element1.test.com']);
        $element->getIdentifierValue();
    }

    public function testGetIdentifierValueWithoutDatasetIdentifierThrowsException()
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('You must assign a Dataset (with an identifier) to an element before retrieving its identifier value');

        $dataset = new Dataset('Test');
        $dataset->addField(new Field('url', FieldType::URL));
        $element = new DatasetElement(['url' => 'http://element1.test.com']);
        $element->setDataset($dataset);
        $element->getIdentifierValue();
    }

    public function testGetUniqueNameWithoutDatasetThrowsException()
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('You must assign a Dataset to an element before retrieving its unique name');
        $element = new DatasetElement(['url' => 'http://element1.test.com']);
        $element->getUniqueName();
    }

    /**
     * @dataProvider uniqueNameProvider
     */
    public function testGetUniqueNameShouldPass(DatasetElement $element, string $expectedName)
    {
        $value = $element->getUniqueName();
        self::assertEquals($expectedName, $value);
    }

    public function uniqueNameProvider()
    {
        $bundle = TestHelper::getSampleBusinessBundle();
        $plan = $bundle->getDataset('Plan');
        $userStatus = $bundle->getDataset('UserStatus');

        $noNameSet = new Dataset('Test', 'Test');
        $noNameSet->setFields([
            (new Field('id', FieldType::INTEGER))->setIdentifier(true),
            new Field('description', FieldType::STRING),
        ]);

        $noNameSet->addElement(new DatasetElement([
            'id' => 123,
            'description' => 'User is registered but has not confirmed his email address yet',
        ]));
        $noNameSet->addElement(new DatasetElement([
            'id' => 124,
            'description' => 'User is fully registered and allowed to user our application',
        ]));

        $duplicateNameSet = new Dataset('Test', 'Test');
        $duplicateNameSet->setFields([
            (new Field('id', FieldType::INTEGER))->setIdentifier(true),
            new Field('name', FieldType::STRING),
        ]);

        $duplicateNameSet->addElement(new DatasetElement([
            'id' => 123,
            'name' => 'Name 1',
        ]));
        $duplicateNameSet->addElement(new DatasetElement([
            'id' => 124,
            'name' => 'Name 2',
        ]));
        $duplicateNameSet->addElement(new DatasetElement([
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
