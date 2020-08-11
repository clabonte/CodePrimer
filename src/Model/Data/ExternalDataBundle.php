<?php

namespace CodePrimer\Model\Data;

/**
 * Class ExternalDataBundle
 * This class carries a set of external Data associated with a given ApiClient along with a key that can be used to
 * retrieve or update them.
 */
class ExternalDataBundle extends DataBundle
{
    /** @var string Constant used to represent the application's default API client */
    const DEFAULT_API_CLIENT = 'default';

    /** @var string The source of the data */
    private $apiClient;

    public function __construct(string $name = '', string $description = '', string $apiClient = self::DEFAULT_API_CLIENT)
    {
        parent::__construct($name, $description);
        $this->apiClient = $apiClient;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setApiClient(string $apiClient): ExternalDataBundle
    {
        $this->apiClient = $apiClient;

        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getApiClient(): string
    {
        return $this->apiClient;
    }
}
