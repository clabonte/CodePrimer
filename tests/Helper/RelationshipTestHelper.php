<?php

namespace CodePrimer\Tests\Helper;

use CodePrimer\Adapter\RelationalDatabaseAdapter;
use CodePrimer\Model\BusinessBundle;
use CodePrimer\Model\BusinessModel;
use CodePrimer\Model\RelationshipSide;

class RelationshipTestHelper
{
    /** @var BusinessBundle */
    private $businessBundle;

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
        $this->businessBundle = TestHelper::getSampleBusinessBundle();
        $adapter = new RelationalDatabaseAdapter();
        $adapter->generateRelationalFields($this->businessBundle);

        // Extract the common entities used for testing
        $this->user = $this->businessBundle->getBusinessModel('User');
        $this->subscription = $this->businessBundle->getBusinessModel('Subscription');
        $this->metadata = $this->businessBundle->getBusinessModel('Metadata');
        $this->post = $this->businessBundle->getBusinessModel('Post');
        $this->topic = $this->businessBundle->getBusinessModel('Topic');
    }

    public function getBusinessBundle(): BusinessBundle
    {
        return $this->businessBundle;
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
