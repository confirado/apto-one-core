<?php

namespace Apto\Catalog\Application\Backend\Commands\PriceMatrix;

use Apto\Base\Application\Core\CommandInterface;

class RemovePriceMatrixElement implements CommandInterface
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
     * AddPriceMatrixElement constructor.
     * @param string $priceMatrixId
     * @param string $priceMatrixElementId
     */
    public function __construct(string $priceMatrixId, string $priceMatrixElementId)
    {
        $this->priceMatrixId = $priceMatrixId;
        $this->priceMatrixElementId = $priceMatrixElementId;
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
}