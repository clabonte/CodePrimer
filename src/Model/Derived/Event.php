<?php
/**
 * Created by PhpStorm.
 * User: cbonte
 * Date: 2019-05-19
 * Time: 3:31 PM.
 */

namespace CodePrimer\Model\Derived;

use CodePrimer\Model\Data\InputDataBundle;
use InvalidArgumentException;

class Event
{
    const DEFAULT_BUNDLE = 'default';

    /** @var string */
    private $name;

    /** @var string */
    private $description;

    /** @var InputDataBundle[] List of data bundles associated with this event */
    private $dataBundles = [];

    public function __construct(string $name, string $description = '')
    {
        $this->name = $name;
        $this->description = $description;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setName(string $name): Event
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
    public function setDescription(string $description): Event
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
     * @return InputDataBundle[]
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
    public function addDataBundle(InputDataBundle $dataBundle): self
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
    public function getDataBundle(string $name = self::DEFAULT_BUNDLE): ?InputDataBundle
    {
        if (isset($this->dataBundles[$name])) {
            return $this->dataBundles[$name];
        }

        return null;
    }
}
