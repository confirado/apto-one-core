<?php

namespace Apto\Catalog\Application\Core\Query\Configuration;

use Apto\Base\Application\Core\PublicQueryInterface;

/**
 * @internal NEVER USE THIS QUERY TO MAKE AN ORDER! ONLY FOR DISPLAY PURPOSE!
 * Class FindPriceByState
 * @package Apto\Catalog\Application\Core\Query\Configuration
 */
class FindPriceByState implements PublicQueryInterface
{
    /**
     * @var string
     */
    private $productId;

    /**
     * @var array
     */
    private $state;

    /**
     * @var array
     */
    private $shopCurrency;

    /**
     * @var array
     */
    private $displayCurrency;

    /**
     * @var string|null
     */
    private $customerGroupExternalId;

    /**
     * @var string
     */
    private $locale;

    /**
     * @var array
     */
    private $sessionCookies;

    /**
     * @var string|null
     */
    private $taxState;

    /**
     * @var array
     */
    private array $connectorUser;

    /**
     * @param string $productId
     * @param array $state
     * @param array $shopCurrency
     * @param array $displayCurrency
     * @param string|null $customerGroupExternalId
     * @param string $locale
     * @param array $sessionCookies
     * @param string|null $taxState
     */
    public function __construct(
        string $productId,
        array $state,
        array $shopCurrency,
        array $displayCurrency,
        string $customerGroupExternalId = null,
        string $locale,
        array $sessionCookies = [],
        ?string $taxState = null,
        ?array $connectorUser = []
    )
    {
        $this->productId = $productId;
        $this->state = $state;
        $this->shopCurrency = $shopCurrency;
        $this->displayCurrency = $displayCurrency;
        $this->customerGroupExternalId = $customerGroupExternalId;
        $this->locale = $locale;
        $this->sessionCookies = $sessionCookies;
        $this->taxState = $taxState;
        $this->connectorUser = $connectorUser ?? [];
    }

    /**
     * @return string
     */
    public function getProductId(): string
    {
        return $this->productId;
    }

    /**
     * @return array
     */
    public function getState(): array
    {
        return $this->state;
    }

    /**
     * @return array
     */
    public function getShopCurrency(): array
    {
        return $this->shopCurrency;
    }

    /**
     * @return array
     */
    public function getDisplayCurrency(): array
    {
        return $this->displayCurrency;
    }

    /**
     * @return string|null
     */
    public function getCustomerGroupExternalId()
    {
        return $this->customerGroupExternalId;
    }

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * @return array
     */
    public function getSessionCookies(): array
    {
        return $this->sessionCookies;
    }

    /**
     * @return string|null
     */
    public function getTaxState(): ?string
    {
        return $this->taxState;
    }

    /**
     * @return array
     */
    public function getConnectorUser(): array
    {
        return $this->connectorUser;
    }
}
