<?php

namespace CodePrimer\Tests\Model;

use CodePrimer\Helper\FieldType;
use CodePrimer\Model\BusinessModel;
use CodePrimer\Model\Field;
use CodePrimer\Model\Relationship;
use CodePrimer\Model\RelationshipSide;
use PHPUnit\Framework\TestCase;

class RelationshipTest extends TestCase
{
    public function testOneToOneUnidirectionalRelationshipShouldWork()
    {
        $leftModel = new BusinessModel('LeftModel');
        $leftField = new Field('LeftField', FieldType::STRING);
        $leftModel->addField($leftField);

        $rightModel = new BusinessModel('rightModel');
        $rightField = new Field('RightField', FieldType::STRING);
        $rightModel->addField($rightField);

        $leftSide = $this->createRelationshipSide($leftModel, $leftField);
        $rightSide = $this->createRelationshipSide($rightModel, null);

        $relationship = new Relationship(Relationship::ONE_TO_ONE, $leftSide, $rightSide);
        self::assertNotNull($relationship->getLeftSide());
        self::assertNotNull($relationship->getRightSide());

        // Validate left side
        $this->assertSide($leftSide, $rightSide, true, false);

        // Validate right side
        $this->assertSide($rightSide, $leftSide, false, false);
    }

    public function testOneToOneBidirectionalRelationshipShouldWork()
    {
        $leftModel = new BusinessModel('LeftModel');
        $leftField = new Field('LeftField', FieldType::STRING);
        $leftModel->addField($leftField);

        $rightModel = new BusinessModel('rightModel');
        $rightField = new Field('RightField', FieldType::STRING);
        $rightModel->addField($rightField);

        $leftSide = $this->createRelationshipSide($leftModel, $leftField);
        $rightSide = $this->createRelationshipSide($rightModel, $rightField);

        $relationship = new Relationship(Relationship::ONE_TO_ONE, $leftSide, $rightSide);
        self::assertNotNull($relationship->getLeftSide());
        self::assertNotNull($relationship->getRightSide());

        // Validate left side
        $this->assertSide($leftSide, $rightSide, true, true);

        // Validate right side
        $this->assertSide($rightSide, $leftSide, false, true);
    }

    public function testOverrideOneToOneBidirectionalRelationshipShouldWork()
    {
        $leftModel = new BusinessModel('LeftModel');
        $leftField = new Field('LeftField', FieldType::STRING);
        $leftModel->addField($leftField);

        $rightModel = new BusinessModel('rightModel');
        $rightField = new Field('RightField', FieldType::STRING);
        $rightModel->addField($rightField);

        $leftSide = $this->createRelationshipSide($leftModel, $leftField);
        $rightSide = $this->createRelationshipSide($rightModel, $rightField);

        $relationship = new Relationship(Relationship::ONE_TO_ONE, $leftSide, $rightSide);
        self::assertNotNull($relationship->getLeftSide());
        self::assertNotNull($relationship->getRightSide());

        // Validate left side
        $this->assertSide($leftSide, $rightSide, true, true);
        $this->assertEquals($leftSide, $leftField->getRelation());

        // Validate right side
        $this->assertSide($rightSide, $leftSide, false, true);
        $this->assertEquals($rightSide, $rightField->getRelation());

        // Remove the field on the right side...
        $rightSide->setField(null);
        // Validate left side is now unidirectional
        $this->assertSide($leftSide, $rightSide, true, false);
        $this->assertEquals($leftSide, $leftField->getRelation());

        // Validate right side is now unidirectional
        $this->assertSide($rightSide, $leftSide, false, false);
        $this->assertNull($rightField->getRelation());
    }

    private function createRelationshipSide(BusinessModel $model, Field $field = null): RelationshipSide
    {
        $side = new RelationshipSide($model, $field);
        self::assertNull($side->getRelationship());
        self::assertNull($side->getSide());
        self::assertNull($side->getRemoteSide());
        self::assertFalse($side->isLeft());
        self::assertFalse($side->isRight());

        return $side;
    }

    private function assertSide(RelationshipSide $side, RelationshipSide $remoteSide, bool $left, bool $bidirectional)
    {
        self::assertNotNull($side->getRelationship());
        self::assertNotNull($side->getSide());
        self::assertEquals($left, $side->isLeft());
        self::assertEquals(!$left, $side->isRight());
        self::assertEquals($remoteSide, $side->getRemoteSide());
        self::assertEquals($bidirectional, $side->isBidirectional());
    }
}
