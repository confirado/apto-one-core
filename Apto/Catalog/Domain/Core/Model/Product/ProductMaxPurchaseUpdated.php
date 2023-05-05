<?php

namespace Apto\Catalog\Domain\Core\Model\Product;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class ProductMaxPurchaseUpdated extends AbstractDomainEvent
{
    /**
     * @var int
     */
    private $maxPurchase;

    /**
     * ProductMaxPurchaseUpdated constructor.
     * @param AptoUuid $id
     * @param int $maxPurchase
     */
    public function __construct(AptoUuid $id, int $maxPurchase)
    {
        parent::__construct($id);
        $this->maxPurchase = $maxPurchase;
    }

    /**
     * @return int
     */
    public function getMaxPurchase(): int
    {
        return $this->maxPurchase;
    }
}