<?php

namespace Apto\Catalog\Domain\Core\Model\Product\Element;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class ElementExtendedPriceCalculationFormulaUpdated extends AbstractDomainEvent
{
    /**
     * @var string
     */
    private $extendedPriceCalculationFormula;

    /**
     * @param AptoUuid $id
     * @param string $extendedPriceCalculationFormula
     */
    public function __construct(AptoUuid $id, string $extendedPriceCalculationFormula)
    {
        parent::__construct($id);
        $this->extendedPriceCalculationFormula = $extendedPriceCalculationFormula;
    }

    /**
     * @return string
     */
    public function getExtendedPriceCalculationFormula(): string
    {
        return $this->extendedPriceCalculationFormula;
    }
}