<?php
/**
 * Created by PhpStorm.
 * User: cbonte
 * Date: 2019-05-19
 * Time: 3:35 PM.
 */

namespace CodePrimer\Model;

class StateMachine
{
    /** @var string */
    private $name;

    /** @var State[] */
    private $states = [];

    /** @var Transition[] */
    private $transitions = [];

    /**
     * StateMachine constructor.
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function addState(State $state)
    {
        $this->states[$state->getName()] = $state;
    }

    public function addTransition(Transition $transition)
    {
        $this->transitions[$transition->getName()] = $transition;
    }

    /**
     * @return State[]
     */
    public function getStates(): array
    {
        return $this->states;
    }

    /**
     * @return Transition[]
     */
    public function getTransitions(): array
    {
        return $this->transitions;
    }
}
