<?php


namespace CodePrimer\Tests\Helper;


use CodePrimer\Adapter\RelationalDatabaseAdapter;
use CodePrimer\Model\Entity;
use CodePrimer\Model\Package;
use CodePrimer\Model\RelationshipSide;

class RelationshipTestHelper
{
    /** @var Package  */
    private $package;

    /** @var Entity */
    private $user;

    /** @var Entity */
    private $subscription;

    /** @var Entity */
    private $metadata;

    /** @var Entity */
    private $post;

    /** @var Entity */
    private $topic;

    /**
     * RelationshipTestHelper constructor.
     */
    public function __construct()
    {
        $this->package = TestHelper::getSamplePackage();
        $adapter = new RelationalDatabaseAdapter();
        $adapter->generateRelationalFields($this->package);

        // Extract the common entities used for testing
        $this->user = $this->package->getEntity('User');
        $this->subscription = $this->package->getEntity('Subscription');
        $this->metadata = $this->package->getEntity('Metadata');
        $this->post = $this->package->getEntity('Post');
        $this->topic = $this->package->getEntity('Topic');
    }

    /**
     * @return \CodePrimer\Model\Package
     */
    public function getPackage(): Package
    {
        return $this->package;
    }

    public function getOneToOneUnidirectionalRelationship(): RelationshipSide
    {
        return $this->user->getField('stats')->getRelation();
    }

    public function getOneToOneBidirectionalLeftRelationship(): RelationshipSide
    {
        return $this->user->getField('subscription')->getRelation();
    }

    public function getOneToOneBidirectionalRightRelationship(): RelationshipSide
    {
        return $this->subscription->getField('user')->getRelation();
    }

    public function getOneToManyRelationship(): RelationshipSide
    {
        return $this->user->getField('metadata')->getRelation();
    }

    public function getManytoOneRelationship(): RelationshipSide
    {
        return $this->metadata->getField('user')->getRelation();
    }

    public function getManyToManyLeftRelationship(): RelationshipSide
    {
        return $this->user->getField('topics')->getRelation();
    }

    public function getManyToManyRightRelationship(): RelationshipSide
    {
        return $this->topic->getField('authors')->getRelation();
    }
}
