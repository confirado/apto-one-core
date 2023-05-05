<?php

namespace Apto\Catalog\Application\Backend\Commands\PriceMatrix;

use Apto\Base\Application\Core\CommandInterface;

class RemovePriceMatrixElementPrice implements CommandInterface
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
     * @var string
     */
    private $priceMatrixElementPriceId;

    /**
     * AddPriceMatrixElement constructor.
     * @param string $priceMatrixId
     * @param string $priceMatrixElementId
     * @param string $priceMatrixElementPriceId
     */
    public function __construct(string $priceMatrixId, string $priceMatrixElementId, string $priceMatrixElementPriceId)
    {
        $this->priceMatrixId = $priceMatrixId;
        $this->priceMatrixElementId = $priceMatrixElementId;
        $this->priceMatrixElementPriceId = $priceMatrixElementPriceId;
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
     * @return string
     */
    public function getPriceMatrixElementPriceId(): string
    {
        return $this->priceMatrixElementPriceId;
    }
}