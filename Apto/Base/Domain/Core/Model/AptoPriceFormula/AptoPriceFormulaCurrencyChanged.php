<?php

namespace Apto\Base\Domain\Core\Model\AptoPriceFormula;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Money\Currency;

class AptoPriceFormulaCurrencyChanged extends AbstractAptoPriceFormulaEvent
{
    /**
     * @var Currency
     */
    protected $currency;

    /**
     * AptoPriceFormulaCurrencyChanged constructor.
     * @param AptoUuid $id
     * @param AptoUuid $reference
     * @param Currency $currency
     */
    public function __construct(AptoUuid $id, AptoUuid $reference, Currency $currency)
    {
        parent::__construct($id, $reference);
        $this->currency = $currency;
    }

    /**
     * @return Currency
     */
    public function getPrice(): currency
    {
        return $this->currency;
    }
}