<?php

namespace Apto\Base\Domain\Core\Model\AptoPriceFormula;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Money\Currency;

class AptoPriceFormulaAdded extends AbstractAptoPriceFormulaEvent
{
    /**
     * @var string
     */
    protected $formula;

    /**
     * @var Currency
     */
    protected $currency;

    /**
     * @var AptoUuid
     */
    protected $customerGroupId;

    /**
     * AptoPriceFormulaAdded constructor.
     * @param AptoUuid $id
     * @param AptoUuid $reference
     * @param string $formula
     * @param Currency $currency
     * @param AptoUuid $customerGroupId
     */
    public function __construct(AptoUuid $id, AptoUuid $reference, string $formula, Currency $currency, AptoUuid $customerGroupId)
    {
        parent::__construct($id, $reference);
        $this->formula = $formula;
        $this->currency = $currency;
        $this->customerGroupId = $customerGroupId;
    }

    /**
     * @return string
     */
    public function getFormula(): string
    {
        return $this->formula;
    }

    /**
     * @return Currency
     */
    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    /**
     * @return AptoUuid
     */
    public function getCustomerGroupId(): AptoUuid
    {
        return $this->customerGroupId;
    }

}