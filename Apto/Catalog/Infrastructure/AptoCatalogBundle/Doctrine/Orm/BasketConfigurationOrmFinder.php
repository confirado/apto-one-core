<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\Doctrine\Orm;

use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\AptoOrmFinder;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlQueryBuilder;
use Apto\Catalog\Application\Core\Query\Configuration\BasketConfigurationFinder;

use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlBuilderException;
use Apto\Catalog\Domain\Core\Model\Configuration\BasketConfiguration;

class BasketConfigurationOrmFinder extends AptoOrmFinder implements BasketConfigurationFinder
{
    const ENTITY_CLASS = BasketConfiguration::class;

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
                'b' => [
                    ['id.id', 'id'],
                    'created',
                    'state'
                ],
                'p' => [
                    ['id.id', 'id']
                ]
            ])->setJoins([
                'b' => [
                    ['product', 'p', 'id']
                ]
            ]);

        return $builder->getSingleResultOrNull($this->entityManager);
    }

    /**
     * @param string $searchString
     * @return array
     * @throws DqlBuilderException
     */
    public function findConfigurations(string $searchString = ''): array
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->setValues([
                'b' => [
                    ['id.id', 'id'],
                    'created',
                    'state'
                ]
            ])
            ->setSearch([
                'b' => [
                    'id.id',
                    'name'
                ]
            ], $searchString)
            ->setOrderBy([
                ['b.created', 'DESC']
            ]);

        return $builder->getResult($this->entityManager);
    }

    /**
     * @param array $idList
     * @return array
     */
    public function findBasketConfigurationByIdList(array $idList = []): array
    {
        $dql = 'SELECT
                  c.id.id as id,
                  c.created,
                  c.state
              FROM
                  ' . $this->entityClass . ' c
              WHERE
                  c.id.id IN (:idList)
              ORDER BY c.created DESC
              ';

        $query = $this->entityManager->createQuery($dql);
        $query->setParameters(['idList' => $idList]);

        return $query->getScalarResult();
    }
}