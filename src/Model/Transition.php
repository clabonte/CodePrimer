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

    /**
     * @codeCoverageIgnore
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getFromState(): State
    {
        return $this->fromState;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setFromState(State $fromState): void
    {
        $this->fromState = $fromState;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getToState(): State
    {
        return $this->toState;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setToState(State $toState): void
    {
        $this->toState = $toState;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getCondition(): ?string
    {
        return $this->condition;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setCondition(?string $condition): void
    {
        $this->condition = $condition;
    }
}
