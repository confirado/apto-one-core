<?php

namespace Apto\Catalog\Domain\Core\Model\Product;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class ProductComputedProductValuesUpdated extends AbstractDomainEvent
{
    /**
     * @var array
     */
    private $computedProductValues;

    /**
     * CategoryNameUpdated constructor.
     * @param AptoUuid $id
     * @param array $computedProductValues
     */
    public function __construct(AptoUuid $id, array $computedProductValues)
    {
        parent::__construct($id);
        $this->computedProductValues = $computedProductValues;
    }

    /**
     * @return array
     */
    public function getComputedProductValues(): array
    {
        return $this->computedProductValues;
    }
}