<?php

namespace CodePrimer\Model;

class RelationshipSide
{
    const LEFT = 'left';
    const RIGHT = 'right';

    /** @var string|null */
    private $side = null;

    /** @var Entity */
    private $entity;

    /** @var Field|null */
    private $field;

    /** @var Relationship The relationship this which this side belongs */
    private $relationship;

    /**
     * RelationshipSide constructor.
     *
     * @param string $side
     */
    public function __construct(Entity $entity, Field $field = null)
    {
        $this->entity = $entity;
        $this->setField($field);
    }

    public function getSide(): ?string
    {
        return $this->side;
    }

    /**
     * Checks if this is the left side of a relationship.
     */
    public function isLeft(): bool
    {
        if (isset($this->side) && RelationshipSide::LEFT == $this->side) {
            return true;
        }

        return false;
    }

    /**
     * Checks if this is the right side of a relationship.
     */
    public function isRight(): bool
    {
        if (isset($this->side) && RelationshipSide::RIGHT == $this->side) {
            return true;
        }

        return false;
    }

    public function getEntity(): Entity
    {
        return $this->entity;
    }

    public function getField(): ?Field
    {
        return $this->field;
    }

    public function setField(?Field $field): RelationshipSide
    {
        // Remove the field's current relation
        if (null !== $this->field) {
            $this->field->setRelation(null);
        }

        // Update the field and set its relation
        $this->field = $field;
        if (null !== $field) {
            $field->setRelation($this);
        }

        return $this;
    }

    public function getRelationship(): Relationship
    {
        return $this->relationship;
    }

    public function setRelationship(Relationship $relationship, string $side): RelationshipSide
    {
        $this->relationship = $relationship;
        $this->side = $side;

        return $this;
    }

    /**
     * Returns the remote side of the relationship, if known.
     */
    public function getRemoteSide(): ?RelationshipSide
    {
        if (null !== $this->relationship) {
            switch ($this->side) {
                case self::LEFT:
                    return $this->relationship->getRightSide();
                case self::RIGHT:
                    return $this->relationship->getLeftSide();
            }
        }

        return null;
    }
}
