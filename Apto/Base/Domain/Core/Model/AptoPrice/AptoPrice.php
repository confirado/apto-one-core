<?php

namespace Apto\Base\Domain\Core\Model\AptoPrice;

use Money\Money;
use Apto\Base\Domain\Core\Model\AptoEntity;
use Apto\Base\Domain\Core\Model\AptoUuid;

class AptoPrice extends AptoEntity
{
    /**
     * @var Money
     */
    protected $price;

    /**
     * @var AptoUuid
     */
    protected $customerGroupId;

    /**
     * @var AptoUuid|null
     */
    protected ?AptoUuid $productConditionId;

    /**
     * @param AptoUuid $id
     * @param Money $price
     * @param AptoUuid $customerGroupId
     * @param AptoUuid|null $productConditionId
     */
    public function __construct(AptoUuid $id, Money $price, AptoUuid $customerGroupId, ?AptoUuid $productConditionId = null)
    {
        parent::__construct($id);
        $this->price = $price;
        $this->customerGroupId = $customerGroupId;
        $this->productConditionId = $productConditionId;
    }

    /**
     * @param Money $price
     * @return AptoPrice
     */
    public function setPrice(Money $price): AptoPrice
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @return Money
     */
    public function getPrice(): Money
    {
        return $this->price;
    }

    /**
     * @param AptoUuid $customerGroupId
     * @return AptoPrice
     */
    public function setCustomerGroupId(AptoUuid $customerGroupId): AptoPrice
    {
        $this->customerGroupId = $customerGroupId;
        return $this;
    }

    /**
     * @return AptoUuid
     */
    public function getCustomerGroupId(): AptoUuid
    {
        return $this->customerGroupId;
    }

    /**
     * @param $amount
     * @return AptoPrice
     */
    public function setPriceAmount($amount): AptoPrice
    {
        $this->price = new Money($amount, $this->price->getCurrency());
        return $this;
    }

    /**
     * @return AptoUuid|null
     */
    public function getProductConditionId(): ?AptoUuid
    {
        return $this->productConditionId;
    }

    /**
     * @param AptoUuid|null $productConditionId
     */
    public function setProductConditionId(?AptoUuid $productConditionId): void
    {
        $this->productConditionId = $productConditionId;
    }

    /**
     * @param AptoUuid $id
     * @return AptoPrice
     */
    public function copy(AptoUuid $id): AptoPrice
    {
        //create new price
        $price = new AptoPrice(
            $id,
            $this->getPrice(),
            $this->getCustomerGroupId(),
            $this->getProductConditionId()
        );

        // return copy
        return $price;
    }
}
