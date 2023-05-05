<?php

namespace Apto\Catalog\Application\Frontend\Commands\Configuration;

class UpdateBasketConfiguration extends AbstractAddBasketConfiguration
{
    /**
     * @var null|string
     */
    private $configurationId;

    /**
     * UpdateBasketConfiguration constructor.
     * @param string $productId
     * @param string|null $configurationId
     * @param array $state
     * @param array $sessionCookies
     * @param string $locale
     * @param int|null $quantity
     * @param array $perspectives
     * @param array $additionalData
     */
    public function __construct
    (
        string $productId,
        string $configurationId = null,
        array $state,
        array $sessionCookies,
        string $locale,
        int $quantity = null,
        array $perspectives = ['persp1'],
        array $additionalData = []
    )
    {
        parent::__construct($productId, $state, $sessionCookies, $locale, $quantity, $perspectives, $additionalData);
        $this->configurationId = $configurationId;
    }

    /**
     * @return null|string
     */
    public function getConfigurationId()
    {
        return $this->configurationId;
    }
}