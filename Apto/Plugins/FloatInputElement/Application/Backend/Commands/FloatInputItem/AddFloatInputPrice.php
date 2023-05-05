<?php

namespace Apto\Plugins\FloatInputElement\Application\Backend\Commands\FloatInputItem;

use Apto\Base\Application\Core\CommandInterface;

class AddFloatInputPrice implements CommandInterface
{
    /**
     * @var string
     */
    protected $productId;

    /**
     * @var string
     */
    protected $sectionId;

    /**
     * @var string
     */
    protected $elementId;

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
     * AddFloatInputPrice constructor.
     * @param string $productId
     * @param string $sectionId
     * @param string $elementId
     * @param $amount
     * @param string $currency
     * @param string $customerGroupId
     */
    public function __construct(
        string $productId,
        string $sectionId,
        string $elementId,
        $amount,
        string $currency,
        string $customerGroupId
    ) {
        $this->productId = $productId;
        $this->sectionId = $sectionId;
        $this->elementId = $elementId;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->customerGroupId = $customerGroupId;
    }

    /**
     * @return string
     */
    public function getProductId(): string
    {
        return $this->productId;
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