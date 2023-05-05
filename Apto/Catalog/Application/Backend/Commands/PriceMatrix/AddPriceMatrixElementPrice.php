<?php

namespace Apto\Catalog\Application\Backend\Commands\PriceMatrix;

use Apto\Base\Application\Core\CommandInterface;

class AddPriceMatrixElementPrice implements CommandInterface
{
    /**
     * @var string
     */
    private $priceMatrixId;

    /**
     * @var string
     */
    private $priceMatrixElementId;

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
     * AddPriceMatrixElementPrice constructor.
     * @param string $priceMatrixId
     * @param string $priceMatrixElementId
     * @param mixed $amount
     * @param string $currency
     * @param string $customerGroupId
     */
    public function __construct(string $priceMatrixId, string $priceMatrixElementId, $amount, string $currency, string $customerGroupId)
    {
        $this->priceMatrixId = $priceMatrixId;
        $this->priceMatrixElementId = $priceMatrixElementId;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->customerGroupId = $customerGroupId;
    }

    /**
     * @return string
     */
    public function getPriceMatrixId(): string
    {
        return $this->priceMatrixId;
    }

    /**
     * @return string
     */
    public function getPriceMatrixElementId(): string
    {
        return $this->priceMatrixElementId;
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