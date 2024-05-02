<?php

namespace Apto\Base\Domain\Core\Model\AptoPrice;

use Apto\Base\Domain\Core\Model\AptoEntity;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Money\Money;

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
     * AptoPrice constructor.
     * @param AptoUuid $id
     * @param Money $price
     * @param AptoUuid $customerGroupId
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
     * @param AptoUuid $id
     * @return AptoPrice
     */
    public function copy(AptoUuid $id): AptoPrice
    {
        //create new price
        $price = new AptoPrice(
            $id,
            $this->getPrice(),
            $this->getCustomerGroupId()
        );

        // return copy
        return $price;
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
}
