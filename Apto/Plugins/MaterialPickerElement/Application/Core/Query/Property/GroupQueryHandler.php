<?php

namespace Apto\Plugins\MaterialPickerElement\Application\Core\Query\Property;

use Apto\Base\Application\Core\QueryHandlerInterface;

class GroupQueryHandler implements QueryHandlerInterface
{
    /**
     * @var GroupFinder
     */
    protected $groupFinder;

    /**
     * GroupQueryHandler constructor.
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
     * @param FindGroupsByPage $query
     * @return array
     */
    public function handleFindGroupsByPage(FindGroupsByPage $query)
    {
        return $this->groupFinder->findByListingPageNumber($query->getPageNumber(), $query->getRecordsPerPage(), $query->getSearchString());
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
     * @param FindGroupProperties $query
     * @return array
     */
    public function handleFindGroupProperties(FindGroupProperties $query)
    {
        return $this->groupFinder->findGroupProperties($query->getId(), $query->getSearchString());
    }

    /**
     * @param FindProperty $query
     * @return array|null
     */
    public function handleFindProperty(FindProperty $query)
    {
        return $this->groupFinder->findPropertyById($query->getId());
    }

    /**
     * @param FindPropertyCustomProperties $query
     * @return array
     */
    public function handleFindPropertyCustomProperties(FindPropertyCustomProperties $query)
    {
        return $this->groupFinder->findPropertyCustomProperties($query->getId());
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield FindGroups::class => [
            'method' => 'handleFindGroups',
            'aptoMessageName' => 'FindMaterialPickerGroups',
            'bus' => 'query_bus'
        ];

        yield FindGroupsByPage::class => [
            'method' => 'handleFindGroupsByPage',
            'aptoMessageName' => 'FindMaterialPickerGroupsByPage',
            'bus' => 'query_bus'
        ];

        yield FindGroup::class => [
            'method' => 'handleFindGroup',
            'aptoMessageName' => 'FindMaterialPickerGroup',
            'bus' => 'query_bus'
        ];

        yield FindGroupProperties::class => [
            'method' => 'handleFindGroupProperties',
            'aptoMessageName' => 'FindMaterialPickerGroupProperties',
            'bus' => 'query_bus'
        ];

        yield FindProperty::class => [
            'method' => 'handleFindProperty',
            'aptoMessageName' => 'FindMaterialPickerProperty',
            'bus' => 'query_bus'
        ];

        yield FindPropertyCustomProperties::class => [
            'method' => 'handleFindPropertyCustomProperties',
            'aptoMessageName' => 'FindMaterialPickerPropertyCustomProperties',
            'bus' => 'query_bus'
        ];
    }
}