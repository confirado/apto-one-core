<?php

namespace Apto\Catalog\Application\Frontend\Commands\Configuration;

abstract class AbstractAddBasketConfiguration extends ConfigurationConnectorCommand
{
    /**
     * @var string
     */
    protected $locale;

    /**
     * @var int
     */
    protected $quantity;

    /**
     * @var array
     */
    protected $perspectives;

    /**
     * @var array
     */
    protected $additionalData;

    /**
     * AddBasketConfiguration constructor.
     * @param string $productId
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
        array $state,
        array $sessionCookies,
        string $locale,
        int $quantity = null,
        array $perspectives = ['persp1'],
        array $additionalData = []
    )
    {
        parent::__construct($productId, $state, $sessionCookies);
        $this->locale = $locale;
        $this->quantity = $quantity === null ? 1 : $quantity;
        $this->perspectives = $perspectives;
        $this->additionalData = $additionalData;
    }

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @return array
     */
    public function getPerspectives(): array
    {
        return $this->perspectives;
    }

    /**
     * @return array
     */
    public function getAdditionalData(): array
    {
        return $this->additionalData;
    }
}