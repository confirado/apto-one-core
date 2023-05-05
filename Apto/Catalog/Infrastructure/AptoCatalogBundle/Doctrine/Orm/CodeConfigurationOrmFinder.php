<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\Doctrine\Orm;

use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\AptoOrmFinder;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlQueryBuilder;
use Apto\Catalog\Application\Core\Query\Configuration\CodeConfigurationFinder;

use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlBuilderException;
use Apto\Catalog\Domain\Core\Model\Configuration\CodeConfiguration;

class CodeConfigurationOrmFinder extends AptoOrmFinder implements CodeConfigurationFinder
{
    const ENTITY_CLASS = CodeConfiguration::class;

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
                'c' => [
                    ['id.id', 'id'],
                    'created',
                    'state',
                    'code'
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
     * @param string $code
     * @return array|null
     * @throws DqlBuilderException
     */
    public function findByCode(string $code)
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->findByProperty('code', $code)
            ->setValues([
                'c' => [
                    ['id.id', 'id'],
                    'created',
                    'state',
                    'code'
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
}