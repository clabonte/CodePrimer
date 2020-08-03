<?php

namespace CodePrimer\Tests\Helper;

use CodePrimer\Adapter\RelationalDatabaseAdapter;
use CodePrimer\Model\BusinessModel;
use CodePrimer\Model\Package;
use CodePrimer\Model\RelationshipSide;

class RelationshipTestHelper
{
    /** @var Package */
    private $package;

    /** @var BusinessModel */
    private $user;

    /** @var BusinessModel */
    private $subscription;

    /** @var BusinessModel */
    private $metadata;

    /** @var BusinessModel */
    private $post;

    /** @var BusinessModel */
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
        $this->user = $this->package->getBusinessModel('User');
        $this->subscription = $this->package->getBusinessModel('Subscription');
        $this->metadata = $this->package->getBusinessModel('Metadata');
        $this->post = $this->package->getBusinessModel('Post');
        $this->topic = $this->package->getBusinessModel('Topic');
    }

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
