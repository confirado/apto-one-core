<?php
namespace Apto\Catalog\Application\Backend\Commands\Product\Section;

use Apto\Catalog\Application\Backend\Commands\Product\ProductChildCommand;

class AddProductSectionPrice extends ProductChildCommand
{
    /**
     * @var string
     */
    private $sectionId;

    /**
     * @var mixed
     */
    private $amount;

    /**
     * @var string
     */
    private $currency;

    /**
     * @var string
     */
    private $customerGroupId;

    /**
     * @ todo define accepted type for amount
     * AddProductSectionPrice constructor.
     * @param string $productId
     * @param string $sectionId
     * @param mixed $amount
     * @param string $currency
     * @param string $customerGroupId
     */
    public function __construct(string $productId, string $sectionId, $amount, string $currency, string $customerGroupId)
    {
        parent::__construct($productId);
        $this->sectionId = $sectionId;
        $this->amount = $amount;
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
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
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