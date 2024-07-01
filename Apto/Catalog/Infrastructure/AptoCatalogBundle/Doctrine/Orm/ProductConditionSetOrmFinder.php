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
     * @param array $ids
     * @return array|mixed
     * @throws DqlBuilderException
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
                    'conditionsOperator',
                ],
                'p' => [
                    ['id.id', 'id'],
                    ['identifier.value', 'identifier'],
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
                    ['conditions', 'csc', 'id'],
                    ['product', 'p', 'id']
                ],
                'csc' => [
                    ['computedProductValue', 'cpv', 'surrogateId']
                ]
            ])
            ->setPostProcess([
                'c' => [
                    'conditionsOperator' => [DqlQueryBuilder::class, 'decodeInteger'],
                ],
                'csc' => [
                    'operator' => [DqlQueryBuilder::class, 'decodeInteger'],
                    'value' => [DqlQueryBuilder::class, 'castString'],
                    'type' => [DqlQueryBuilder::class, 'decodeInteger']
                ]
            ]);

        $result = $builder->getResult($this->entityManager);

        foreach ($result['data'] as &$conditionSet) {
            if (count($conditionSet['product']) === 0) {
                $conditionSet['product'] = null;
            } else {
                $conditionSet['product'] = $conditionSet['product'][0];
            }
        }

        return $result;
    }

    /**
     * @param string $productId
     * @param array $ids
     * @return array
     * @throws DqlBuilderException,
     */
    public function findByIdsForProduct(string $productId, array $ids)
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->setWhere('c.id.id in (:ids) AND p.id.id = :productId', ['ids' => $ids, 'productId' => $productId])
            ->setValues([
                'c' => [
                    ['id.id', 'id'],
                    ['identifier.value', 'identifier'],
                    'conditionsOperator',
                ],
                'p' => [
                    ['id.id', 'id'],
                    ['identifier.value', 'identifier'],
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
                    ['conditions', 'csc', 'id'],
                    ['product', 'p', 'id']
                ],
                'csc' => [
                    ['computedProductValue', 'cpv', 'surrogateId']
                ]
            ])
            ->setPostProcess([
                'c' => [
                    'conditionsOperator' => [DqlQueryBuilder::class, 'decodeInteger'],
                ],
                'csc' => [
                    'operator' => [DqlQueryBuilder::class, 'decodeInteger'],
                    'value' => [DqlQueryBuilder::class, 'castString'],
                    'type' => [DqlQueryBuilder::class, 'decodeInteger']
                ]
            ]);

        $result = $builder->getResult($this->entityManager);

        foreach ($result['data'] as &$conditionSet) {
            if (count($conditionSet['product']) === 0) {
                $conditionSet['product'] = null;
            } else {
                $conditionSet['product'] = $conditionSet['product'][0];
            }
        }

        return $result;
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
