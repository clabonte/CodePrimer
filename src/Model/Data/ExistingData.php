<?php

namespace CodePrimer\Model\Data;

use CodePrimer\Model\BusinessModel;
use CodePrimer\Model\Field;
use InvalidArgumentException;

class ExistingData extends Data
{
    // Constants used to specify the origin of the data we are dealing with
    /** @var string The origin of the data is from the context (e.g. HTTP session) */
    const CONTEXT = 'context';
    /** @var string The origin of the data is an internal data source (i.e. DataClient) */
    const INTERNAL = 'internal';
    /** @var string The origin of the data is an external data source (i.e. ApiClient) */
    const EXTERNAL = 'external';

    /** @var string The origin of the data, one of the following constants: CONTEXT, INTERNAL or EXTERNAL */
    private $origin;

    /** @var string The source of the data (e.g. ApiClient name) */
    private $source;

    /**
     * ExistingData constructor.
     *
     * @param Field|string $field
     */
    public function __construct(string $origin, BusinessModel $businessModel, $field, string $source = '', string $details = self::BASIC)
    {
        $this->validate($origin);
        parent::__construct($businessModel, $field, $details);

        $this->origin = $origin;
        $this->source = $source;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getOrigin(): string
    {
        return $this->origin;
    }

    public function setOrigin(string $origin): ExistingData
    {
        $this->validate($origin);
        $this->origin = $origin;

        return $this;
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

    /**
     * @throws InvalidArgumentException If the origin specified in not valid
     */
    private function validate(string $origin)
    {
        switch ($origin) {
            case self::CONTEXT:
            case self::EXTERNAL:
            case self::INTERNAL:
                break;
            default:
                throw new InvalidArgumentException('Invalid origin provided: '.$origin.'. Must be one of: context, internal or external');
        }
    }
}
