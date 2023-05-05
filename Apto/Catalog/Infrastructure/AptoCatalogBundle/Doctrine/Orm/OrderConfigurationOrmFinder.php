<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\Doctrine\Orm;

use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\AptoOrmFinder;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlQueryBuilder;
use Apto\Catalog\Application\Core\Query\Configuration\OrderConfigurationFinder;
use Apto\Catalog\Domain\Core\Model\Configuration\OrderConfiguration;

class OrderConfigurationOrmFinder extends AptoOrmFinder implements OrderConfigurationFinder
{
    const ENTITY_CLASS = OrderConfiguration::class;

    /**
     * @param string $id
     * @return array|null
     */
    public function findById(string $id)
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->findById($id)
            ->setValues([
                'o' => [
                    ['id.id', 'id'],
                    'name',
                    'created',
                    'state'
                ],
                'p' => [
                    ['id.id', 'id']
                ]
            ])->setJoins([
                'o' => [
                    ['product', 'p', 'id']
                ]
            ]);

        return $builder->getSingleResultOrNull($this->entityManager);
    }

    /**
     * @param string $searchString
     * @return array
     */
    public function findConfigurations(string $searchString = ''): array
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->setValues([
                'o' => [
                    ['id.id', 'id'],
                    'name',
                    'created',
                    'state'
                ]
            ])
            ->setSearch([
                'o' => [
                    'id.id',
                    'name'
                ]
            ], $searchString)
            ->setOrderBy([
                ['o.created', 'DESC']
            ]);

        return $builder->getResult($this->entityManager);
    }
}