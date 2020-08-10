<?php

namespace CodePrimer\Model;

class Relationship
{
    const ONE_TO_ONE = 'OneToOne';
    const ONE_TO_MANY = 'OneToMany';
    const MANY_TO_MANY = 'ManyToMany';

    /** @var string */
    private $type;

    /** @var RelationshipSide */
    private $leftSide;

    /** @var RelationshipSide */
    private $rightSide;

    /**
     * Relationship constructor.
     */
    public function __construct(string $type, RelationshipSide $leftSide, RelationshipSide $rightSide)
    {
        $this->type = $type;

        $leftSide->setRelationship($this, RelationshipSide::LEFT);
        $this->leftSide = $leftSide;

        $rightSide->setRelationship($this, RelationshipSide::RIGHT);
        $this->rightSide = $rightSide;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getLeftSide(): RelationshipSide
    {
        return $this->leftSide;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getRightSide(): RelationshipSide
    {
        return $this->rightSide;
    }
}
