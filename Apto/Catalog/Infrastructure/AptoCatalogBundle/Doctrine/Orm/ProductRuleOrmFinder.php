<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\Doctrine\Orm;

use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\AptoOrmFinder;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlQueryBuilder;
use Apto\Catalog\Application\Core\Query\Product\Rule\ProductRuleFinder;
use Apto\Catalog\Domain\Core\Model\Product\Rule\Rule;

class ProductRuleOrmFinder extends AptoOrmFinder implements ProductRuleFinder
{
    const ENTITY_CLASS = Rule::class;

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
                'r' => [
                    ['id.id', 'id'],
                    'active',
                    'name',
                    'errorMessage',
                    'conditionsOperator',
                    'implicationsOperator',
                    'softRule',
                    'description',
                ]
            ])
            ->setPostProcess([
                'r' => [
                    'active' => [DqlQueryBuilder::class, 'decodeBool'],
                    'errorMessage' => [DqlQueryBuilder::class, 'decodeJson'],
                    'conditionsOperator' => [DqlQueryBuilder::class, 'decodeInteger'],
                    'implicationsOperator' => [DqlQueryBuilder::class, 'decodeInteger'],
                    'softRule' => [DqlQueryBuilder::class, 'decodeBool']
                ]
            ]);

        return $builder->getSingleResultOrNull($this->entityManager);
    }

    /**
     * @param string $id
     * @return array|null
     * @throws \Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlBuilderException
     */
    public function findConditions(string $id)
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->findById($id)
            ->setValues([
                'r' => [
                ],
                'c' => [
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
                'r' => [
                    ['conditions', 'c', 'id']
                ],
                'c' => [
                    ['computedProductValue', 'cpv', 'surrogateId']
                ]
            ])
            ->setPostProcess([
                'c' => [
                    'value' => [DqlQueryBuilder::class, 'castString'],
                    'type' => [DqlQueryBuilder::class, 'decodeInteger']
                ]
            ]);
        return $builder->getSingleResultOrNull($this->entityManager);
    }

    /**
     * @param string $id
     * @return array|null
     * @throws \Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlBuilderException
     */
    public function findImplications(string $id)
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->findById($id)
            ->setValues([
                'r' => [
                ],
                'i' => [
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
                'r' => [
                    ['implications', 'i', 'id']
                ],
                'i' => [
                    ['computedProductValue', 'cpv', 'surrogateId']
                ]
            ])
            ->setPostProcess([
                'i' => [
                    'value' => [DqlQueryBuilder::class, 'castString'],
                    'type' => [DqlQueryBuilder::class, 'decodeInteger']
                ]
            ]);

        return $builder->getSingleResultOrNull($this->entityManager);
    }
}
