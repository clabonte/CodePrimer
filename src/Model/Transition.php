<?php
/**
 * Created by PhpStorm.
 * User: cbonte
 * Date: 2019-05-19
 * Time: 5:36 PM.
 */

namespace CodePrimer\Model;

class Transition
{
    /** @var string */
    private $name;

    /** @var string */
    private $description;

    /** @var State */
    private $fromState;

    /** @var State */
    private $toState;

    /** @var string|null */
    private $condition;

    /**
     * Transition constructor.
     */
    public function __construct(string $name, string $description, State $fromState, State $toState, ?string $condition = null)
    {
        $this->name = $name;
        $this->description = $description;
        $this->fromState = $fromState;
        $this->toState = $toState;
        $this->condition = $condition;
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

    public function getFromState(): State
    {
        return $this->fromState;
    }

    public function setFromState(State $fromState): void
    {
        $this->fromState = $fromState;
    }

    public function getToState(): State
    {
        return $this->toState;
    }

    public function setToState(State $toState): void
    {
        $this->toState = $toState;
    }

    public function getCondition(): ?string
    {
        return $this->condition;
    }

    public function setCondition(?string $condition): void
    {
        $this->condition = $condition;
    }
}
