<?php

namespace Apto\Base\Domain\Core\Model\AptoPrice;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Money\Money;

class AptoPriceAdded extends AbstractAptoPriceEvent
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
     * AptoPricePriceChanged constructor.
     * @param AptoUuid $id
     * @param AptoUuid $reference
     * @param Money $price
     * @param AptoUuid $customerGroupId
     */
    public function __construct(AptoUuid $id, AptoUuid $reference, Money $price, AptoUuid $customerGroupId)
    {
        parent::__construct($id, $reference);
        $this->price = $price;
        $this->customerGroupId = $customerGroupId;
    }

    /**
     * @return Money
     */
    public function getPrice(): Money
    {
        return $this->price;
    }

    /**
     * @return AptoUuid
     */
    public function getCustomerGroupId(): AptoUuid
    {
        return $this->customerGroupId;
    }
}