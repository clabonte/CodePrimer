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
        foreach ($this->package->getEntities() as $entity) {
            foreach ($entity->getFields() as $field) {
                self::assertNull($field->getRelation(), 'Unexpected relation found for field '.$field->getName(). ' in entity '.$entity->getName());
            }
        }

        $this->helper->buildRelationships($this->package);

        // Make sure it has only created relations on the right set of fields
        $fieldHelper = new FieldHelper();
        foreach ($this->package->getEntities() as $entity) {
            foreach ($entity->getFields() as $field) {
                if ($fieldHelper->isEntity($field, $this->package)) {
                    self::assertNotNull($field->getRelation(), 'Missing relation for field '.$field->getName(). ' in entity '.$entity->getName());
                } else {
                    self::assertNull($field->getRelation(), 'Unexpected relation found for field '.$field->getName(). ' in entity '.$entity->getName());
                }
            }
        }
    }

    /**
     * Validate the User -- 1-to-1 --> UserStats relationship has been properly created
     */
    public function testBuildOneToOneUnidirectionalRelationshipsShouldPass()
    {
        $this->helper->buildRelationships($this->package);

        $userEntity = $this->package->getEntity('User');
        self::assertNotNull($userEntity);

        $statsEntity = $this->package->getEntity('UserStats');
        self::assertNotNull($statsEntity);

        $field = $userEntity->getField('stats');
        self::assertNotNull($field);

        $relation = $field->getRelation();
        self::assertNotNull($relation, 'Relation not found for stats field');
        self::assertEquals(RelationshipSide::LEFT, $relation->getSide());
        self::assertEquals($userEntity, $relation->getEntity());
        self::assertEquals($field, $relation->getField());

        $remoteSide = $relation->getRemoteSide();
        self::assertNotNull($remoteSide, 'Remote side of relation not found');
        self::assertEquals(RelationshipSide::RIGHT, $remoteSide->getSide());
        self::assertEquals($statsEntity, $remoteSide->getEntity());
        self::assertNull($remoteSide->getField());

        $relationship = $relation->getRelationship();
        self::assertNotNull($relationship, 'Relationship not found');
        self::assertEquals(Relationship::ONE_TO_ONE, $relationship->getType());
        self::assertEquals($relation, $relationship->getLeftSide());
        self::assertEquals($remoteSide, $relationship->getRightSide());
    }

    /**
     * Validate the User -- 1-to-* --> Metadata relationship has been properly created
     */
    public function testBuildOneToManyUnidirectionalRelationshipsShouldPass()
    {
        $this->helper->buildRelationships($this->package);

        $userEntity = $this->package->getEntity('User');
        self::assertNotNull($userEntity);

        $metadataEntity = $this->package->getEntity('Metadata');
        self::assertNotNull($metadataEntity);

        $field = $userEntity->getField('metadata');
        self::assertNotNull($field);

        $relation = $field->getRelation();
        self::assertNotNull($relation, 'Relation not found for metadata field');
        self::assertEquals(RelationshipSide::LEFT, $relation->getSide());
        self::assertEquals($userEntity, $relation->getEntity());
        self::assertEquals($field, $relation->getField());

        $remoteSide = $relation->getRemoteSide();
        self::assertNotNull($remoteSide, 'Remote side of relation not found');
        self::assertEquals(RelationshipSide::RIGHT, $remoteSide->getSide());
        self::assertEquals($metadataEntity, $remoteSide->getEntity());
        self::assertNull($remoteSide->getField());

        $relationship = $relation->getRelationship();
        self::assertNotNull($relationship, 'Relationship not found');
        self::assertEquals(Relationship::ONE_TO_MANY, $relationship->getType());
        self::assertEquals($relation, $relationship->getLeftSide());
        self::assertEquals($remoteSide, $relationship->getRightSide());
    }

    /**
     * Validate the User <-- 1-to-* --> Post relationship has been properly created
     */
    public function testBuildOneToManyBidirectionalRelationshipsShouldPass()
    {
        $this->helper->buildRelationships($this->package);

        $userEntity = $this->package->getEntity('User');
        self::assertNotNull($userEntity);

        $postEntity = $this->package->getEntity('Post');
        self::assertNotNull($postEntity);

        $remoteField = $postEntity->getField('author');
        self::assertNotNull($remoteField);

        $field = $userEntity->getField('posts');
        self::assertNotNull($field);

        $relation = $field->getRelation();
        self::assertNotNull($relation, 'Relation not found for posts field');
        self::assertEquals(RelationshipSide::LEFT, $relation->getSide());
        self::assertEquals($userEntity, $relation->getEntity());
        self::assertEquals($field, $relation->getField());

        $remoteSide = $relation->getRemoteSide();
        self::assertNotNull($remoteSide, 'Remote side of relation not found');
        self::assertEquals(RelationshipSide::RIGHT, $remoteSide->getSide());
        self::assertEquals($postEntity, $remoteSide->getEntity());
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
        self::assertEquals($postEntity, $relation->getEntity());
        self::assertEquals($remoteField, $relation->getField());

        $remoteSide = $relation->getRemoteSide();
        self::assertNotNull($remoteSide, 'Remote side of relation not found');
        self::assertEquals(RelationshipSide::LEFT, $remoteSide->getSide());
        self::assertEquals($userEntity, $remoteSide->getEntity());
        self::assertEquals($field, $remoteSide->getField());

        self::assertEquals($relationship, $relation->getRelationship());
    }

    /**
     * Validate the User <-- 1-to-* --> Post relationship built in reverse has been properly created
     */
    public function testBuildReverseOneToManyBidirectionalRelationshipsShouldPass()
    {
        $userEntity = $this->package->getEntity('User');
        self::assertNotNull($userEntity);

        $postEntity = $this->package->getEntity('Post');
        self::assertNotNull($postEntity);

        // Create a new package with the entities in 'reverse' order
        $package = new Package('Test', 'TestPackage');
        $package->addEntity($postEntity);
        $package->addEntity($userEntity);

        // Build the relationships
        $this->helper->buildRelationships($package);

        $remoteField = $postEntity->getField('author');
        self::assertNotNull($remoteField);

        $field = $userEntity->getField('posts');
        self::assertNotNull($field);

        $relation = $field->getRelation();
        self::assertNotNull($relation, 'Relation not found for posts field');
        self::assertEquals(RelationshipSide::LEFT, $relation->getSide());
        self::assertEquals($userEntity, $relation->getEntity());
        self::assertEquals($field, $relation->getField());

        $remoteSide = $relation->getRemoteSide();
        self::assertNotNull($remoteSide, 'Remote side of relation not found');
        self::assertEquals(RelationshipSide::RIGHT, $remoteSide->getSide());
        self::assertEquals($postEntity, $remoteSide->getEntity());
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
        self::assertEquals($postEntity, $relation->getEntity());
        self::assertEquals($remoteField, $relation->getField());

        $remoteSide = $relation->getRemoteSide();
        self::assertNotNull($remoteSide, 'Remote side of relation not found');
        self::assertEquals(RelationshipSide::LEFT, $remoteSide->getSide());
        self::assertEquals($userEntity, $remoteSide->getEntity());
        self::assertEquals($field, $remoteSide->getField());

        self::assertEquals($relationship, $relation->getRelationship());
    }

    /**
     * Validate the User <-- *-to-* --> Topic relationship has been properly created
     */
    public function testBuildManyToManyRelationshipShouldPass()
    {
        $this->helper->buildRelationships($this->package);

        $userEntity = $this->package->getEntity('User');
        self::assertNotNull($userEntity);

        $topicEntity = $this->package->getEntity('Topic');
        self::assertNotNull($topicEntity);

        $remoteField = $topicEntity->getField('authors');
        self::assertNotNull($remoteField);

        $field = $userEntity->getField('topics');
        self::assertNotNull($field);

        $relation = $field->getRelation();
        self::assertNotNull($relation, 'Relation not found for topics field');
        self::assertEquals(RelationshipSide::LEFT, $relation->getSide());
        self::assertEquals($userEntity, $relation->getEntity());
        self::assertEquals($field, $relation->getField());

        $remoteSide = $relation->getRemoteSide();
        self::assertNotNull($remoteSide, 'Remote side of relation not found');
        self::assertEquals(RelationshipSide::RIGHT, $remoteSide->getSide());
        self::assertEquals($topicEntity, $remoteSide->getEntity());
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
        self::assertEquals($topicEntity, $relation->getEntity());
        self::assertEquals($remoteField, $relation->getField());

        $remoteSide = $relation->getRemoteSide();
        self::assertNotNull($remoteSide, 'Remote side of relation not found');
        self::assertEquals(RelationshipSide::LEFT, $remoteSide->getSide());
        self::assertEquals($userEntity, $remoteSide->getEntity());
        self::assertEquals($field, $remoteSide->getField());

        self::assertEquals($relationship, $relation->getRelationship());
    }

    /**
     * Failure scenario: We do not currently support the creation of relationships when more than 1 potential
     * field can be used to build the relationship
     */
    public function testBuildRelationshipForEntityWithMultipleLinksShouldThrowException()
    {
        $postEntity = $this->package->getEntity('Post');
        self::assertNotNull($postEntity);

        // Add a 'Reviewer' field to a post that also points to the 'User' entity
        $postEntity->addField(new Field('reviewer', 'User'));

        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Multiple bidirectional relationships found between the same entities: User and Post. This is not supported yet');

        $this->helper->buildRelationships($this->package);
    }

    /**
     * Failure scenario: If the FieldHelper falsely identifies a type as an entity but the entity cannot be
     * located in the package, an exception shall be thrown
     */
    public function testBuildRelationshipForUnknownEntityShouldThrowException()
    {
        // Add a field to a fake entity to the Post entity
        $postEntity = $this->package->getEntity('Post');
        self::assertNotNull($postEntity);
        $fakeField = new Field('reviewer', 'Unknown');
        $postEntity->addField($fakeField);

        // Create a stub for the FieldHelper class that always returns true.
        $stub = $this->createMock(FieldHelper::class);
        $stub->method('isEntity')
            ->will($this->returnCallback([$this, 'isEntityStubCallback']));

        $packageHelper = new PackageHelper($stub);

        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Failed to locate remote entity Unknown in package FunctionalTest');

        $packageHelper->buildRelationships($this->package);

    }

    /**
     * Stub method for the FieldHelper mock
     * @return bool
     */
    public function isEntityStubCallback()
    {
        $args = func_get_args();
        /** @var Field $field */
        $field = $args[0];
        if ($field->getType() == 'Unknown') {
            return true;
        }

        return false;
    }
}
