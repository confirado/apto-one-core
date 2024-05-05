<?php

namespace Apto\Catalog\Application\Core\Query\Product\Condition;

use Apto\Base\Application\Core\QueryHandlerInterface;
use Apto\Catalog\Application\Core\Query\Product\ProductFinder;

class ConditionQueryHandler implements QueryHandlerInterface
{
    /**
     * @var ProductFinder
     */
    private ProductFinder $productFinder;

    /**
     * @var ProductConditionSetFinder
     */
    private ProductConditionSetFinder $productConditionSetFinder;

    /**
     * @param ProductFinder $productFinder
     * @param ProductConditionSetFinder $productConditionSetFinder
     */
    public function __construct(ProductFinder $productFinder, ProductConditionSetFinder $productConditionSetFinder)
    {
        $this->productFinder = $productFinder;
        $this->productConditionSetFinder = $productConditionSetFinder;
    }

    /**
     * @param FindConditionSets $query
     * @return array|null
     */
    public function handleFindConditionSets(FindConditionSets $query): ?array
    {
        return $this->productFinder->findProductConditionSets($query->getProductId());
    }

    /**
     * @param FindConditionSet $query
     * @return array|null
     */
    public function handleFindConditionSet(FindConditionSet $query): ?array
    {
        return $this->productConditionSetFinder->findById($query->getConditionSetId());
    }

    /**
     * @param FindConditions $query
     * @return array|null
     */
    public function handleFindConditions(FindConditions $query): ?array
    {
        return $this->productFinder->findProductConditions($query->getProductId());
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield FindConditionSets::class => [
            'method' => 'handleFindConditionSets',
            'bus' => 'query_bus'
        ];

        yield FindConditionSet::class => [
            'method' => 'handleFindConditionSet',
            'bus' => 'query_bus'
        ];

        yield FindConditions::class => [
            'method' => 'handleFindConditions',
            'bus' => 'query_bus'
        ];
    }
}
