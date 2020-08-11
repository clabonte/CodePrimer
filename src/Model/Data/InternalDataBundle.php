<?php

namespace CodePrimer\Model\Data;

/**
 * Class InternalDataBundle
 * This class carries a set of internal Data associated with a given DataClient along with a key that can be used to
 * fetch or update them.
 */
class InternalDataBundle extends DataBundle
{
    /** @var string Constant used to represent the application's default data client */
    const DEFAULT_DATA_CLIENT = 'default';

    /** @var string The source of the data */
    private $dataClient;

    public function __construct(string $name = '', string $description = '', string $dataClient = self::DEFAULT_DATA_CLIENT)
    {
        parent::__construct($name, $description);
        $this->dataClient = $dataClient;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setDataClient(string $dataClient): InternalDataBundle
    {
        $this->dataClient = $dataClient;

        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDataClient(): string
    {
        return $this->dataClient;
    }
}
