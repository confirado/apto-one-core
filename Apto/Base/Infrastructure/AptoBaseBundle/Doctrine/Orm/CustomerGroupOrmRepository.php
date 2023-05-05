<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm;

use Apto\Base\Domain\Core\Model\CustomerGroup\CustomerGroup;
use Apto\Base\Domain\Core\Model\CustomerGroup\CustomerGroupRepository;

class CustomerGroupOrmRepository extends AptoOrmRepository implements CustomerGroupRepository
{
    const ENTITY_CLASS = CustomerGroup::class;

    /**
     * @param CustomerGroup $model
     */
    public function update(CustomerGroup $model)
    {
        $this->_em->merge($model);
    }

    /**
     * @param CustomerGroup $model
     */
    public function add(CustomerGroup $model)
    {
        $this->_em->persist($model);
    }

    /**
     * @param CustomerGroup $model
     */
    public function remove(CustomerGroup $model)
    {
        $this->_em->remove($model);
    }

    /**
     * @param mixed|string $id
     * @return CustomerGroup|mixed|null|object
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findById($id)
    {
        $builder = $this->createQueryBuilder('CustomerGroup')
            ->where('CustomerGroup.id.id = :id')
            ->setParameter('id', $id);

        return $builder->getQuery()->getOneOrNullResult();
    }

    /**
     * @param string $shopId
     * @return array
     */
    public function findFallback(string $shopId): array
    {
        $builder = $this->createQueryBuilder('CustomerGroup')
            ->where('CustomerGroup.shopId = :shopId AND CustomerGroup.fallback = :fallback')
            ->setParameters([
                'shopId' => $shopId,
                'fallback' => true
            ]);

        return $builder->getQuery()->getResult();
    }

    /**
     * @param string $shopId
     * @param string $excludeId
     * @return array
     */
    public function findFallbackWithExcludeId(string $shopId, string $excludeId): array
    {
        $builder = $this->createQueryBuilder('CustomerGroup')
            ->where('CustomerGroup.id.id != :excludeId AND CustomerGroup.shopId = :shopId AND CustomerGroup.fallback = :fallback')
            ->setParameters([
                'excludeId' => $excludeId,
                'shopId' => $shopId,
                'fallback' => true
            ]);

        return $builder->getQuery()->getResult();
    }

    /**
     * @param string $name
     * @return CustomerGroup|mixed|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneByName(string $name)
    {
        $builder = $this->createQueryBuilder('CustomerGroup')
            ->where('CustomerGroup.name = :name')
            ->setParameter('name', $name);

        return $builder->getQuery()->getOneOrNullResult();
    }

    /**
     * @param string $shopId
     * @param string $externalId
     * @return CustomerGroup|mixed|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneByShopAndExternalId(string $shopId, string $externalId)
    {
        $builder = $this->createQueryBuilder('CustomerGroup')
            ->where('CustomerGroup.shopId = :shopId')
            ->andWhere('CustomerGroup.externalId = :externalId')
            ->setParameter('shopId', $shopId)
            ->setParameter('externalId', $externalId);

        return $builder->getQuery()->getOneOrNullResult();
    }

    /**
     * @param string $shopId
     * @return array|null
     */
    public function findAllExternalAndUuidsByShopId(string $shopId)
    {
        $dql = 'SELECT
                  cg.id.id as id,
                  cg.externalId,
                  cg.inputGross,
                  cg.showGross,
                  cg.fallback
              FROM
                  ' . $this->getEntityName() . ' cg
              WHERE
                  cg.shopId = :shopId AND cg.externalId IS NOT NULL';

        $query = $this->_em->createQuery($dql)
            ->setParameter('shopId', $shopId);

        return $query->getScalarResult();
    }
}