<?php

namespace CodePrimer\Model\Derived;

use CodePrimer\Model\Data\MessageDataBundle;
use InvalidArgumentException;

class Message
{
    const DEFAULT_BUNDLE = 'default';

    /** @var string */
    private $name;

    /** @var string */
    private $description;

    /** @var MessageDataBundle[] List of data bundles associated with this message */
    private $dataBundles = [];

    /** @var string The message's unique ID in the bundle */
    private $id;

    public function __construct(string $id, string $name = '', string $description = '')
    {
        if (empty($name)) {
            $name = $id;
        }
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setName(string $name): Message
    {
        $this->name = $name;

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
    public function setDescription(string $description): Message
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @codeCoverageIgnore
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @codeCoverageIgnore
     *
     * @return MessageDataBundle[]
     */
    public function getDataBundles(): array
    {
        return $this->dataBundles;
    }

    /**
     * Adds a data bundle to this event.
     *
     * @return $this
     *
     * @throws InvalidArgumentException If a bundle with the same name is already present
     */
    public function addDataBundle(MessageDataBundle $dataBundle): self
    {
        $name = $dataBundle->getName();
        if (empty($name)) {
            $name = self::DEFAULT_BUNDLE;
        }

        if (isset($this->dataBundles[$name])) {
            throw new InvalidArgumentException('DataBundle already present: '.$name.', please use a unique name for your bundle');
        }
        $this->dataBundles[$name] = $dataBundle;

        return $this;
    }

    /**
     * Retrieves a data bundle by its name.
     */
    public function getDataBundle(string $name = self::DEFAULT_BUNDLE): ?MessageDataBundle
    {
        if (isset($this->dataBundles[$name])) {
            return $this->dataBundles[$name];
        }

        return null;
    }
}
