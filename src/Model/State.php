<?php
/**
 * Created by PhpStorm.
 * User: cbonte
 * Date: 2019-05-19
 * Time: 5:34 PM.
 */

namespace CodePrimer\Model;

use InvalidArgumentException;

class State
{
    /** @var string */
    private $name;

    /** @var string */
    private $description;

    /** @var bool */
    private $initial = false;

    /** @var bool */
    private $final = false;

    /** @var Transition[] The list of possible transitions from this state */
    private $transitions = [];

    /**
     * State constructor.
     */
    public function __construct(string $name, string $description = '', bool $initial = false, bool $final = false)
    {
        $this->name = $name;
        $this->description = $description;
        $this->initial = $initial;
        $this->final = $final;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function isInitial(): bool
    {
        return $this->initial;
    }

    public function setInitial(bool $initial): void
    {
        $this->initial = $initial;
    }

    public function isFinal(): bool
    {
        return $this->final;
    }

    public function setFinal(bool $final): void
    {
        $this->final = $final;
    }

    /**
     * @return Transition[]
     */
    public function getTransitions(): array
    {
        return $this->transitions;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function addTransition(Transition $transition)
    {
        if ($transition->getFromState()->getName() != $this->name) {
            throw new InvalidArgumentException('Transition fromState must the same as the State to which it is being added');
        }

        $this->transitions[$transition->getName()] = $transition;
    }
}
