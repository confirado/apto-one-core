<?php

namespace Apto\Catalog\Application\Core\Query\Product\Condition;

use Apto\Base\Application\Core\QueryHandlerInterface;

class ProductConditionHandler implements QueryHandlerInterface
{
    /**
     * @param FindProductConditions $query
     * @return array
     */
    public function handleFindProductConditions(FindProductConditions $query): array
    {
        return [];
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield FindProductConditions::class => [
            'method' => 'handleFindProductConditions',
            'bus' => 'query_bus'
        ];
    }
}
