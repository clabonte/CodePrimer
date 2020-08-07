<?php

namespace CodePrimer\Model\Data;

use CodePrimer\Model\BusinessModel;
use CodePrimer\Model\Field;
use InvalidArgumentException;

class InputData extends Data
{
    // Constants used to specify the type of input we are dealing with
    /** @var string Input data is expected to be new */
    const NEW = 'new';
    /** @var string Input data is expected to be updated */
    const UPDATED = 'updated';
    /** @var string Input data is expected to be new or updated */
    const NEW_OR_UPDATED = 'newOrUpdated';
    /** @var string Input is expected to already exist but is no longer valid */
    const OBSOLETE = 'obsolete';

    /** @var string The type of input expected. One of the following constants: NEW, EXISTING, NEW_OR_EXISTING or OBSOLETE */
    private $type;

    /** @var bool Whether this data is mandatory or optional */
    private $mandatory;

    /**
     * InputData constructor.
     *
     * @param Field|string $field
     */
    public function __construct(string $type, BusinessModel $businessModel, $field, bool $mandatory = true, string $details = self::BASIC)
    {
        $this->validate($type);
        parent::__construct($businessModel, $field, $details);
        $this->type = $type;
        $this->mandatory = $mandatory;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type The type of input expected. One of the following constants: NEW, EXISTING, NEW_OR_EXISTING or OBSOLETE
     *
     * @throws InvalidArgumentException If the type provided is not valid
     */
    public function setType(string $type): InputData
    {
        $this->validate($type);
        $this->type = $type;

        return $this;
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
    public function setMandatory(bool $mandatory): InputData
    {
        $this->mandatory = $mandatory;

        return $this;
    }

    /**
     * @throws InvalidArgumentException If the type provided is not valid
     */
    private function validate(string $type)
    {
        switch ($type) {
            case self::NEW:
            case self::UPDATED:
            case self::NEW_OR_UPDATED:
            case self::OBSOLETE:
                break;
            default:
                throw new InvalidArgumentException('Invalid type provided: '.$type.'. Must be one of: new, updated, newOrUpdated or obsolete');
        }
    }
}
