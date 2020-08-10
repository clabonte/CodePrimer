<?php

namespace CodePrimer\Model\Data;

use InvalidArgumentException;

/**
 * Class ExistingDataBundle
 * This class carries a set of ExistingData only along with a query indicating how to retrieve them.
 */
class ExistingDataBundle extends DataBundle
{
    /** @var string The source of the data */
    private $source;

    public function __construct(string $source = ExistingData::DEFAULT_SOURCE, string $name = '', string $description = '')
    {
        parent::__construct($name, $description);
        $this->source = $source;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getSource(): string
    {
        return $this->source;
    }

    public function add(ExistingData $data): DataBundle
    {
        return parent::addData($data);
    }
}
