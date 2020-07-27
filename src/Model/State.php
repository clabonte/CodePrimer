<?php
/**
 * Created by PhpStorm.
 * User: cbonte
 * Date: 2019-05-19
 * Time: 5:34 PM
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
    private $transitions = array();

    /**
     * State constructor.
     * @param string $name
     * @param string $description
     * @param bool $initial
     * @param bool $final
     */
    public function __construct(string $name, string $description = '', bool $initial = false, bool $final = false)
    {
        $this->name = $name;
        $this->description = $description;
        $this->initial = $initial;
        $this->final = $final;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return bool
     */
    public function isInitial(): bool
    {
        return $this->initial;
    }

    /**
     * @param bool $initial
     */
    public function setInitial(bool $initial): void
    {
        $this->initial = $initial;
    }

    /**
     * @return bool
     */
    public function isFinal(): bool
    {
        return $this->final;
    }

    /**
     * @param bool $final
     */
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
     * @param Transition $transition
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
