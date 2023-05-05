<?php

namespace Apto\Catalog\Application\Backend\Commands\PriceMatrix;

use Apto\Base\Application\Core\CommandInterface;

class AddPriceMatrixElement implements CommandInterface
{
    /**
     * @var string
     */
    private $priceMatrixId;

    /**
     * @var mixed
     */
    private $columnValue;

    /**
     * @var mixed
     */
    private $rowValue;

    /**
     * AddPriceMatrixElement constructor.
     * @param string $priceMatrixId
     * @param mixed $columnValue
     * @param mixed $rowValue
     */
    public function __construct(string $priceMatrixId, $columnValue, $rowValue)
    {
        $this->priceMatrixId = $priceMatrixId;
        $this->columnValue = $columnValue;
        $this->rowValue = $rowValue;
    }

    /**
     * @return string
     */
    public function getPriceMatrixId(): string
    {
        return $this->priceMatrixId;
    }

    /**
     * @return mixed
     */
    public function getColumnValue()
    {
        return $this->columnValue;
    }

    /**
     * @return mixed
     */
    public function getRowValue()
    {
        return $this->rowValue;
    }
}