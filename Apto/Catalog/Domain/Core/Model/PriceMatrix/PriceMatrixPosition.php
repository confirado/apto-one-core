<?php
namespace Apto\Catalog\Domain\Core\Model\PriceMatrix;

class PriceMatrixPosition implements \JsonSerializable
{
    /**
     * @var float
     */
    private $columnValue;

    /**
     * @var float
     */
    private $rowValue;

    /**
     * PriceMatrix constructor.
     * @param mixed $columnValue
     * @param mixed $rowValue
     */
    public function __construct($columnValue, $rowValue)
    {
        if (!is_numeric($columnValue)) {
            throw new \InvalidArgumentException('ColumnValue: \'' . $columnValue . '\' is not a valid numeric value.');
        }

        if (!is_numeric($rowValue)) {
            throw new \InvalidArgumentException('RowValue: \'' . $rowValue . '\' is not a valid numeric value.');
        }

        $this->columnValue = floatval($columnValue);
        $this->rowValue = floatval($rowValue);
    }

    /**
     * @return float
     */
    public function getColumnValue(): float
    {
        return $this->columnValue;
    }

    /**
     * @return float
     */
    public function getRowValue(): float
    {
        return $this->rowValue;
    }

    /**
     * @param PriceMatrixPosition $priceMatrixPosition
     * @return bool
     */
    public function equals(PriceMatrixPosition $priceMatrixPosition)
    {
        if (
            $this->columnValue === $priceMatrixPosition->getColumnValue() &&
            $this->rowValue === $priceMatrixPosition->getRowValue()
        ) {
            return true;
        }
        return false;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->__toArray();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return json_encode($this->__toArray());
    }

    /**
     * @return array
     */
    public function __toArray()
    {
        return [
            'columnValue' => $this->getColumnValue(),
            'rowValue' => $this->getRowValue()
        ];
    }
}