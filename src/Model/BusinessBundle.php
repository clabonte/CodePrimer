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
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @param string $namespace
     *
     * @return BusinessBundle
     */
    public function setNamespace($namespace): self
    {
        $this->namespace = rtrim($namespace, '\\/');

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return BusinessBundle
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return BusinessBundle
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
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
