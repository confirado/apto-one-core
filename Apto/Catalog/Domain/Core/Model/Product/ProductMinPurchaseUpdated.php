<?php

namespace Apto\Catalog\Domain\Core\Model\Product;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class ProductMinPurchaseUpdated extends AbstractDomainEvent
{
    /**
     * @var int
     */
    private $minPurchase;

    /**
     * ProductMinPurchaseUpdated constructor.
     * @param AptoUuid $id
     * @param int $minPurchase
     */
    public function __construct(AptoUuid $id, int $minPurchase)
    {
        parent::__construct($id);
        $this->minPurchase = $minPurchase;
    }

    /**
     * @return int
     */
    public function getMinPurchase(): int
    {
        return $this->minPurchase;
    }
}