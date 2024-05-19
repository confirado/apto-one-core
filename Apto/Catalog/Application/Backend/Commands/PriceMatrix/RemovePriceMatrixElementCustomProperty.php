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
    private $id;

    /**
     * RemovePriceMatrixElementCustomProperty constructor.
     * @param string $priceMatrixId
     * @param string $priceMatrixElementId
     * @param string $id
     */
    public function __construct(string $priceMatrixId, string $priceMatrixElementId, string $id)
    {
        $this->priceMatrixId = $priceMatrixId;
        $this->priceMatrixElementId = $priceMatrixElementId;
        $this->id = $id;
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
    public function getId(): string
    {
        return $this->id;
    }
}
