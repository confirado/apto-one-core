<?php

namespace Apto\Base\Domain\Core\Model\AptoPriceFormula;

use Apto\Base\Domain\Core\Model\AptoEntity;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Money\Currency;

class AptoPriceFormula extends AptoEntity
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
     * @var AptoUuid|null
     */
    private ?AptoUuid $productConditionId;

    /**
     * AptoPrice constructor.
     * @param AptoUuid $id
     * @param string $formula
     * @param Currency $currency
     * @param AptoUuid $customerGroupId
     */
    public function __construct(AptoUuid $id, string $formula, Currency $currency, AptoUuid $customerGroupId, ?AptoUuid $productConditionId)
    {
        parent::__construct($id);
        $this->formula = $formula;
        $this->currency = $currency;
        $this->customerGroupId = $customerGroupId;
        $this->productConditionId = $productConditionId;
    }

    /**
     * @return string
     */
    public function getFormula(): string
    {
        return $this->formula;
    }

    /**
     * @param string $formula
     * @return AptoPriceFormula
     */
    public function setFormula(string $formula): AptoPriceFormula
    {
        $this->formula = $formula;
        return $this;
    }

    /**
     * @return Currency
     */
    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    /**
     * @param Currency $currency
     * @return AptoPriceFormula
     */
    public function setCurrency(Currency $currency): AptoPriceFormula
    {
        $this->currency = $currency;
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
     * @param AptoUuid $customerGroupId
     * @return AptoPriceFormula
     */
    public function setCustomerGroupId(AptoUuid $customerGroupId): AptoPriceFormula
    {
        $this->customerGroupId = $customerGroupId;
        return $this;
    }

    /**
     * @param AptoUuid $id
     * @return AptoPriceFormula
     */
    public function copy(AptoUuid $id): AptoPriceFormula
    {
        // create new price formula
        $priceFormula = new AptoPriceFormula(
            $id,
            $this->getFormula(),
            $this->getCurrency(),
            $this->getCustomerGroupId(),
            $this->getProductConditionId()
        );

        // return copy
        return $priceFormula;
    }

    /**
     * @return AptoUuid|null
     */
    public function getProductConditionId(): ?AptoUuid
    {
        return $this->productConditionId;
    }
}
