<?php

namespace CodePrimer\Model\Data;

use CodePrimer\Model\BusinessModel;
use CodePrimer\Model\Field;

class EventData extends Data
{
    /** @var bool Whether this data is mandatory or optional */
    private $mandatory;

    /**
     * InputData constructor.
     *
     * @param Field|string $field
     */
    public function __construct(BusinessModel $businessModel, $field, bool $mandatory = true, string $details = self::BASIC)
    {
        parent::__construct($businessModel, $field, $details);
        $this->mandatory = $mandatory;
    }

    /**
     * @codeCoverageIgnore
     */
    public function isMandatory(): bool
    {
        return $this->mandatory;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setMandatory(bool $mandatory): EventData
    {
        $this->mandatory = $mandatory;

        return $this;
    }
}
