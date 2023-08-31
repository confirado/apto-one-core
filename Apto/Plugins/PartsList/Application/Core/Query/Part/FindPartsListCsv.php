<?php

namespace Apto\Plugins\PartsList\Application\Core\Query\Part;

use Apto\Base\Application\Core\PublicQueryInterface;

class FindPartsListCsv implements PublicQueryInterface
{
    /**
     * @var string
     */
    private string $productId;

    /**
     * @var array
     */
    private array $state;

    /**
     * @var string
     */
    private string $currency;

    /**
     * @var string
     */
    private string $customerGroupId;

    /**
     * @param string $productId
     * @param array $state
     * @param string $currency
     * @param string $customerGroupId
     */
    public function __construct(
        string $productId,
        array $state,
        string $currency,
        string $customerGroupId
    ) {
        $this->productId = $productId;
        $this->state = $state;
        $this->currency = $currency;
        $this->customerGroupId = $customerGroupId;
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
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @return string
     */
    public function getCustomerGroupId(): string
    {
        return $this->customerGroupId;
    }
}

