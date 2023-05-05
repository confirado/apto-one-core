<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm;

use Apto\Base\Application\Core\Query\CustomerGroup\CustomerGroupFinder;
use Apto\Base\Domain\Core\Model\CustomerGroup\CustomerGroup;

class CustomerGroupOrmFinder extends AptoOrmFinder implements CustomerGroupFinder
{
    const ENTITY_CLASS = CustomerGroup::class;

    /**
     * @param string $property
     * @param string $value
     * @return null
     * @throws DqlBuilderException
     */
    protected function findByProperty(string $property, string $value)
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->findByProperty($property, $value)
            ->setValues([
                'c' => [
                    ['id.id', 'id'],
                    'name',
                    'inputGross',
                    'showGross',
                    'shopId',
                    'externalId',
                    'fallback',
                    'created'
                ]
            ])
            ->setPostProcess([
                'c' => [
                    'inputGross' => [DqlQueryBuilder::class, 'decodeBool'],
                    'showGross' => [DqlQueryBuilder::class, 'decodeBool'],
                    'fallback' => [DqlQueryBuilder::class, 'decodeBool']
                ]
            ]);

        return $builder->getSingleResultOrNull($this->entityManager);
    }

    /**
     * @param string $id
     * @return array|null
     * @throws DqlBuilderException
     */
    public function findById(string $id)
    {
        return $this->findByProperty('id.id', $id);
    }

    /**
     * @param string $name
     * @return array|null
     * @throws DqlBuilderException
     */
    public function findByName(string $name)
    {
        return $this->findByProperty('name', $name);
    }

    /**
     * @param string $shopId
     * @param string $externalId
     * @return array|null
     * @throws DqlBuilderException
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
                    'name',
                    'inputGross',
                    'showGross',
                    'externalId',
                    'fallback',
                    'created'
                ]
            ])
            ->setPostProcess([
                'c' => [
                    'inputGross' => [DqlQueryBuilder::class, 'decodeBool'],
                    'showGross' => [DqlQueryBuilder::class, 'decodeBool'],
                    'fallback' => [DqlQueryBuilder::class, 'decodeBool']
                ]
            ]);

        return $builder->getSingleResultOrNull($this->entityManager);
    }

    /**
     * @return array|null
     * @throws DqlBuilderException
     */
    public function findFallbackCustomerGroup()
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->findByProperty('fallback', true)
            ->setValues([
                'c' => [
                    ['id.id', 'id'],
                    'name',
                    'inputGross',
                    'showGross',
                    'externalId',
                    'fallback',
                    'created'
                ]
            ])
            ->setPostProcess([
                'c' => [
                    'inputGross' => [DqlQueryBuilder::class, 'decodeBool'],
                    'showGross' => [DqlQueryBuilder::class, 'decodeBool'],
                    'fallback' => [DqlQueryBuilder::class, 'decodeBool']
                ]
            ]);

        return $builder->getSingleResultOrNull($this->entityManager);
    }

    /**
     * @param string $searchString
     * @return array
     * @throws DqlBuilderException
     */
    public function findCustomerGroups(string $searchString = ''): array
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->setValues([
                'c' => [
                    ['id.id', 'id'],
                    'name',
                    'inputGross',
                    'showGross',
                    'externalId',
                    'fallback',
                    'created'
                ]
            ])
            ->setPostProcess([
                'c' => [
                    'inputGross' => [DqlQueryBuilder::class, 'decodeBool'],
                    'showGross' => [DqlQueryBuilder::class, 'decodeBool'],
                    'fallback' => [DqlQueryBuilder::class, 'decodeBool']
                ]
            ])
            ->setSearch([
                'c' => [
                    'id.id',
                    'name',
                    'externalId'
                ]
            ], $searchString)
            ->setOrderBy([
                ['c.created', 'ASC']
            ]);

        return $builder->getResult($this->entityManager);
    }

    /**
     * @param string $shopId
     * @return array|null
     */
    public function findAllExternalAndUuidsByShopId(string $shopId): ?array
    {
        $dql = 'SELECT
                  cg.id.id as id,
                  cg.externalId,
                  cg.inputGross,
                  cg.showGross,
                  cg.fallback
              FROM
                  ' . $this->entityClass . ' cg
              WHERE
                  cg.shopId = :shopId AND cg.externalId IS NOT NULL';

        $query = $this->entityManager->createQuery($dql)
            ->setParameter('shopId', $shopId);

        return $query->getScalarResult();
    }
}