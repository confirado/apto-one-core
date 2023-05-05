<?php

namespace Apto\Catalog\Domain\Core\Model\Shop;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;
use Money\Currency;

class ShopCurrencyUpdated extends AbstractDomainEvent
{
    /**
     * @var Currency
     */
    private $currency;

    /**
     * ShopCurrencyUpdated constructor.
     * @param AptoUuid $id
     * @param Currency $currency
     */
    public function __construct(AptoUuid $id, Currency $currency)
    {
        parent::__construct($id);
        $this->currency = $currency;
    }

    /**
     * @return Currency
     */
    public function getCurrency(): Currency
    {
        return $this->currency;
    }
}