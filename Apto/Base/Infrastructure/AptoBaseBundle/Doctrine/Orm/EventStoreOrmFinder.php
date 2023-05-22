<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm;

use Apto\Base\Application\Core\Query\DomainEvent\DomainEventFinder;

class EventStoreOrmFinder extends AptoOrmFinder implements DomainEventFinder
{
    const ENTITY_CLASS = 'Apto\Base\Domain\Core\Model\DomainEvent\StoredEvent';

    /**
     * @param int $pageNumber
     * @param int $recordsPerPage
     * @param string $searchString
     * @param array $filter
     * @return array
     */
    public function findFilteredDomainEvents(int $pageNumber = 1, int $recordsPerPage = 50, string $searchString = '', array $filter = [])
    {
        $messageNamePostProcess = function($value) {
            return substr($value, strripos($value, '\\') + 1);
        };

        $builder = new DqlPaginatorBuilder($this->entityClass, 'd', 'eventId');
        $builder
            ->setPage($pageNumber)
            ->setRecordsPerPage($recordsPerPage)
            ->setValues([
                'd' => [
                    'eventId',
                    'eventBody',
                    'occurredOn',
                    'typeName',
                    ['typeName', 'messageName'],
                    'userId'
                ]
            ])
            ->setSearch([
                'd' => [
                    'eventBody'
                ]
            ], $searchString)
            ->setPostProcess([
                'd' => [
                    'eventBody' => [DqlQueryBuilder::class, 'decodeJson'],
                    'messageName' => $messageNamePostProcess
                ]
            ])
            ->setOrderBy([
                ['d.eventId', 'DESC']
            ]);

        $filterWhere = $this->getFilterWhere($filter);
        if ($filterWhere[0] != '') {
            $builder->setWhere($filterWhere[0], $filterWhere[1]);
        }

        return $builder->getResult($this->entityManager);
    }

    /**
     * @return array
     */
    public function findGroupedTypeNames()
    {
        $dql = 'SELECT d.typeName FROM ' . $this->entityClass . ' d GROUP BY d.typeName';
        $query = $this->entityManager->createQuery($dql);
        $typeNames = [];

        foreach ($query->getScalarResult() as $typeName) {
            $typeNames[] = [
                'typeName' => $typeName['typeName'],
                'messageName' => substr($typeName['typeName'], strripos($typeName['typeName'], '\\') + 1)
            ];
        }

        return $typeNames;
    }

    /**
     * @return array
     */
    public function findGroupedUserIds()
    {
        $dql = 'SELECT d.userId FROM ' . $this->entityClass . ' d WHERE d.userId IS NOT NULL GROUP BY d.userId';
        $query = $this->entityManager->createQuery($dql);
        return $query->getScalarResult();
    }

    /**
     * @param array $filter
     * @return array
     */
    private function getFilterWhere($filter)
    {
        $filterWhere = '';
        $filterParams = [];
        $hasTypeNames = false;
        $hasUserIds = false;

        if (isset($filter['typeNames']) && count($filter['typeNames']) > 0) {
            $typeNameCount = 0;
            foreach ($filter['typeNames'] as $typeName => $isFilter) {
                if ($isFilter) {
                    $filterWhere .= (false === $hasTypeNames ? '(' : ' OR ') . 'd.typeName = :d_typeName_' . $typeNameCount;
                    $filterParams['d_typeName_' . $typeNameCount] = $typeName;
                    $hasTypeNames = true;
                    $typeNameCount++;
                }
            }
            if (true === $hasTypeNames) {
                $filterWhere .= ')';
            }
        }

        if (isset($filter['userIds']) && count($filter['userIds']) > 0) {
            $userIdCount = 0;
            foreach ($filter['userIds'] as $userId => $isFilter) {
                if ($isFilter) {
                    $filterWhere .= (false === $hasUserIds && true === $hasTypeNames) ? ' AND ' : '';
                    $filterWhere .= (false === $hasUserIds ? '(' : ' OR ') . 'd.userId = :d_userId_' . $userIdCount;
                    $filterParams['d_userId_' . $userIdCount] = $userId;
                    $userIdCount++;
                    $hasUserIds = true;
                }
            }
            if (true === $hasUserIds) {
                $filterWhere .= ')';
            }
        }

        if (isset($filter['fromDate']) && $filter['fromDate'] !== null) {
            $filterWhere .= ($filterWhere == '' ? '' : ' AND ') . 'd.occurredOn >= :d_fromDate';
            $filterParams['d_fromDate'] = $filter['fromDate'] . ' 00:00:00';
        }

        if (isset($filter['toDate']) && $filter['toDate'] !== null) {
            $filterWhere .= ($filterWhere == '' ? '' : ' AND ') . 'd.occurredOn <= :d_toDate';
            $filterParams['d_toDate'] = $filter['toDate'] . ' 23:59:59';
        }

        return [
            $filterWhere,
            $filterParams
        ];
    }
}
