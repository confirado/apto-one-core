<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\Doctrine\Orm;

use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\AptoOrmFinder;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlQueryBuilder;
use Apto\Catalog\Application\Core\Query\Configuration\GuestConfigurationFinder;

use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlBuilderException;
use Apto\Catalog\Domain\Core\Model\Configuration\GuestConfiguration;

class GuestConfigurationOrmFinder extends AptoOrmFinder implements GuestConfigurationFinder
{
    const ENTITY_CLASS = GuestConfiguration::class;

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
                    'created',
                    'state'
                ],
                'p' => [
                    ['id.id', 'id']
                ]
            ])->setJoins([
                'g' => [
                    ['product', 'p', 'id']
                ]
            ]);

        return $builder->getSingleResultOrNull($this->entityManager);
    }
}