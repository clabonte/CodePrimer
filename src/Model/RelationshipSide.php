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
     * @param string $side
     * @param Entity $entity
     * @param Field|null $field
     */
    public function __construct(Entity $entity, Field $field = null)
    {
        $this->entity = $entity;
        $this->setField($field);
    }

    /**
     * @return string|null
     */
    public function getSide(): ?string
    {
        return $this->side;
    }

    /**
     * Checks if this is the left side of a relationship
     * @return bool
     */
    public function isLeft(): bool
    {
        if (isset($this->side) && $this->side == RelationshipSide::LEFT) {
            return true;
        }

        return false;
    }

    /**
     * Checks if this is the right side of a relationship
     * @return bool
     */
    public function isRight(): bool
    {
        if (isset($this->side) && $this->side == RelationshipSide::RIGHT) {
            return true;
        }

        return false;
    }

    /**
     * @return Entity
     */
    public function getEntity(): Entity
    {
        return $this->entity;
    }

    /**
     * @return Field|null
     */
    public function getField(): ?Field
    {
        return $this->field;
    }

    /**
     * @param Field|null $field
     * @return RelationshipSide
     */
    public function setField(?Field $field): RelationshipSide
    {
        // Remove the field's current relation
        if ($this->field !== null) {
            $this->field->setRelation(null);
        }

        // Update the field and set its relation
        $this->field = $field;
        if ($field !== null) {
            $field->setRelation($this);
        }

        return $this;
    }

    /**
     * @return Relationship
     */
    public function getRelationship(): Relationship
    {
        return $this->relationship;
    }

    /**
     * @param Relationship $relationship
     * @param string $side
     * @return RelationshipSide
     */
    public function setRelationship(Relationship $relationship, string $side): RelationshipSide
    {
        $this->relationship = $relationship;
        $this->side = $side;
        return $this;
    }

    /**
     * Returns the remote side of the relationship, if known
     * @return RelationshipSide|null
     */
    public function getRemoteSide(): ?RelationshipSide
    {
        if ($this->relationship !== null) {
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
