<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\Doctrine\Orm;

use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\AptoOrmFinder;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlQueryBuilder;
use Apto\Catalog\Application\Core\Query\Group\GroupFinder;

use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlBuilderException;
use Apto\Catalog\Domain\Core\Model\Group\Group;

class GroupOrmFinder extends AptoOrmFinder implements GroupFinder
{
    const ENTITY_CLASS = Group::class;

    /**
     * @param string $id
     * @return array|null
     * @throws DqlBuilderException
     */
    public function findById(string $id)
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->findById($id)
            ->setValues([
                'g' => [
                    ['id.id', 'id'],
                    'name',
                    'position',
                    ['identifier.value', 'identifier'],
                    'created'
                ]
            ])
            ->setPostProcess([
                'g' => [
                    'name' => [DqlQueryBuilder::class, 'decodeJson'],
                    'position' => [DqlQueryBuilder::class, 'decodeInteger']
                ]
            ]);

        return $builder->getSingleResultOrNull($this->entityManager);
    }

    /**
     * @param string $identifier
     * @return array|null
     * @throws DqlBuilderException
     */
    public function findByIdentifier(string $identifier)
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->setWhere('g.identifier.value = :identifier', ['identifier' => $identifier])
            ->setValues([
                'g' => [
                    ['id.id', 'id'],
                    ['identifier.value', 'identifier']
                ]
            ]);

        return $builder->getSingleResultOrNull($this->entityManager);
    }

    /**
     * @param string $searchString
     * @return array
     * @throws DqlBuilderException
     */
    public function findGroups(string $searchString = ''): array
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->setValues([
                'g' => [
                    ['id.id', 'id'],
                    'name',
                    'position',
                    ['identifier.value', 'identifier'],
                    'created'
                ]
            ])
            ->setSearch([
                'g' => [
                    'id.id',
                    'name'
                ]
            ], $searchString)
            ->setOrderBy([
                ['g.created', 'DESC']
            ])
            ->setPostProcess([
                'g' => [
                    'name' => [DqlQueryBuilder::class, 'decodeJson'],
                    'position' => [DqlQueryBuilder::class, 'decodeInteger']
                ]
            ]);

        return $builder->getResult($this->entityManager);
    }
}