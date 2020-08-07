<?php

namespace CodePrimer\Tests\Model;

use CodePrimer\Model\BusinessBundle;
use CodePrimer\Tests\Helper\TestHelper;
use PHPUnit\Framework\TestCase;

class RelationshipSideTest extends TestCase
{
    /** @var BusinessBundle */
    private $package;

    public function setUp(): void
    {
        parent::setUp();
        $this->package = TestHelper::getSamplePackage();
    }

    public function testIsBidirectionalShouldPass()
    {
        $user = $this->package->getBusinessModel('User');
        self::assertNotNull($user);

        $post = $this->package->getBusinessModel('Post');
        self::assertNotNull($post);

        self::assertFalse($user->getField('stats')->getRelation()->isBidirectional(), 'Unidirectional One-to-One validation failure');
        self::assertTrue($user->getField('subscription')->getRelation()->isBidirectional(), 'Bidirectional One-to-One validation failure');
        self::assertFalse($user->getField('metadata')->getRelation()->isBidirectional(), 'Unidirectional One-to-Many validation failure');
        self::assertTrue($user->getField('posts')->getRelation()->isBidirectional(), 'Bidirectional One-to-Many validation failure');
        self::assertTrue($post->getField('author')->getRelation()->isBidirectional(), 'Bidirectional Many-to-One validation failure');
        self::assertTrue($user->getField('topics')->getRelation()->isBidirectional(), 'Bidirectional Many-to-Many validation failure');
    }
}
