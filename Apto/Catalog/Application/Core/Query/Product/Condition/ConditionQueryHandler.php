<?php

namespace Apto\Catalog\Application\Core\Query\Product\Condition;

use Apto\Base\Application\Core\QueryHandlerInterface;

class ConditionQueryHandler implements QueryHandlerInterface
{
    /**
     * @var
     */
    private $productConditionFinder;

    /**
     * @param ProductConditionFinder $productConditionFinder
     */
    public function __construct(ProductConditionFinder $productConditionFinder)
    {
        $this->productConditionFinder = $productConditionFinder;
    }

    /**
     * @param FindConditions $query
     * @return array|null
     */
    public function handleFindConditions(FindConditions $query)
    {
        return $this->productConditionFinder->findConditions($query->getProductId());
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
