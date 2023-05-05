<?php

namespace Apto\Catalog\Domain\Core\Model\Product;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class ProductStockUpdated extends AbstractDomainEvent
{
    /**
     * @var int
     */
    private $stock;

    /**
     * ProductStockUpdated constructor.
     * @param AptoUuid $id
     * @param int $stock
     */
    public function __construct(AptoUuid $id, int $stock)
    {
        parent::__construct($id);
        $this->stock = $stock;
    }

    /**
     * @return int
     */
    public function getStock(): int
    {
        return $this->stock;
    }
}