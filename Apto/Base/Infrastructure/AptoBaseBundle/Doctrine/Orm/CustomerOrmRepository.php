<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm;

use Apto\Base\Domain\Core\Model\Customer\Customer;
use Apto\Base\Domain\Core\Model\Customer\CustomerRepository;

class CustomerOrmRepository extends AptoOrmRepository implements CustomerRepository
{
    const ENTITY_CLASS = Customer::class;

    /**
     * @param Customer $model
     */
    public function update(Customer $model)
    {
        $this->_em->merge($model);
    }

    /**
     * @param Customer $model
     */
    public function add(Customer $model)
    {
        $this->_em->persist($model);
    }

    /**
     * @param Customer $model
     */
    public function remove(Customer $model)
    {
        $this->_em->remove($model);
    }

    /**
     * @param string $id
     * @return Customer|null
     */
    public function findById($id)
    {
        $builder = $this->createQueryBuilder('Customer')
            ->where('Customer.id.id = :id')
            ->setParameter('id', $id);

        return $builder->getQuery()->getOneOrNullResult();
    }

    /**
     * @param string $username
     * @return Customer|null
     */
    public function findOneByUsername(string $username)
    {
        $builder = $this->createQueryBuilder('Customer')
            ->where('Customer.username.username = :username')
            ->setParameter('username', $username);

        return $builder->getQuery()->getOneOrNullResult();
    }

    /**
     * @param string $email
     * @return Customer|null
     */
    public function findOneByEmail(string $email)
    {
        $builder = $this->createQueryBuilder('Customer')
            ->where('Customer.email = :email')
            ->setParameter('email', $email);

        return $builder->getQuery()->getOneOrNullResult();
    }

    /**
     * @param string $shopId
     * @param string $externalId
     * @return Customer|null
     */
    public function findOneByShopAndExternalId(string $shopId, string $externalId)
    {
        $builder = $this->createQueryBuilder('Customer')
            ->where('Customer.shopId = :shopId')
            ->andWhere('Customer.externalId = :externalId')
            ->setParameter('shopId', $shopId)
            ->setParameter('externalId', $externalId);

        return $builder->getQuery()->getOneOrNullResult();
    }
}