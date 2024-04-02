<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\Doctrine\Orm;

use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\AptoOrmFinder;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlBuilderException;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlQueryBuilder;
use Apto\Catalog\Application\Core\Query\Product\Condition\ProductConditionFinder;
use Apto\Catalog\Domain\Core\Model\Product\Condition\Condition;

class ProductConditionOrmFinder extends AptoOrmFinder implements ProductConditionFinder
{
    const ENTITY_CLASS = Condition::class;

    /**
     * @param string $id
     * @return array|null
     * @throws DqlBuilderException
     */
    public function findConditions(string $id)
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->findById($id)
            ->setValues([
                'c' => [
                    ['id.id', 'id'],
                    ['identifier.value', 'identifier'],
                    'sectionId',
                    'elementId',
                    'property',
                    ['operator.operator', 'operator'],
                    'value',
                    'type'
                ],
                'cpv' => [
                    'surrogateId',
                    ['id.id', 'id'],
                    'name'
                ]
            ])
            ->setJoins([
                'c' => [
                    ['computedProductValue', 'cpv', 'surrogateId']
                ]
            ])
            ->setPostProcess([
                'c' => [
                    'operator' => [DqlQueryBuilder::class, 'decodeInteger'],
                    'value' => [DqlQueryBuilder::class, 'castString'],
                    'type' => [DqlQueryBuilder::class, 'decodeInteger']
                ]
            ]);
        return $builder->getSingleResultOrNull($this->entityManager);
    }
}
