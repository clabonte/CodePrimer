<?php

namespace CodePrimer\Model\Data;

use CodePrimer\Model\BusinessModel;
use CodePrimer\Model\Field;

class ExistingData extends Data
{
    /** @var string Constant used to represent the application's default source */
    const DEFAULT_SOURCE = 'default';

    /** @var string The source of the data (e.g. ApiClient name) */
    private $source;

    /**
     * ExistingData constructor.
     *
     * @param Field|string $field
     */
    public function __construct(BusinessModel $businessModel, $field, string $source = self::DEFAULT_SOURCE, string $details = self::BASIC)
    {
        parent::__construct($businessModel, $field, $details);

        $this->source = $source;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getSource(): string
    {
        return $this->source;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setSource(string $source): ExistingData
    {
        $this->source = $source;

        return $this;
    }
}
