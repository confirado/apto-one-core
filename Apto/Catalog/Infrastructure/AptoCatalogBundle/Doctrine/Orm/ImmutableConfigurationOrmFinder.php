<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\Doctrine\Orm;

use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\AptoOrmFinder;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlBuilder;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlQueryBuilder;
use Apto\Catalog\Application\Core\Query\Configuration\ImmutableConfigurationFinder;

use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlBuilderException;
use Apto\Catalog\Domain\Core\Model\Configuration\ImmutableConfiguration;
use Apto\Catalog\Infrastructure\AptoCatalogBundle\Doctrine\Dql\ImmutableConfigurationDqlService;

class ImmutableConfigurationOrmFinder extends AptoOrmFinder implements ImmutableConfigurationFinder
{
    const ENTITY_CLASS = ImmutableConfiguration::class;

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
                'i' => [
                    ['id.id', 'id'],
                    'created',
                    'state'
                ],
                'p' => [
                    ['id.id', 'id']
                ],
                'cp' => [
                    ['id.id', 'id'],
                    'surrogateId',
                    'key',
                    'value',
                    'translatable'
                ],
            ])->setJoins([
                'i' => [
                    ['product', 'p', 'id'],
                    ['customProperties', 'cp', 'surrogateId']
                ]
            ]);

        return $builder->getSingleResultOrNull($this->entityManager);
    }

    /**
     * @return array
     * @throws DqlBuilderException
     */
    public function findAll()
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->setValues(
                [
                    'i' => [
                        ['id.id', 'id'],
                        'created',
                        'state'
                    ],
                    'p' => [
                        ['id.id', 'id']
                    ],
                    'cp' => [
                        ['id.id', 'id'],
                        'surrogateId',
                        'key',
                        'value',
                        'translatable'
                    ],
                ]
            )->setJoins(
                [
                    'i' => [
                        ['product', 'p', 'id'],
                        ['customProperties', 'cp', 'surrogateId']
                    ]
                ]
            );

        return $builder->getResult($this->entityManager);
    }

    /**
     * @param array $conditions
     * @return array|null
     * @throws DqlBuilderException
     */
    public function findByCustomPropertyValues(array $conditions)
    {
        $dqlService = new ImmutableConfigurationDqlService($this->entityManager, $this->entityClass);
        return $dqlService->findByCustomPropertyValues($conditions);
    }
}
