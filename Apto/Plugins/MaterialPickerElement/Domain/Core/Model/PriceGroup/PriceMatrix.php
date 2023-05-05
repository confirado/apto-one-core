<?php

namespace Apto\Plugins\MaterialPickerElement\Domain\Core\Model\PriceGroup;

use Ramsey\Uuid\Uuid;

class PriceMatrix
{
    /**
     * @var string|null
     */
    private $id;

    /**
     * @var string|null
     */
    private $row;

    /**
     * @var string|null
     */
    private $column;

    /**
     * @var string|null
     */
    private $pricePostProcess;

    /**
     * @param array $priceMatrix
     * @return PriceMatrix
     */
    public static function fromArray(array $priceMatrix): PriceMatrix
    {
        if (!array_key_exists('id', $priceMatrix) || !array_key_exists('row', $priceMatrix) || !array_key_exists('column', $priceMatrix)) {
            throw new \InvalidArgumentException('Invalid value for id, row or column given.');
        }

        return new self($priceMatrix['id'], $priceMatrix['row'], $priceMatrix['column'], $priceMatrix['pricePostProcess']);
    }

    /**
     * PriceMatrix constructor.
     * @param string|null $id
     * @param string|null $row
     * @param string|null $column
     * @param string|null $pricePostProcess
     */
    public function __construct(?string $id = null, ?string $row = null, ?string $column = null, ?string $pricePostProcess = null)
    {
        $this->assertValidValues($id, $row, $column);
        $this->id = $id;
        $this->row = $row;
        $this->column = $column;
        $this->pricePostProcess = $pricePostProcess;
    }

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getRow(): ?string
    {
        return $this->row;
    }

    /**
     * @return string|null
     */
    public function getColumn(): ?string
    {
        return $this->column;
    }

    /**
     * @return string|null
     */
    public function getPricePostProcess(): ?string
    {
        return $this->pricePostProcess;
    }

    /**
     * @param PriceMatrix $priceMatrix
     * @return bool
     */
    public function equals(PriceMatrix $priceMatrix): bool
    {
        // todo pricePostProcess should be considered here?

        if (
            $priceMatrix->getId() === $this->getId() &&
            $priceMatrix->getRow() === $this->getRow() &&
            $priceMatrix->getColumn() === $this->getColumn() &&
            $priceMatrix->getPricePostProcess() === $this->getPricePostProcess()
        ) {
            return true;
        }
        return false;
    }

    /**
     * @param string|null $id
     * @param string|null $row
     * @param string|null $column
     */
    private function assertValidValues(?string $id, ?string $row, ?string $column)
    {
        if (!$id) {
            return;
        }

        if (!Uuid::isValid($id)) {
            throw new \InvalidArgumentException('No valid id given.');
        }

        if (null === $row || '' === $row) {
            throw new \InvalidArgumentException('No valid row value given.');
        }

        if (null === $column || '' === $column) {
            throw new \InvalidArgumentException('No valid column value given.');
        }
    }
}
