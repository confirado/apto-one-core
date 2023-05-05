<?php

namespace Apto\Plugins\PricePerUnitElement\Application\Core\Query\PricePerUnitItem;

use Apto\Base\Application\Core\QueryHandlerInterface;

class PricePerUnitItemQueryHandler implements QueryHandlerInterface
{
    /**
     * @var PricePerUnitItemFinder
     */
    protected $pricePerUnitItemFinder;

    /**
     * PricePerUnitItemQueryHandler constructor.
     * @param PricePerUnitItemFinder $pricePerUnitItemFinder
     */
    public function __construct(PricePerUnitItemFinder $pricePerUnitItemFinder)
    {
        $this->pricePerUnitItemFinder = $pricePerUnitItemFinder;
    }

    /**
     * @param FindPricePerUnitPrices $query
     * @return array
     */
    public function handleFindPricePerUnitPrices(FindPricePerUnitPrices $query)
    {
        return $this->pricePerUnitItemFinder->findPrices($query->getId());
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield FindPricePerUnitPrices::class => [
            'method' => 'handleFindPricePerUnitPrices',
            'bus' => 'query_bus'
        ];
    }
}