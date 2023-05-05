<?php

namespace Apto\Base\Application\Core\Query\Customer;

use Apto\Base\Application\Core\QueryHandlerInterface;

class CustomerQueryHandler implements QueryHandlerInterface
{
    /**
     * @var CustomerFinder
     */
    private $customerFinder;

    /**
     * CustomerQueryHandler constructor.
     * @param CustomerFinder $customerFinder
     */
    public function __construct(CustomerFinder $customerFinder)
    {
        $this->customerFinder = $customerFinder;
    }

    /**
     * @param FindCustomer $query
     * @return array
     */
    public function handleFindCustomer(FindCustomer $query)
    {
        return $this->customerFinder->findById($query->getId());
    }

    /**
     * @param FindCustomerByUsername $query
     * @return array
     */
    public function handleFindCustomerByUsername(FindCustomerByUsername $query)
    {
        return $this->customerFinder->findByUsername($query->getUsername());
    }

    /**
     * @param FindCustomerByEmail $query
     * @return array
     */
    public function handleFindCustomerByEmail(FindCustomerByEmail $query)
    {
        return $this->customerFinder->findByEmail($query->getEmail());
    }

    /**
     * @param FindCustomerByShopAndExternalId $query
     * @return array
     */
    public function handleFindCustomerByShopAndExternalId(FindCustomerByShopAndExternalId $query)
    {
        return $this->customerFinder->findByShopAndExternalId($query->getShopId(), $query->getExternalId());
    }

    /**
     * @param FindCustomers $query
     * @return array
     */
    public function handleFindCustomers(FindCustomers $query)
    {
        return $this->customerFinder->findCustomers($query->getSearchString());
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield FindCustomer::class => [
            'method' => 'handleFindCustomer',
            'bus' => 'query_bus'
        ];

        yield FindCustomerByUsername::class => [
            'method' => 'handleFindCustomerByUsername',
            'bus' => 'query_bus'
        ];

        yield FindCustomerByEmail::class => [
            'method' => 'handleFindCustomerByEmail',
            'bus' => 'query_bus'
        ];

        yield FindCustomerByShopAndExternalId::class => [
            'method' => 'handleFindCustomerByShopAndExternalId',
            'bus' => 'query_bus'
        ];

        yield FindCustomers::class => [
            'method' => 'handleFindCustomers',
            'bus' => 'query_bus'
        ];
    }
}