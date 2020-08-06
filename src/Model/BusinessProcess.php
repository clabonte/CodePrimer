<?php

namespace CodePrimer\Model;

use CodePrimer\Model\Derived\Event;

class BusinessProcess
{
    /** @var string */
    private $name;

    /** @var string */
    private $description;

    /** @var Event The event that can trigger this process */
    private $trigger;

    /** @var bool Whether the process can be triggered synchronously */
    private $synchronous;

    /** @var bool Whether the process can be triggered asynchronously */
    private $asynchronous;

    /** @var array List of intervals at which this process must be triggered */
    private $periodicTriggers = [];

    /** @var bool Whether the process can be triggered from outside */
    private $externalAccess = false;

    /** @var array List of roles that are allowed to trigger this process. Empty = anyone */
    private $roles = [];

    /** @var array List of internal updates that can be produced as an outcome of this process */
    private $internalUpdates = [];

    /** @var array List of external updates that can be produced as an outcome of this process */
    private $externalUpdates = [];

    /** @var array List of messages that can be produced as an outcome of this process */
    private $messages = [];

    /**
     * BusinessProcess constructor.
     */
    public function __construct(string $name, string $description, Event $trigger, bool $synchronous = true, bool $asynchronous = false)
    {
        $this->name = $name;
        $this->description = $description;
        $this->trigger = $trigger;
        $this->synchronous = $synchronous;
        $this->asynchronous = $asynchronous;
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
    public function setName(string $name): BusinessProcess
    {
        $this->name = $name;

        return $this;
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
    public function setDescription(string $description): BusinessProcess
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTrigger(): Event
    {
        return $this->trigger;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setTrigger(Event $trigger): BusinessProcess
    {
        $this->trigger = $trigger;

        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public function isSynchronous(): bool
    {
        return $this->synchronous;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setSynchronous(bool $synchronous): BusinessProcess
    {
        $this->synchronous = $synchronous;

        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public function isAsynchronous(): bool
    {
        return $this->asynchronous;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setAsynchronous(bool $asynchronous): BusinessProcess
    {
        $this->asynchronous = $asynchronous;

        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getPeriodicTriggers(): array
    {
        return $this->periodicTriggers;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setPeriodicTriggers(array $periodicTriggers): BusinessProcess
    {
        $this->periodicTriggers = $periodicTriggers;

        return $this;
    }

    public function isPeriodic(): bool
    {
        return (null !== $this->periodicTriggers) && count($this->periodicTriggers) > 0;
    }

    /**
     * @codeCoverageIgnore
     */
    public function isExternalAccess(): bool
    {
        return $this->externalAccess;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setExternalAccess(bool $externalAccess): BusinessProcess
    {
        $this->externalAccess = $externalAccess;

        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getInternalUpdates(): array
    {
        return $this->internalUpdates;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setInternalUpdates(array $internalUpdates): BusinessProcess
    {
        $this->internalUpdates = $internalUpdates;

        return $this;
    }

    public function isProducingInternalUpdates(): bool
    {
        return (null !== $this->internalUpdates) && count($this->internalUpdates) > 0;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getExternalUpdates(): array
    {
        return $this->externalUpdates;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setExternalUpdates(array $externalUpdates): BusinessProcess
    {
        $this->externalUpdates = $externalUpdates;

        return $this;
    }

    public function isProducingExternalUpdates(): bool
    {
        return (null !== $this->externalUpdates) && count($this->externalUpdates) > 0;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setMessages(array $messages): BusinessProcess
    {
        $this->messages = $messages;

        return $this;
    }

    public function isProducingMessages(): bool
    {
        return (null !== $this->messages) && count($this->messages) > 0;
    }

    /**
     * @codeCoverageIgnore
     *
     * @return string[]
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @codeCoverageIgnore
     *
     * @param string[] $roles
     */
    public function setRoles(array $roles): BusinessProcess
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Checks if a given role is part of the 'restricted' list.
     */
    public function containsRole(string $role): bool
    {
        $result = false;

        if (isset($this->roles)) {
            $result = false !== array_search($role, $this->roles);
        }

        return $result;
    }

    /**
     * Adds a role allowed to call this business process.
     */
    public function addRole(string $role): BusinessProcess
    {
        if (!$this->containsRole($role)) {
            if (!isset($this->roles)) {
                $this->roles = [];
            }
            $this->roles[] = $role;
        }

        return $this;
    }

    public function isRestricted(): bool
    {
        return (null !== $this->roles) && count($this->roles) > 0;
    }

    /**
     * Checks if a given role is allowed to call this business process.
     */
    public function isAllowed(string $role): bool
    {
        if ($this->isRestricted()) {
            return $this->containsRole($role);
        }

        return true;
    }
}
