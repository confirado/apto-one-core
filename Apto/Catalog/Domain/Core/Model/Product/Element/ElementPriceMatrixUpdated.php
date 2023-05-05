<?php

namespace Apto\Catalog\Domain\Core\Model\Product\Element;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class ElementPriceMatrixUpdated extends AbstractDomainEvent
{
    /**
     * @var bool
     */
    private $priceMatrixActive;

    /**
     * @var string|null
     */
    private $priceMatrixId;

    /**
     * @var string|null
     */
    private $priceMatrixRow;

    /**
     * @var string|null
     */
    private $priceMatrixColumn;

    /**
     * @param AptoUuid $id
     * @param bool $priceMatrixActive
     * @param string|null $priceMatrixId
     * @param string|null $priceMatrixRow
     * @param string|null $priceMatrixColumn
     */
    public function __construct(AptoUuid $id, bool $priceMatrixActive, ?string $priceMatrixId, ?string $priceMatrixRow, ?string $priceMatrixColumn)
    {
        parent::__construct($id);
        $this->priceMatrixActive = $priceMatrixActive;
        $this->priceMatrixId = $priceMatrixId;
        $this->priceMatrixRow = $priceMatrixRow;
        $this->priceMatrixColumn = $priceMatrixColumn;
    }

    /**
     * @return bool
     */
    public function isPriceMatrixActive(): bool
    {
        return $this->priceMatrixActive;
    }

    /**
     * @return string|null
     */
    public function getPriceMatrixId(): ?string
    {
        return $this->priceMatrixId;
    }

    /**
     * @return string|null
     */
    public function getPriceMatrixRow(): ?string
    {
        return $this->priceMatrixRow;
    }

    /**
     * @return string|null
     */
    public function getPriceMatrixColumn(): ?string
    {
        return $this->priceMatrixColumn;
    }
}