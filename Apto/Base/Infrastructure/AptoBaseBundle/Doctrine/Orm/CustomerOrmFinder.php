<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm;

use Apto\Base\Application\Core\Query\Customer\CustomerFinder;
use Apto\Base\Domain\Core\Model\Customer\Customer;

class CustomerOrmFinder extends AptoOrmFinder implements CustomerFinder
{
    const ENTITY_CLASS = Customer::class;

    /**
     * @param string $property
     * @param string $value
     * @return array|null
     */
    protected function findByProperty(string $property, string $value)
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->findByProperty($property, $value)
            ->setValues([
                'c' => [
                    ['id.id', 'id'],
                    ['username.username', 'username'],
                    'email',
                    'firstName',
                    'lastName',
                    ['gender.gender', 'gender'],
                    'externalId',
                    'active',
                    'created'
                ]
            ])
            ->setPostProcess([
                'c' => [
                    'active' => [DqlQueryBuilder::class, 'decodeBool']
                ]
            ]);

        return $builder->getSingleResultOrNull($this->entityManager);
    }

    /**
     * @param string $id
     * @return array
     */
    public function findById(string $id)
    {
        return $this->findByProperty('id.id', $id);
    }

    /**
     * @param string $username
     * @return array
     */
    public function findByUsername(string $username)
    {
        return $this->findByProperty('username.username', $username);
    }

    /**
     * @param string $email
     * @return array
     */
    public function findByEmail(string $email)
    {
        return $this->findByProperty('email', $email);
    }

    /**
     * @param string $shopId
     * @param string $externalId
     * @return array|null
     */
    public function findByShopAndExternalId(string $shopId, string $externalId)
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->findByProperty('shopId', $shopId)
            ->findByProperty('externalId', $externalId)
            ->setValues([
                'c' => [
                    ['id.id', 'id'],
                    ['username.username', 'username'],
                    'email',
                    'firstName',
                    'lastName',
                    ['gender.gender', 'gender'],
                    'externalId',
                    'active',
                    'created'
                ]
            ])
            ->setPostProcess([
                'c' => [
                    'active' => [DqlQueryBuilder::class, 'decodeBool']
                ]
            ]);

        return $builder->getSingleResultOrNull($this->entityManager);
    }

    /**
     * @param string $searchString
     * @return array
     */
    public function findCustomers(string $searchString = ''): array
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->setValues([
                'c' => [
                    ['id.id', 'id'],
                    ['username.username', 'username'],
                    'email',
                    'firstName',
                    'lastName',
                    ['gender.gender', 'gender'],
                    'externalId',
                    'active',
                    'created'
                ]
            ])
            ->setPostProcess([
                'c' => [
                    'active' => [DqlQueryBuilder::class, 'decodeBool']
                ]
            ])
            ->setSearch([
                'c' => [
                    'id.id',
                    'username.username',
                    'email',
                    'firstName',
                    'lastName',
                    'externalId'
                ]
            ], $searchString)
            ->setOrderBy([
                ['c.created', 'ASC']
            ]);

        return $builder->getResult($this->entityManager);
    }

}