<?php
/**
 * Created by PhpStorm.
 * User: cbonte
 * Date: 2019-05-19
 * Time: 3:35 PM
 */

namespace CodePrimer\Model;


class StateMachine
{
    /** @var string */
    private $name;

    /** @var State[] */
    private $states = array();

    /** @var Transition[] */
    private $transitions = array();

    /**
     * StateMachine constructor.
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param State $state
     */
    public function addState(State $state)
    {
        $this->states[$state->getName()] = $state;
    }

    /**
     * @param Transition $transition
     */
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
