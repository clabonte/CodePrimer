<?php
/**
 * Created by PhpStorm.
 * User: cbonte
 * Date: 2019-05-19
 * Time: 3:23 PM.
 */

namespace CodePrimer\Model;

class Package
{
    /** @var string */
    private $namespace;

    /** @var string */
    private $name;

    /** @var string */
    private $description;

    /** @var Entity[] */
    private $entities = [];

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
     * @return Package
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
     * @return Package
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
     * @return Package
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Entity[]
     */
    public function getEntities(): array
    {
        return $this->entities;
    }

    /**
     * @return Package
     */
    public function addEntity(Entity $entity): self
    {
        $this->entities[$entity->getName()] = $entity;

        return $this;
    }

    public function getEntity(string $name): ?Entity
    {
        if (isset($this->entities[$name])) {
            return $this->entities[$name];
        }

        return null;
    }

    /**
     * @param Entity[] $entities
     *
     * @return Package
     */
    public function setEntities(array $entities): self
    {
        $this->entities = [];

        foreach ($entities as $entity) {
            $this->addEntity($entity);
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
     * @return Package
     */
    public function setEvents(array $events): self
    {
        $this->events = [];

        foreach ($events as $entity) {
            $this->addEvent($entity);
        }

        return $this;
    }

    /**
     * @return Package
     */
    public function addEvent(Event $event): self
    {
        $this->events[$event->getCode()] = $event;

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
     * @return Package
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
     * @return Package
     */
    public function addSet(Set $set): self
    {
        $this->sets[$set->getName()] = $set;

        return $this;
    }
}
