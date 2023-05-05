<?php

namespace Apto\Base\Application\Core\Query\DomainEvent;

use Apto\Base\Application\Core\Query\AptoFinder;

interface DomainEventFinder extends AptoFinder
{
    /**
     * @param int $pageNumber
     * @param int $recordsPerPage
     * @param string $searchString
     * @param array $filter
     * @return array
     */
    public function findFilteredDomainEvents(int $pageNumber = 1, int $recordsPerPage = 50, string $searchString = '', array $filter = []);

    /**
     * @return array
     */
    public function findGroupedTypeNames();

    /**
     * @return array
     */
    public function findGroupedUserIds();
}

