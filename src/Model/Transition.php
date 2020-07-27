<?php
/**
 * Created by PhpStorm.
 * User: cbonte
 * Date: 2019-05-19
 * Time: 5:36 PM
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
     * @param string $name
     * @param string $description
     * @param State $fromState
     * @param State $toState
     * @param null|string $condition
     */
    public function __construct(string $name, string $description, State $fromState, State $toState, ?string $condition = null)
    {
        $this->name = $name;
        $this->description = $description;
        $this->fromState = $fromState;
        $this->toState = $toState;
        $this->condition = $condition;
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
     * @return State
     */
    public function getFromState(): State
    {
        return $this->fromState;
    }

    /**
     * @param State $fromState
     */
    public function setFromState(State $fromState): void
    {
        $this->fromState = $fromState;
    }

    /**
     * @return State
     */
    public function getToState(): State
    {
        return $this->toState;
    }

    /**
     * @param State $toState
     */
    public function setToState(State $toState): void
    {
        $this->toState = $toState;
    }

    /**
     * @return null|string
     */
    public function getCondition(): ?string
    {
        return $this->condition;
    }

    /**
     * @param null|string $condition
     */
    public function setCondition(?string $condition): void
    {
        $this->condition = $condition;
    }

}
