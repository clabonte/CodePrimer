<?php

namespace CodePrimer\Model\Data;

/**
 * Class EventDataBundle
 * This class carries a set of EventData.
 */
class EventDataBundle extends DataBundle
{
    public function __construct(string $name = '', string $description = '')
    {
        parent::__construct($name, $description);
    }

    /**
     * Adds data to the bundle, converting it to an InputData instance if it is not one based on the field's mandatory
     * attribute.
     */
    public function add(Data $data): DataBundle
    {
        if (!$data instanceof EventData) {
            $inputData = new EventData($data->getBusinessModel(), $data->getField(), $data->getField()->isMandatory());

            return parent::add($inputData);
        }

        return parent::add($data);
    }
}
