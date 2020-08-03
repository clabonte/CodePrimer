<?php

namespace CodePrimer\Tests\Helper;

use CodePrimer\Helper\FieldHelper;
use CodePrimer\Helper\PackageHelper;
use CodePrimer\Model\Field;
use CodePrimer\Model\Package;
use CodePrimer\Model\Relationship;
use CodePrimer\Model\RelationshipSide;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class PackageHelperTest extends TestCase
{
    /** @var PackageHelper */
    private $helper;

    /** @var Package */
    private $package;

    public function setUp(): void
    {
        parent::setUp();
        $this->helper = new PackageHelper();
        $this->package = TestHelper::getSamplePackage(true, false);
    }

    public function testBuildRelationshipsShouldPass()
    {
        // Make sure there is no relationship before building them
        foreach ($this->package->getBusinessModels() as $businessModel) {
            foreach ($businessModel->getFields() as $field) {
                self::assertNull($field->getRelation(), 'Unexpected relation found for field '.$field->getName().' in entity '.$businessModel->getName());
            }
        }

        $this->helper->buildRelationships($this->package);

        // Make sure it has only created relations on the right set of fields
        $fieldHelper = new FieldHelper();
        foreach ($this->package->getBusinessModels() as $businessModel) {
            foreach ($businessModel->getFields() as $field) {
                if ($fieldHelper->isBusinessModel($field, $this->package)) {
                    self::assertNotNull($field->getRelation(), 'Missing relation for field '.$field->getName().' in entity '.$businessModel->getName());
                } else {
                    self::assertNull($field->getRelation(), 'Unexpected relation found for field '.$field->getName().' in entity '.$businessModel->getName());
                }
            }
        }
    }

    /**
     * Validate the User -- 1-to-1 --> UserStats relationship has been properly created.
     */
    public function testBuildOneToOneUnidirectionalRelationshipsShouldPass()
    {
        $this->helper->buildRelationships($this->package);

        $userBusinessModel = $this->package->getBusinessModel('User');
        self::assertNotNull($userBusinessModel);

        $statsBusinessModel = $this->package->getBusinessModel('UserStats');
        self::assertNotNull($statsBusinessModel);

        $field = $userBusinessModel->getField('stats');
        self::assertNotNull($field);

        $relation = $field->getRelation();
        self::assertNotNull($relation, 'Relation not found for stats field');
        self::assertEquals(RelationshipSide::LEFT, $relation->getSide());
        self::assertEquals($userBusinessModel, $relation->getBusinessModel());
        self::assertEquals($field, $relation->getField());

        $remoteSide = $relation->getRemoteSide();
        self::assertNotNull($remoteSide, 'Remote side of relation not found');
        self::assertEquals(RelationshipSide::RIGHT, $remoteSide->getSide());
        self::assertEquals($statsBusinessModel, $remoteSide->getBusinessModel());
        self::assertNull($remoteSide->getField());

        $relationship = $relation->getRelationship();
        self::assertNotNull($relationship, 'Relationship not found');
        self::assertEquals(Relationship::ONE_TO_ONE, $relationship->getType());
        self::assertEquals($relation, $relationship->getLeftSide());
        self::assertEquals($remoteSide, $relationship->getRightSide());
    }

    /**
     * Validate the User -- 1-to-* --> Metadata relationship has been properly created.
     */
    public function testBuildOneToManyUnidirectionalRelationshipsShouldPass()
    {
        $this->helper->buildRelationships($this->package);

        $userBusinessModel = $this->package->getBusinessModel('User');
        self::assertNotNull($userBusinessModel);

        $metadataBusinessModel = $this->package->getBusinessModel('Metadata');
        self::assertNotNull($metadataBusinessModel);

        $field = $userBusinessModel->getField('metadata');
        self::assertNotNull($field);

        $relation = $field->getRelation();
        self::assertNotNull($relation, 'Relation not found for metadata field');
        self::assertEquals(RelationshipSide::LEFT, $relation->getSide());
        self::assertEquals($userBusinessModel, $relation->getBusinessModel());
        self::assertEquals($field, $relation->getField());

        $remoteSide = $relation->getRemoteSide();
        self::assertNotNull($remoteSide, 'Remote side of relation not found');
        self::assertEquals(RelationshipSide::RIGHT, $remoteSide->getSide());
        self::assertEquals($metadataBusinessModel, $remoteSide->getBusinessModel());
        self::assertNull($remoteSide->getField());

        $relationship = $relation->getRelationship();
        self::assertNotNull($relationship, 'Relationship not found');
        self::assertEquals(Relationship::ONE_TO_MANY, $relationship->getType());
        self::assertEquals($relation, $relationship->getLeftSide());
        self::assertEquals($remoteSide, $relationship->getRightSide());
    }

    /**
     * Validate the User <-- 1-to-* --> Post relationship has been properly created.
     */
    public function testBuildOneToManyBidirectionalRelationshipsShouldPass()
    {
        $this->helper->buildRelationships($this->package);

        $userBusinessModel = $this->package->getBusinessModel('User');
        self::assertNotNull($userBusinessModel);

        $postBusinessModel = $this->package->getBusinessModel('Post');
        self::assertNotNull($postBusinessModel);

        $remoteField = $postBusinessModel->getField('author');
        self::assertNotNull($remoteField);

        $field = $userBusinessModel->getField('posts');
        self::assertNotNull($field);

        $relation = $field->getRelation();
        self::assertNotNull($relation, 'Relation not found for posts field');
        self::assertEquals(RelationshipSide::LEFT, $relation->getSide());
        self::assertEquals($userBusinessModel, $relation->getBusinessModel());
        self::assertEquals($field, $relation->getField());

        $remoteSide = $relation->getRemoteSide();
        self::assertNotNull($remoteSide, 'Remote side of relation not found');
        self::assertEquals(RelationshipSide::RIGHT, $remoteSide->getSide());
        self::assertEquals($postBusinessModel, $remoteSide->getBusinessModel());
        self::assertEquals($remoteField, $remoteSide->getField());

        $relationship = $relation->getRelationship();
        self::assertNotNull($relationship, 'Relationship not found');
        self::assertEquals(Relationship::ONE_TO_MANY, $relationship->getType());
        self::assertEquals($relation, $relationship->getLeftSide());
        self::assertEquals($remoteSide, $relationship->getRightSide());

        // Validate the other side of the relation
        $relation = $remoteField->getRelation();
        self::assertNotNull($relation, 'Relation not found for author field');
        self::assertEquals(RelationshipSide::RIGHT, $relation->getSide());
        self::assertEquals($postBusinessModel, $relation->getBusinessModel());
        self::assertEquals($remoteField, $relation->getField());

        $remoteSide = $relation->getRemoteSide();
        self::assertNotNull($remoteSide, 'Remote side of relation not found');
        self::assertEquals(RelationshipSide::LEFT, $remoteSide->getSide());
        self::assertEquals($userBusinessModel, $remoteSide->getBusinessModel());
        self::assertEquals($field, $remoteSide->getField());

        self::assertEquals($relationship, $relation->getRelationship());
    }

    /**
     * Validate the User <-- 1-to-* --> Post relationship built in reverse has been properly created.
     */
    public function testBuildReverseOneToManyBidirectionalRelationshipsShouldPass()
    {
        $userBusinessModel = $this->package->getBusinessModel('User');
        self::assertNotNull($userBusinessModel);

        $postBusinessModel = $this->package->getBusinessModel('Post');
        self::assertNotNull($postBusinessModel);

        // Create a new package with the entities in 'reverse' order
        $package = new Package('Test', 'TestPackage');
        $package->addBusinessModel($postBusinessModel);
        $package->addBusinessModel($userBusinessModel);

        // Build the relationships
        $this->helper->buildRelationships($package);

        $remoteField = $postBusinessModel->getField('author');
        self::assertNotNull($remoteField);

        $field = $userBusinessModel->getField('posts');
        self::assertNotNull($field);

        $relation = $field->getRelation();
        self::assertNotNull($relation, 'Relation not found for posts field');
        self::assertEquals(RelationshipSide::LEFT, $relation->getSide());
        self::assertEquals($userBusinessModel, $relation->getBusinessModel());
        self::assertEquals($field, $relation->getField());

        $remoteSide = $relation->getRemoteSide();
        self::assertNotNull($remoteSide, 'Remote side of relation not found');
        self::assertEquals(RelationshipSide::RIGHT, $remoteSide->getSide());
        self::assertEquals($postBusinessModel, $remoteSide->getBusinessModel());
        self::assertEquals($remoteField, $remoteSide->getField());

        $relationship = $relation->getRelationship();
        self::assertNotNull($relationship, 'Relationship not found');
        self::assertEquals(Relationship::ONE_TO_MANY, $relationship->getType());
        self::assertEquals($relation, $relationship->getLeftSide());
        self::assertEquals($remoteSide, $relationship->getRightSide());

        // Validate the other side of the relation
        $relation = $remoteField->getRelation();
        self::assertNotNull($relation, 'Relation not found for author field');
        self::assertEquals(RelationshipSide::RIGHT, $relation->getSide());
        self::assertEquals($postBusinessModel, $relation->getBusinessModel());
        self::assertEquals($remoteField, $relation->getField());

        $remoteSide = $relation->getRemoteSide();
        self::assertNotNull($remoteSide, 'Remote side of relation not found');
        self::assertEquals(RelationshipSide::LEFT, $remoteSide->getSide());
        self::assertEquals($userBusinessModel, $remoteSide->getBusinessModel());
        self::assertEquals($field, $remoteSide->getField());

        self::assertEquals($relationship, $relation->getRelationship());
    }

    /**
     * Validate the User <-- *-to-* --> Topic relationship has been properly created.
     */
    public function testBuildManyToManyRelationshipShouldPass()
    {
        $this->helper->buildRelationships($this->package);

        $userBusinessModel = $this->package->getBusinessModel('User');
        self::assertNotNull($userBusinessModel);

        $topicBusinessModel = $this->package->getBusinessModel('Topic');
        self::assertNotNull($topicBusinessModel);

        $remoteField = $topicBusinessModel->getField('authors');
        self::assertNotNull($remoteField);

        $field = $userBusinessModel->getField('topics');
        self::assertNotNull($field);

        $relation = $field->getRelation();
        self::assertNotNull($relation, 'Relation not found for topics field');
        self::assertEquals(RelationshipSide::LEFT, $relation->getSide());
        self::assertEquals($userBusinessModel, $relation->getBusinessModel());
        self::assertEquals($field, $relation->getField());

        $remoteSide = $relation->getRemoteSide();
        self::assertNotNull($remoteSide, 'Remote side of relation not found');
        self::assertEquals(RelationshipSide::RIGHT, $remoteSide->getSide());
        self::assertEquals($topicBusinessModel, $remoteSide->getBusinessModel());
        self::assertEquals($remoteField, $remoteSide->getField());

        $relationship = $relation->getRelationship();
        self::assertNotNull($relationship, 'Relationship not found');
        self::assertEquals(Relationship::MANY_TO_MANY, $relationship->getType());
        self::assertEquals($relation, $relationship->getLeftSide());
        self::assertEquals($remoteSide, $relationship->getRightSide());

        // Validate the other side of the relation
        $relation = $remoteField->getRelation();
        self::assertNotNull($relation, 'Relation not found for author field');
        self::assertEquals(RelationshipSide::RIGHT, $relation->getSide());
        self::assertEquals($topicBusinessModel, $relation->getBusinessModel());
        self::assertEquals($remoteField, $relation->getField());

        $remoteSide = $relation->getRemoteSide();
        self::assertNotNull($remoteSide, 'Remote side of relation not found');
        self::assertEquals(RelationshipSide::LEFT, $remoteSide->getSide());
        self::assertEquals($userBusinessModel, $remoteSide->getBusinessModel());
        self::assertEquals($field, $remoteSide->getField());

        self::assertEquals($relationship, $relation->getRelationship());
    }

    /**
     * Failure scenario: We do not currently support the creation of relationships when more than 1 potential
     * field can be used to build the relationship.
     */
    public function testBuildRelationshipForBusinessModelWithMultipleLinksShouldThrowException()
    {
        $postBusinessModel = $this->package->getBusinessModel('Post');
        self::assertNotNull($postBusinessModel);

        // Add a 'Reviewer' field to a post that also points to the 'User' entity
        $postBusinessModel->addField(new Field('reviewer', 'User'));

        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Multiple bidirectional relationships found between the same entities: User and Post. This is not supported yet');

        $this->helper->buildRelationships($this->package);
    }

    /**
     * Failure scenario: If the FieldHelper falsely identifies a type as an entity but the entity cannot be
     * located in the package, an exception shall be thrown.
     */
    public function testBuildRelationshipForUnknownBusinessModelShouldThrowException()
    {
        // Add a field to a fake entity to the Post entity
        $postBusinessModel = $this->package->getBusinessModel('Post');
        self::assertNotNull($postBusinessModel);
        $fakeField = new Field('reviewer', 'Unknown');
        $postBusinessModel->addField($fakeField);

        // Create a stub for the FieldHelper class that always returns true.
        $stub = $this->createMock(FieldHelper::class);
        $stub->method('isBusinessModel')
            ->will($this->returnCallback([$this, 'isBusinessModelStubCallback']));

        $packageHelper = new PackageHelper($stub);

        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Failed to locate remote entity Unknown in package FunctionalTest');

        $packageHelper->buildRelationships($this->package);
    }

    /**
     * Stub method for the FieldHelper mock.
     *
     * @return bool
     */
    public function isBusinessModelStubCallback()
    {
        $args = func_get_args();
        /** @var Field $field */
        $field = $args[0];
        if ('Unknown' == $field->getType()) {
            return true;
        }

        return false;
    }
}
