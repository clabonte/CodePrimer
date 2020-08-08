<?php

namespace CodePrimer\Model\Data;

use InvalidArgumentException;

/**
 * Class FetchDataBundle
 * This class carries a set of ExistingData only along with a query indicating how to retrieve them.
 */
class FetchDataBundle extends DataBundle
{
    /** @var string The source of the data */
    private $source;

    // TODO Replace by a proper class to define a query
    /** @var string */
    private $query;

    public function __construct(string $source = ExistingData::DEFAULT_SOURCE, string $name = '', string $description = '')
    {
        parent::__construct($name, $description);
        $this->source = $source;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setQuery(string $query): FetchDataBundle
    {
        $this->query = $query;

        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getSource(): string
    {
        return $this->source;
    }

    public function addData(Data $data): DataBundle
    {
        if ($data instanceof ExistingData) {
            if ($data->getSource() == $this->source) {
                return parent::addData($data);
            }
            throw new InvalidArgumentException('This DataBundle only supports data from the following source: '.$this->source.'. Received: '.$data->getSource());
        }
        throw new InvalidArgumentException('FetchDataBundle only supports ExistingData arguments');
    }
}
