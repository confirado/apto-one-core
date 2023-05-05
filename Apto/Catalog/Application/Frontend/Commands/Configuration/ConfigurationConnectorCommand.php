<?php

namespace Apto\Catalog\Application\Frontend\Commands\Configuration;

abstract class ConfigurationConnectorCommand extends ConfigurationCommand
{
    /**
     * @var array
     */
    private $sessionCookies;

    /**
     * ConfigurationConnectorCommand constructor.
     * @param string $productId
     * @param array $state
     * @param array $sessionCookies
     */
    public function __construct(string $productId, array $state, array $sessionCookies)
    {
        parent::__construct($productId, $state);
        $this->sessionCookies = $sessionCookies;
    }

    /**
     * @return array
     */
    public function getSessionCookies(): array
    {
        return $this->sessionCookies;
    }
}