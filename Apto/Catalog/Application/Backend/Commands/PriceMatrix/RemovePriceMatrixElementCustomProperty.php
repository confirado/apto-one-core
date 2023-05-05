<?php

namespace Apto\Catalog\Application\Backend\Commands\PriceMatrix;

use Apto\Base\Application\Core\CommandInterface;

class RemovePriceMatrixElementCustomProperty implements CommandInterface
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
    private $key;

    /**
     * RemovePriceMatrixElementCustomProperty constructor.
     * @param string $priceMatrixId
     * @param string $priceMatrixElementId
     * @param string $key
     */
    public function __construct(string $priceMatrixId, string $priceMatrixElementId, string $key)
    {
        $this->priceMatrixId = $priceMatrixId;
        $this->priceMatrixElementId = $priceMatrixElementId;
        $this->key = $key;
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
    public function getKey(): string
    {
        return $this->key;
    }
}