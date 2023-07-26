<?php

namespace Apto\Plugins\PartsList\Application\Core\Query\Unit;

use Apto\Base\Application\Core\QueryHandlerInterface;

class UnitQueryHandler implements QueryHandlerInterface
{
    /**
     * @var UnitFinder
     */
    private $partFinder;

    /**
     * UnitQueryHandler constructor.
     * @param UnitFinder $partFinder
     */
    public function __construct(UnitFinder $partFinder)
    {
        $this->partFinder = $partFinder;
    }

    /**
     * @param FindUnit $query
     * @return array|null
     */
    public function handleFindUnit(FindUnit $query)
    {
        return $this->partFinder->findById($query->getId());
    }

    /**
     * @param FindUnits $query
     * @return array
     */
    public function handleFindUnits(FindUnits $query): array
    {
        return $this->partFinder->findByListingPageNumber(
            $query->getPageNumber(),
            $query->getRecordsPerPage(),
            $query->getSearchString()
        );
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield FindUnit::class => [
            'method' => 'handleFindUnit',
            'aptoMessagePrefix' => 'AptoPartsList',
            'bus' => 'query_bus'
        ];

        yield FindUnits::class => [
            'method' => 'handleFindUnits',
            'aptoMessagePrefix' => 'AptoPartsList',
            'bus' => 'query_bus'
        ];
    }
}