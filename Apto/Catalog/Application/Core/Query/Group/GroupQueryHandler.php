<?php

namespace Apto\Catalog\Application\Core\Query\Group;

use Apto\Base\Application\Core\QueryHandlerInterface;

class GroupQueryHandler implements QueryHandlerInterface
{
    /**
     * @var GroupFinder
     */
    protected $groupFinder;

    /**
     * CategoryQueryHandler constructor.
     * @param GroupFinder $groupFinder
     */
    public function __construct(GroupFinder $groupFinder)
    {
        $this->groupFinder = $groupFinder;
    }

    /**
     * @param FindGroups $query
     * @return array
     */
    public function handleFindGroups(FindGroups $query)
    {
        return $this->groupFinder->findGroups($query->getSearchString());
    }

    /**
     * @param FindGroup $query
     * @return array|null
     */
    public function handleFindGroup(FindGroup $query)
    {
        return $this->groupFinder->findById($query->getId());
    }

    /**
     * @param FindGroupByIdentifier $query
     * @return array|null
     */
    public function handleFindGroupByIdentifier(FindGroupByIdentifier $query)
    {
        return $this->groupFinder->findByIdentifier($query->getIdentifier());
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield FindGroupByIdentifier::class => [
            'method' => 'handleFindGroupByIdentifier',
            'bus' => 'query_bus'
        ];

        yield FindGroup::class => [
            'method' => 'handleFindGroup',
            'bus' => 'query_bus'
        ];

        yield FindGroups::class => [
            'method' => 'handleFindGroups',
            'bus' => 'query_bus'
        ];
    }
}