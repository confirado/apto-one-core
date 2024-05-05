<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\Doctrine\Orm;

use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\AptoOrmFinder;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlBuilderException;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlQueryBuilder;
use Apto\Catalog\Application\Core\Query\Product\Condition\ProductConditionSetFinder;
use Apto\Catalog\Domain\Core\Model\Product\Condition\ConditionSet;

class ProductConditionSetOrmFinder extends AptoOrmFinder implements ProductConditionSetFinder
{
    const ENTITY_CLASS = ConditionSet::class;

    /**
     * @param string $id
     * @return array|null
     * @throws DqlBuilderException
     */
    public function findById(string $id): ?array
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->findById($id)
            ->setValues([
                'c' => [
                    ['id.id', 'id'],
                    ['identifier.value', 'identifier'],
                    'conditionsOperator',
                ]
            ])
            ->setPostProcess([
                'c' => [
                    'conditionsOperator' => [DqlQueryBuilder::class, 'decodeInteger'],
                ]
            ])
        ;

        return $builder->getSingleResultOrNull($this->entityManager);
    }

    /**
     * @param string $id
     * @return array|null
     * @throws DqlBuilderException
     */
    public function findConditions(string $id): ?array
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->findById($id)
            ->setValues([
                'c' => [
                ],
                'csc' => [
                    ['id.id', 'id'],
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
                    ['conditions', 'csc', 'id']
                ],
                'csc' => [
                    ['computedProductValue', 'cpv', 'surrogateId']
                ]
            ])
            ->setPostProcess([
                'csc' => [
                    'value' => [DqlQueryBuilder::class, 'castString'],
                    'type' => [DqlQueryBuilder::class, 'decodeInteger']
                ]
            ]);
        return $builder->getSingleResultOrNull($this->entityManager);
    }
}
