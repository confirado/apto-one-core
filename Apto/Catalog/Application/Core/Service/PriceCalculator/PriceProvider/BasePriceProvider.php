<?php

namespace Apto\Catalog\Application\Core\Service\PriceCalculator\PriceProvider;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Catalog\Application\Core\Service\PriceCalculator\PriceCalculator;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefinition;
use Money\Money;

interface BasePriceProvider extends PriceProvider
{
    /**
     * @param PriceCalculator $priceCalculator
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param ElementDefinition $elementDefinition
     * @param Money $elementPrice
     * @return Money
     */
    public function getPrice(
        PriceCalculator $priceCalculator,
        AptoUuid $sectionId,
        AptoUuid $elementId,
        ElementDefinition $elementDefinition,
        Money $elementPrice
    ): Money;
}