<?php

namespace Apto\Catalog\Domain\Core\Model\PriceMatrix;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;
use Money\Money;

class PriceMatrixElementPriceAdded extends AbstractDomainEvent
{
    /**
     * @var AptoUuid
     */
    private $priceMatrixElementId;

    /**
     * @var AptoUuid
     */
    private $customerGroupId;

    /**
     * @var Money
     */
    private $price;

    /**
     * PriceMatrixPositionAdded constructor.
     * @param AptoUuid $id
     * @param AptoUuid $priceMatrixElementId
     * @param AptoUuid $customerGroupId
     * @param Money $price
     */
    public function __construct(AptoUuid $id, AptoUuid $priceMatrixElementId, AptoUuid $customerGroupId, Money $price)
    {
        parent::__construct($id);
        $this->priceMatrixElementId = $priceMatrixElementId;
        $this->customerGroupId = $customerGroupId;
        $this->price = $price;
    }

    /**
     * @return AptoUuid
     */
    public function getPriceMatrixElementId(): AptoUuid
    {
        return $this->priceMatrixElementId;
    }

    /**
     * @return AptoUuid
     */
    public function getCustomerGroupId(): AptoUuid
    {
        return $this->customerGroupId;
    }

    /**
     * @return Money
     */
    public function getPrice(): Money
    {
        return $this->price;
    }
}