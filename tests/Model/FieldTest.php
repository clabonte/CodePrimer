<?php

namespace CodePrimer\Tests\Model;

use CodePrimer\Helper\FieldType;
use CodePrimer\Model\Field;
use PHPUnit\Framework\TestCase;

class FieldTest extends TestCase
{
    public function testBasicSetters()
    {
        $field = new Field('TestName', FieldType::STRING, 'Test Description');

        self::assertEquals('TestName', $field->getName());
        self::assertEquals(FieldType::STRING, $field->getType());
        self::assertEquals('Test Description', $field->getDescription());
        self::assertFalse($field->isMandatory());
        self::assertFalse($field->isManaged());
        self::assertFalse($field->isList());
        self::assertFalse($field->isSearchable());
        self::assertNull($field->getDefault());
        self::assertNull($field->getExample());

        $field
            ->setManaged(true)
            ->setMandatory(true)
            ->setDescription('New Description')
            ->setName('NewName')
            ->setDefault('Default')
            ->setExample('Example')
            ->setList(true)
            ->setSearchable(true)
            ->setType(FieldType::URL);

        self::assertEquals('NewName', $field->getName());
        self::assertEquals(FieldType::URL, $field->getType());
        self::assertEquals('New Description', $field->getDescription());
        self::assertTrue($field->isMandatory());
        self::assertTrue($field->isManaged());
        self::assertTrue($field->isList());
        self::assertTrue($field->isSearchable());
        self::assertEquals('Default', $field->getDefault());
        self::assertEquals('Example', $field->getExample());
    }
}
