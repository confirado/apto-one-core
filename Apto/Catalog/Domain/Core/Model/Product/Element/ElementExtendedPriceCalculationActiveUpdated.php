<?php

namespace Apto\Catalog\Domain\Core\Model\Product\Element;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class ElementExtendedPriceCalculationActiveUpdated extends AbstractDomainEvent
{
    /**
     * @var bool
     */
    private $extendedPriceCalculationActive;

    /**
     * @param AptoUuid $id
     * @param bool $extendedPriceCalculationActive
     */
    public function __construct(AptoUuid $id, bool $extendedPriceCalculationActive)
    {
        parent::__construct($id);
        $this->extendedPriceCalculationActive = $extendedPriceCalculationActive;
    }

    /**
     * @return bool
     */
    public function getExtendedPriceCalculationActive(): bool
    {
        return $this->extendedPriceCalculationActive;
    }
}