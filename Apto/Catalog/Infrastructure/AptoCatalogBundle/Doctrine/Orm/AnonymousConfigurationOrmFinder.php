<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\Doctrine\Orm;

use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\AptoOrmFinder;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlQueryBuilder;
use Apto\Catalog\Application\Core\Query\Configuration\AnonymousConfigurationFinder;

use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlBuilderException;
use Apto\Catalog\Domain\Core\Model\Configuration\AnonymousConfiguration;

class AnonymousConfigurationOrmFinder extends AptoOrmFinder implements AnonymousConfigurationFinder
{
    const ENTITY_CLASS = AnonymousConfiguration::class;

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
                'a' => [
                    ['id.id', 'id'],
                    'created',
                    'state'
                ],
                'p' => [
                    ['id.id', 'id']
                ]
            ])->setJoins([
                'a' => [
                    ['product', 'p', 'id']
                ]
            ]);

        return $builder->getSingleResultOrNull($this->entityManager);
    }
}
