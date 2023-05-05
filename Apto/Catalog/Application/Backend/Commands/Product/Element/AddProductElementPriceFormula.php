<?php
namespace Apto\Catalog\Application\Backend\Commands\Product\Element;

use Apto\Catalog\Application\Backend\Commands\Product\ProductChildCommand;

class AddProductElementPriceFormula extends ProductChildCommand
{
    /**
     * @var string
     */
    private $sectionId;

    /**
     * @var string
     */
    private $elementId;

    /**
     * @var string
     */
    private $formula;

    /**
     * @var string
     */
    private $currency;

    /**
     * @var string
     */
    private $customerGroupId;

    /**
     * AddProductPriceFormula constructor.
     * @param string $productId
     * @param string $sectionId
     * @param string $elementId
     * @param string $formula
     * @param string $currency
     * @param string $customerGroupId
     */
    public function __construct(string $productId, string $sectionId, string $elementId, string $formula, string $currency, string $customerGroupId)
    {
        parent::__construct($productId);
        $this->sectionId = $sectionId;
        $this->elementId = $elementId;
        $this->formula = $formula;
        $this->currency = $currency;
        $this->customerGroupId = $customerGroupId;
    }

    /**
     * @return string
     */
    public function getSectionId(): string
    {
        return $this->sectionId;
    }

    /**
     * @return string
     */
    public function getElementId(): string
    {
        return $this->elementId;
    }

    /**
     * @return string
     */
    public function getFormula()
    {
        return $this->formula;
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
    public function getCustomerGroupId(): string
    {
        return $this->customerGroupId;
    }
}