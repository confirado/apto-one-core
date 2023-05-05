<?php

namespace Apto\Catalog\Domain\Core\Model\Product;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class ProductWeightUpdated extends AbstractDomainEvent
{
    /**
     * @var float
     */
    private $weight;

    /**
     * ProductWeightUpdated constructor.
     * @param AptoUuid $id
     * @param float $weight
     */
    public function __construct(AptoUuid $id, float $weight)
    {
        parent::__construct($id);
        $this->weight = $weight;
    }

    /**
     * @return float
     */
    public function getWeight(): float
    {
        return $this->weight;
    }
}