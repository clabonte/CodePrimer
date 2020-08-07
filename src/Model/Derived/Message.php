<?php

namespace CodePrimer\Model\Derived;

class Message extends Event
{
    /** @var string The message's unique ID in the bundle */
    private $id;

    public function __construct(string $id, string $name = '', string $description = '')
    {
        if (empty($name)) {
            $name = $id;
        }
        parent::__construct($name, $description);
        $this->id = $id;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getId(): string
    {
        return $this->id;
    }
}
