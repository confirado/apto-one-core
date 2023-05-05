<?php

namespace Apto\Catalog\Application\Core\Query\Filter;

use Apto\Base\Application\Core\QueryHandlerInterface;

class FilterPropertyQueryHandler implements QueryHandlerInterface
{
    /**
     * @var FilterPropertyFinder
     */
    protected $filterPropertyFinder;

    /**
     * FilterPropertyQueryHandler constructor.
     * @param FilterPropertyFinder $filterPropertyFinder
     */
    public function __construct(FilterPropertyFinder $filterPropertyFinder)
    {
        $this->filterPropertyFinder = $filterPropertyFinder;
    }

    /**
     * @param FindFilterProperties $query
     * @return array
     */
    public function handleFindFilterProperties(FindFilterProperties $query)
    {
        return $this->filterPropertyFinder->findFilterProperties($query->getSearchString());
    }

    /**
     * @param FindFilterProperty $query
     * @return array|null
     */
    public function handleFindFilterProperty(FindFilterProperty $query)
    {
        return $this->filterPropertyFinder->findById($query->getId());
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield FindFilterProperty::class => [
            'method' => 'handleFindFilterProperty',
            'bus' => 'query_bus'
        ];

        yield FindFilterProperties::class => [
            'method' => 'handleFindFilterProperties',
            'bus' => 'query_bus'
        ];
    }
}