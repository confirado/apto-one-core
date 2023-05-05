<?php

namespace Apto\Base\Domain\Core\Model\AptoPrice;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Money\Money;

class AptoPricePriceChanged extends AbstractAptoPriceEvent
{
    /**
     * @var Money
     */
    protected $price;

    /**
     * AptoPricePriceChanged constructor.
     * @param AptoUuid $id
     * @param AptoUuid $reference
     * @param Money $price
     */
    public function __construct(AptoUuid $id, AptoUuid $reference, Money $price)
    {
        parent::__construct($id, $reference);
        $this->price = $price;
    }

    /**
     * @return Money
     */
    public function getPrice(): Money
    {
        return $this->price;
    }
}