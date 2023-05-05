<?php

namespace Apto\Catalog\Application\Backend\Commands\Product;

class UpdateProductPrice extends AbstractAddProductPrice
{
    /**
     * @var string
     */
    private $priceId;

    /**
     * @ todo define accepted type for amount
     * UpdateProductPrice constructor.
     * @param string $id
     * @param string $priceId
     * @param mixed $amount
     * @param string $currency
     * @param string $customerGroupId
     */
    public function __construct(string $id, string $priceId, $amount, string $currency, string $customerGroupId)
    {
        parent::__construct($id, $amount, $currency, $customerGroupId);
        $this->priceId = $priceId;
    }

    /**
     * @return string
     */
    public function getPriceId(): string
    {
        return $this->priceId;
    }
}