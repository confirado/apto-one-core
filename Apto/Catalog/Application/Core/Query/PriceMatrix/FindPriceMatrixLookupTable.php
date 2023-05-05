<?php

namespace Apto\Catalog\Application\Core\Query\PriceMatrix;

use Apto\Base\Application\Core\PublicQueryInterface;

class FindPriceMatrixLookupTable implements PublicQueryInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var array
     */
    private $shopCurrency;

    /**
     * @var string|null
     */
    private $customerGroupExternalId;

    /**
     * FindPriceMatrixRules constructor.
     * @param string $id
     * @param array $shopCurrency
     * @param string|null $customerGroupExternalId
     */
    public function __construct(string $id, array $shopCurrency, string $customerGroupExternalId = null)
    {
        $this->id = $id;
        $this->shopCurrency = $shopCurrency;
        $this->customerGroupExternalId = $customerGroupExternalId;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getShopCurrency(): array
    {
        return $this->shopCurrency;
    }

    /**
     * @return string|null
     */
    public function getCustomerGroupExternalId()
    {
        return $this->customerGroupExternalId;
    }
}