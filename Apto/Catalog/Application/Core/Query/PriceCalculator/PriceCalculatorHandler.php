<?php

namespace Apto\Catalog\Application\Core\Query\PriceCalculator;

use Apto\Base\Application\Core\QueryHandlerInterface;
use Apto\Catalog\Application\Core\Service\PriceCalculator\PriceCalculatorRegistry;

class PriceCalculatorHandler implements QueryHandlerInterface
{
    /**
     * @var PriceCalculatorRegistry
     */
    private $priceCalculatorRegistry;

    /**
     * PriceCalculatorHandler constructor.
     * @param PriceCalculatorRegistry $priceCalculatorRegistry
     */
    public function __construct(PriceCalculatorRegistry $priceCalculatorRegistry)
    {
        $this->priceCalculatorRegistry = $priceCalculatorRegistry;
    }

    /**
     * @param FindPriceCalculators $query
     * @return array
     */
    public function handleFindPriceCalculators(FindPriceCalculators $query): array
    {
        return $this->priceCalculatorRegistry->getPriceCalculatorList();
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield FindPriceCalculators::class => [
            'method' => 'handleFindPriceCalculators',
            'bus' => 'query_bus'
        ];
    }
}