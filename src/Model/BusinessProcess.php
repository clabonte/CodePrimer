<?php

namespace CodePrimer\Model;

use CodePrimer\Model\Data\ContextDataBundle;
use CodePrimer\Model\Data\DataBundle;
use CodePrimer\Model\Data\ExternalDataBundle;
use CodePrimer\Model\Data\InternalDataBundle;
use CodePrimer\Model\Derived\Event;
use CodePrimer\Model\Derived\Message;
use InvalidArgumentException;

class BusinessProcess
{
    // The following constants define the list of process types supported
    const CREATE = 'create';
    const RETRIEVE = 'retrieve';
    const UPDATE = 'update';
    const DELETE = 'delete';
    const CUSTOM = 'custom';

    /** @var string One of the constants above */
    private $type;

    /** @var string */
    private $category = '';

    /** @var string */
    private $name;

    /** @var string */
    private $description;

    /** @var Event The event that can trigger this process */
    private $event;

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

    /** @var DataBundle[][] List of data used/required as input for this process */
    private $requiredData = [];

    /** @var DataBundle[][] List of data updates produced as an outcome of this process */
    private $producedData = [];

    /** @var Message[] List of messages that can be produced as an outcome of this process */
    private $messages = [];

    /**
     * BusinessProcess constructor.
     */
    public function __construct(string $name, string $description, Event $event, string $type = self::CUSTOM, bool $synchronous = true, bool $asynchronous = false)
    {
        $this->validate($type);
        $this->type = $type;
        $this->name = $name;
        $this->description = $description;
        $this->event = $event;
        $this->synchronous = $synchronous;
        $this->asynchronous = $asynchronous;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function setType(string $type): BusinessProcess
    {
        $this->validate($type);
        $this->type = $type;

        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getCategory(): string
    {
        return $this->category;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setCategory(string $category): BusinessProcess
    {
        $this->category = $category;

        return $this;
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
    public function getEvent(): Event
    {
        return $this->event;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setEvent(Event $event): BusinessProcess
    {
        $this->event = $event;

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

    public function isDataRequired(): bool
    {
        return count($this->requiredData) > 0;
    }

    /**
     * @codeCoverageIgnore
     *
     * @return DataBundle[][]
     */
    public function getRequiredData(): array
    {
        return $this->requiredData;
    }

    public function addRequiredData(DataBundle $dataBundle): BusinessProcess
    {
        $type = get_class($dataBundle);
        if (empty($this->requiredData[$type])) {
            $this->requiredData[$type] = [];
        }
        $this->requiredData[$type][$dataBundle->getName()] = $dataBundle;

        return $this;
    }

    public function isContextDataRequired(): bool
    {
        $type = ContextDataBundle::class;

        return isset($this->requiredData[$type]) && (count($this->requiredData[$type]) > 0);
    }

    /**
     * @return DataBundle[]
     */
    public function getRequiredContextData(): array
    {
        if ($this->isContextDataRequired()) {
            return $this->requiredData[ContextDataBundle::class];
        }

        return [];
    }

    public function isInternalDataRequired(): bool
    {
        $type = InternalDataBundle::class;

        return isset($this->requiredData[$type]) && (count($this->requiredData[$type]) > 0);
    }

    /**
     * @return DataBundle[]
     */
    public function getRequiredInternalData(): array
    {
        if ($this->isInternalDataRequired()) {
            return $this->requiredData[InternalDataBundle::class];
        }

        return [];
    }

    public function isExternalDataRequired(): bool
    {
        $type = ExternalDataBundle::class;

        return isset($this->requiredData[$type]) && (count($this->requiredData[$type]) > 0);
    }

    /**
     * @return DataBundle[]
     */
    public function getRequiredExternalData(): array
    {
        if ($this->isExternalDataRequired()) {
            return $this->requiredData[ExternalDataBundle::class];
        }

        return [];
    }

    /**
     * @codeCoverageIgnore
     *
     * @return DataBundle[]
     */
    public function getProducedData(): array
    {
        return $this->producedData;
    }

    public function isDataProduced(): bool
    {
        return count($this->producedData) > 0;
    }

    public function addProducedData(DataBundle $dataBundle): BusinessProcess
    {
        $type = get_class($dataBundle);
        if (empty($this->producedData[$type])) {
            $this->producedData[$type] = [];
        }
        $this->producedData[$type][$dataBundle->getName()] = $dataBundle;

        return $this;
    }

    public function isContextDataProduced(): bool
    {
        $type = ContextDataBundle::class;

        return isset($this->producedData[$type]) && (count($this->producedData[$type]) > 0);
    }

    /**
     * @return DataBundle[]
     */
    public function getProducedContextData(): array
    {
        if ($this->isContextDataProduced()) {
            return $this->producedData[ContextDataBundle::class];
        }

        return [];
    }

    public function isInternalDataProduced(): bool
    {
        $type = InternalDataBundle::class;

        return isset($this->producedData[$type]) && (count($this->producedData[$type]) > 0);
    }

    /**
     * @return DataBundle[]
     */
    public function getProducedInternalData(): array
    {
        if ($this->isInternalDataProduced()) {
            return $this->producedData[InternalDataBundle::class];
        }

        return [];
    }

    public function isExternalDataProduced(): bool
    {
        $type = ExternalDataBundle::class;

        return isset($this->producedData[$type]) && (count($this->producedData[$type]) > 0);
    }

    /**
     * @return DataBundle[]
     */
    public function getProducedExternalData(): array
    {
        if ($this->isExternalDataProduced()) {
            return $this->producedData[ExternalDataBundle::class];
        }

        return [];
    }

    /**
     * @codeCoverageIgnore
     *
     * @return Message[]
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * @codeCoverageIgnore
     */
    public function addMessage(Message $message): BusinessProcess
    {
        $this->messages[$message->getId()] = $message;

        return $this;
    }

    public function isMessageProduced(): bool
    {
        return count($this->messages) > 0;
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

    private function validate($type)
    {
        switch ($type) {
            case self::CUSTOM:
            case self::CREATE:
            case self::RETRIEVE:
            case self::UPDATE:
            case self::DELETE:
                break;
            default:
                throw new InvalidArgumentException('Invalid type provided: '.$type.'. Must be one of: create, retrieve, update, delete, custom');
        }
    }
}
