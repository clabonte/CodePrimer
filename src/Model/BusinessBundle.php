<?php
/**
 * Created by PhpStorm.
 * User: cbonte
 * Date: 2019-05-19
 * Time: 3:23 PM.
 */

namespace CodePrimer\Model;

use CodePrimer\Model\Derived\Event;

class BusinessBundle
{
    /** @var string */
    private $namespace;

    /** @var string */
    private $name;

    /** @var string */
    private $description;

    /** @var BusinessModel[] */
    private $businessModels = [];

    /** @var BusinessProcess[] */
    private $businessProcesses = [];

    /** @var Event[] */
    private $events = [];

    /** @var Set[] */
    private $sets = [];

    /**
     * Package constructor.
     *
     * @param $namespace
     * @param $name
     */
    public function __construct($namespace, $name)
    {
        $this->setNamespace($namespace);
        $this->name = $name;
    }

    /**
     * @codeCoverageIgnore
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @codeCoverageIgnore
     * @param string $namespace
     *
     * @return BusinessBundle
     */
    public function setNamespace($namespace): self
    {
        $this->namespace = rtrim($namespace, '\\/');

        return $this;
    }

    /**
     * @codeCoverageIgnore
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @codeCoverageIgnore
     * @return BusinessBundle
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @codeCoverageIgnore
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @codeCoverageIgnore
     * @return BusinessBundle
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @codeCoverageIgnore
     * @return BusinessModel[]
     */
    public function getBusinessModels(): array
    {
        return $this->businessModels;
    }

    /**
     * @return BusinessBundle
     */
    public function addBusinessModel(BusinessModel $businessModel): self
    {
        $this->businessModels[$businessModel->getName()] = $businessModel;

        return $this;
    }

    public function getBusinessModel(string $name): ?BusinessModel
    {
        if (isset($this->businessModels[$name])) {
            return $this->businessModels[$name];
        }

        return null;
    }

    /**
     * @param BusinessModel[] $businessModels
     *
     * @return BusinessBundle
     */
    public function setBusinessModels(array $businessModels): self
    {
        $this->businessModels = [];

        foreach ($businessModels as $businessModel) {
            $this->addBusinessModel($businessModel);
        }

        return $this;
    }

    /**
     * @codeCoverageIgnore
     * @return BusinessProcess[]
     */
    public function getBusinessProcesses(): array
    {
        return $this->businessProcesses;
    }

    /**
     * @return BusinessBundle
     */
    public function addBusinessProcess(BusinessProcess $businessProcess): self
    {
        $this->businessProcesses[$businessProcess->getName()] = $businessProcess;

        return $this;
    }

    public function getBusinessProcess(string $name): ?BusinessProcess
    {
        if (isset($this->businessProcesses[$name])) {
            return $this->businessProcesses[$name];
        }

        return null;
    }

    /**
     * @param BusinessProcess[] $businessModels
     *
     * @return BusinessBundle
     */
    public function setBusinessProcesses(array $businessProcesses): self
    {
        $this->businessProcesses = [];

        foreach ($businessProcesses as $businessProcess) {
            $this->addBusinessProcess($businessProcess);
        }

        return $this;
    }

    /**
     * @return Event[]
     */
    public function getEvents(): array
    {
        return $this->events;
    }

    /**
     * @param Event[] $events
     *
     * @return BusinessBundle
     */
    public function setEvents(array $events): self
    {
        $this->events = [];

        foreach ($events as $businessModel) {
            $this->addEvent($businessModel);
        }

        return $this;
    }

    /**
     * @return BusinessBundle
     */
    public function addEvent(Event $event): self
    {
        $this->events[$event->getName()] = $event;

        return $this;
    }

    public function getEvent(string $name): ?Event
    {
        if (isset($this->events[$name])) {
            return $this->events[$name];
        }

        return null;
    }

    /**
     * @codeCoverageIgnore
     * @return Set[]
     */
    public function listSets(): array
    {
        return $this->sets;
    }

    public function getSet(string $name): ?Set
    {
        if (isset($this->sets[$name])) {
            return $this->sets[$name];
        }

        return null;
    }

    /**
     * @param Set[] $sets
     *
     * @return BusinessBundle
     */
    public function setSets(array $sets): self
    {
        $this->sets = [];

        foreach ($sets as $set) {
            $this->addSet($set);
        }

        return $this;
    }

    /**
     * @return BusinessBundle
     */
    public function addSet(Set $set): self
    {
        $this->sets[$set->getName()] = $set;

        return $this;
    }
}
