<?php

namespace Apto\Plugins\MaterialPickerElement\Application\Core\Query\PriceGroup;

use Apto\Base\Application\Core\QueryHandlerInterface;

class PriceGroupQueryHandler implements QueryHandlerInterface
{
    /**
     * @var PriceGroupFinder
     */
    protected $priceGroupFinder;

    /**
     * PriceGroupQueryHandler constructor.
     * @param PriceGroupFinder $priceGroupFinder
     */
    public function __construct(PriceGroupFinder $priceGroupFinder)
    {
        $this->priceGroupFinder = $priceGroupFinder;
    }

    /**
     * @param FindPriceGroups $query
     * @return array
     */
    public function handleFindPriceGroups(FindPriceGroups $query)
    {
        return $this->priceGroupFinder->findPriceGroups($query->getSearchString());
    }

    /**
     * @param FindPriceGroupsByPage $query
     * @return array
     */
    public function handleFindPriceGroupsByPage(FindPriceGroupsByPage $query)
    {
        return $this->priceGroupFinder->findByListingPageNumber($query->getPageNumber(), $query->getRecordsPerPage(), $query->getSearchString());
    }

    /**
     * @param FindPriceGroup $query
     * @return array|null
     */
    public function handleFindPriceGroup(FindPriceGroup $query)
    {
        return $this->priceGroupFinder->findById($query->getId());
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield FindPriceGroups::class => [
            'method' => 'handleFindPriceGroups',
            'aptoMessageName' => 'FindMaterialPickerPriceGroups',
            'bus' => 'query_bus'
        ];

        yield FindPriceGroupsByPage::class => [
            'method' => 'handleFindPriceGroupsByPage',
            'aptoMessageName' => 'FindMaterialPickerPriceGroupsByPage',
            'bus' => 'query_bus'
        ];

        yield FindPriceGroup::class => [
            'method' => 'handleFindPriceGroup',
            'aptoMessageName' => 'FindMaterialPickerPriceGroup',
            'bus' => 'query_bus'
        ];
    }
}