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

    public function __construct(ProductFinder $productFinder)
    {
        $this->productFinder = $productFinder;
    }

    /**
     * @param FindConditions $query
     * @return array|null
     */
    public function handleFindConditions(FindConditions $query)
    {
        return $this->productFinder->findProductConditions($query->getProductId());
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield FindConditions::class => [
            'method' => 'handleFindConditions',
            'bus' => 'query_bus'
        ];
    }
}
