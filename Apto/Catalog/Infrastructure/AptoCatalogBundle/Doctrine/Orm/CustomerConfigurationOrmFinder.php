<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\Doctrine\Orm;

use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\AptoOrmFinder;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlQueryBuilder;
use Apto\Catalog\Application\Core\Query\Configuration\CustomerConfigurationFinder;
use Apto\Catalog\Domain\Core\Model\Configuration\CustomerConfiguration;

class CustomerConfigurationOrmFinder extends AptoOrmFinder implements CustomerConfigurationFinder
{
    const ENTITY_CLASS = CustomerConfiguration::class;
    
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
                'c' => [
                    ['id.id', 'id'],
                    'name',
                    'created',
                    'state'
                ],
                'p' => [
                    ['id.id', 'id']
                ]
            ])->setJoins([
                'c' => [
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
                'c' => [
                    ['id.id', 'id'],
                    'name',
                    'created',
                    'state'
                ]
            ])
            ->setSearch([
                'c' => [
                    'id.id',
                    'name'
                ]
            ], $searchString)
            ->setOrderBy([
                ['c.created', 'DESC']
            ]);

        return $builder->getResult($this->entityManager);
    }
}