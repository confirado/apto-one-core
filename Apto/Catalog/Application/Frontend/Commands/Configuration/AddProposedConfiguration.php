<?php

namespace Apto\Catalog\Application\Frontend\Commands\Configuration;

class AddProposedConfiguration extends ConfigurationCommand
{
    /**
     * @var string|null
     */
    private $uuid;

    /**
     * AddProposedConfiguration constructor.
     * @param string $productId
     * @param array $state
     * @param string|null $uuid
     */
    public function __construct(string $productId, array $state, string $uuid = null)
    {
        parent::__construct($productId, $state);
        $this->uuid = $uuid;
    }

    /**
     * @return string|null
     */
    public function getUuid()
    {
        return $this->uuid;
    }
}