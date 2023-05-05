<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\Doctrine\Orm;

use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\AptoOrmFinder;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlQueryBuilder;
use Apto\Catalog\Application\Core\Query\Configuration\ProposedConfigurationFinder;
use Apto\Catalog\Domain\Core\Model\Configuration\ProposedConfiguration;

class ProposedConfigurationOrmFinder extends AptoOrmFinder implements ProposedConfigurationFinder
{
    const ENTITY_CLASS = ProposedConfiguration::class;

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
                'p' => [
                    ['id.id', 'id'],
                    'created',
                    'state'
                ],
                'pr' => [
                    ['id.id', 'id']
                ]
            ])->setJoins([
                'p' => [
                    ['product', 'pr', 'id']
                ]
            ]);

        return $builder->getSingleResultOrNull($this->entityManager);
    }

    /**
     * @param string $productId
     * @param string $searchString
     * @return array
     */
    public function findConfigurations(string $productId, string $searchString = ''): array
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->setValues([
                'p' => [
                    ['id.id', 'id'],
                    'created',
                    'state'
                ],
                'pr' => [
                    ['id.id', 'id']
                ]
            ])
            ->setSearch([
                'p' => [
                    'id.id',
                    'name'
                ]
            ], $searchString)
            ->setWhere('pr.id.id = :productId', ['productId' => $productId])
            ->setJoins([
                'p' => [
                    ['product', 'pr', 'id']
                ]
            ])
            ->setOrderBy([
                ['p.created', 'ASC']
            ]);

        return $builder->getResult($this->entityManager);
    }
}