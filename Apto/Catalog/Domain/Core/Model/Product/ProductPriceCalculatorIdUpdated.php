<?php

namespace Apto\Catalog\Domain\Core\Model\Product;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class ProductPriceCalculatorIdUpdated extends AbstractDomainEvent
{
    /**
     * @var string
     */
    private $priceCalculatorId;

    /**
     * ProductPriceCalculatorIdUpdated constructor.
     * @param AptoUuid $id
     * @param string $priceCalculatorId
     */
    public function __construct(AptoUuid $id, string $priceCalculatorId)
    {
        parent::__construct($id);
        $this->priceCalculatorId = $priceCalculatorId;
    }

    /**
     * @return string
     */
    public function getPriceCalculatorId(): string
    {
        return $this->priceCalculatorId;
    }
}