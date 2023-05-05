<?php

namespace Apto\Catalog\Domain\Core\Model\Product;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class ProductTaxRateUpdated extends AbstractDomainEvent
{
    /**
     * @var float
     */
    private $taxRate;

    /**
     * ProductTaxRateUpdated constructor.
     * @param AptoUuid $id
     * @param float $taxRate
     */
    public function __construct(AptoUuid $id, float $taxRate)
    {
        parent::__construct($id);
        $this->taxRate = $taxRate;
    }

    /**
     * @return float
     */
    public function getTaxRate(): float
    {
        return $this->taxRate;
    }
}