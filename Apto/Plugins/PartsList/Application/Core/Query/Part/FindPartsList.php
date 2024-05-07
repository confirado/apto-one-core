<?php

namespace Apto\Plugins\PartsList\Application\Core\Query\Part;

use Apto\Base\Application\Core\PublicQueryInterface;

class FindPartsList implements PublicQueryInterface
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
    private string $customerGroupExternalId;

    /**
     * @var string|null
     */
    private ?string $categoryId;

    /**
     * @param string $productId
     * @param array $state
     * @param string $currency
     * @param string $customerGroupExternalId
     */
    public function __construct(
        string $productId,
        array $state,
        string $currency,
        string $customerGroupExternalId,
        ?string $categoryId,
    ) {
        $this->productId = $productId;
        $this->state = $state;
        $this->currency = $currency;
        $this->customerGroupExternalId = $customerGroupExternalId;
        $this->categoryId = $categoryId;
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
    public function getCustomerGroupExternalId(): string
    {
        return $this->customerGroupExternalId;
    }

    /**
     * @return string|null
     */
    public function getCategoryId(): ?string
    {
        return $this->categoryId;
    }
}

