<?php

namespace Apto\Base\Application\Core\Query\CustomerGroup;

use Apto\Base\Application\Core\QueryHandlerInterface;

class CustomerGroupQueryHandler implements QueryHandlerInterface
{
    /**
     * @var CustomerGroupFinder
     */
    private $customerGroupFinder;

    /**
     * CustomerQueryHandler constructor.
     * @param CustomerGroupFinder $customerGroupFinder
     */
    public function __construct(CustomerGroupFinder $customerGroupFinder)
    {
        $this->customerGroupFinder = $customerGroupFinder;
    }

    /**
     * @param FindCustomerGroup $query
     * @return array
     */
    public function handleFindCustomerGroup(FindCustomerGroup $query)
    {
        return $this->customerGroupFinder->findById($query->getId());
    }

    /**
     * @param FindCustomerGroupByName $query
     * @return array
     */
    public function handleFindCustomerGroupByName(FindCustomerGroupByName $query)
    {
        return $this->customerGroupFinder->findByName($query->getName());
    }

    /**
     * @param FindCustomerGroupByShopAndExternalId $query
     * @return array
     */
    public function handleFindCustomerGroupByShopAndExternalId(FindCustomerGroupByShopAndExternalId $query)
    {
        return $this->customerGroupFinder->findByShopAndExternalId($query->getShopId(), $query->getExternalId());
    }

    /**
     * @param FindCustomerGroups $query
     * @return array
     */
    public function handleFindCustomerGroups(FindCustomerGroups $query)
    {
        return $this->customerGroupFinder->findCustomerGroups($query->getSearchString());
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield FindCustomerGroups::class => [
            'method' => 'handleFindCustomerGroups',
            'bus' => 'query_bus'
        ];

        yield FindCustomerGroupByShopAndExternalId::class => [
            'method' => 'handleFindCustomerGroupByShopAndExternalId',
            'bus' => 'query_bus'
        ];

        yield FindCustomerGroupByName::class => [
            'method' => 'handleFindCustomerGroupByName',
            'bus' => 'query_bus'
        ];

        yield FindCustomerGroup::class => [
            'method' => 'handleFindCustomerGroup',
            'bus' => 'query_bus'
        ];
    }
}