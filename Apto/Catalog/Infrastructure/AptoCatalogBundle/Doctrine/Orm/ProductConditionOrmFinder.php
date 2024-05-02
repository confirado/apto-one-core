<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\Doctrine\Orm;

use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\AptoOrmFinder;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlQueryBuilder;
use Apto\Catalog\Application\Core\Query\Product\Condition\ProductConditionFinder;
use Apto\Catalog\Domain\Core\Model\Product\Condition\Condition;

class ProductConditionOrmFinder extends AptoOrmFinder implements ProductConditionFinder
{
    const ENTITY_CLASS = Condition::class;

    public function findById(string $id)
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

    /**
     * @param array $ids
     * @return mixed
     */
    public function findByIds(array $ids)
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->setWhere('c.id.id in (:ids)', ['ids' => $ids])
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

        return $builder->getResult($this->entityManager);
    }
}
