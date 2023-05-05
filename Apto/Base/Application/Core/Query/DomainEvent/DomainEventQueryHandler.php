<?php

namespace Apto\Base\Application\Core\Query\DomainEvent;

use Apto\Base\Application\Core\QueryHandlerInterface;

class DomainEventQueryHandler implements QueryHandlerInterface
{
    /**
     * @var DomainEventFinder
     */
    protected $domainEventFinder;

    /**
     * DomainEventQueryHandler constructor.
     * @param DomainEventFinder $domainEventFinder
     */
    public function __construct(DomainEventFinder $domainEventFinder)
    {
        $this->domainEventFinder = $domainEventFinder;
    }

    /**
     * @param FindDomainEventLog $query
     * @return array
     */
    public function handleFindDomainEventLog(FindDomainEventLog $query)
    {
        return $this->domainEventFinder->findFilteredDomainEvents($query->getPageNumber(), $query->getRecordsPerPage(), $query->getSearchString(), $query->getFilter());
    }

    /**
     * @param FindGroupedTypeNames $query
     * @return array
     */
    public function handleFindGroupedTypeNames(FindGroupedTypeNames $query)
    {
        return $this->domainEventFinder->findGroupedTypeNames();
    }

    /**
     * @param FindGroupedUserIds $query
     * @return array
     */
    public function handleFindGroupedUserIds(FindGroupedUserIds $query)
    {
        return $this->domainEventFinder->findGroupedUserIds();
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield FindGroupedUserIds::class => [
            'method' => 'handleFindGroupedUserIds',
            'bus' => 'query_bus'
        ];

        yield FindGroupedTypeNames::class => [
            'method' => 'handleFindGroupedTypeNames',
            'bus' => 'query_bus'
        ];

        yield FindDomainEventLog::class => [
            'method' => 'handleFindDomainEventLog',
            'bus' => 'query_bus'
        ];
    }
}