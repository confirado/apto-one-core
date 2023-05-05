<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\Doctrine\Orm;

use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\AptoOrmFinder;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlQueryBuilder;
use Apto\Catalog\Application\Core\Query\Filter\FilterCategoryFinder;

use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlBuilderException;
use Apto\Catalog\Domain\Core\Model\Filter\FilterCategory;

class FilterCategoryOrmFinder extends AptoOrmFinder implements FilterCategoryFinder
{
    const ENTITY_CLASS = FilterCategory::class;

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
                            'f' => [
                                ['id.id', 'id'],
                                'name',
                                'position',
                                ['identifier.value', 'identifier'],
                                'created'
                            ]
                        ])
            ->setPostProcess([
                'f' => [
                    'name' => [DqlQueryBuilder::class, 'decodeJson'],
                    'position' => [DqlQueryBuilder::class, 'decodeInteger']
                ]
            ]);

        return $builder->getSingleResultOrNull($this->entityManager);
    }

    /**
     * @param string $identifier
     * @return array|null
     * @throws DqlBuilderException
     */
    public function findByIdentifier(string $identifier)
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->setWhere('f.identifier.value = :identifier', ['identifier' => $identifier])
            ->setValues([
                            'f' => [
                                ['id.id', 'id'],
                                'position',
                                ['identifier.value', 'identifier'],
                            ]
                        ])
            ->setPostProcess([
                'f' => [
                    'position' => [DqlQueryBuilder::class, 'decodeInteger']
                ]
            ]);

        return $builder->getSingleResultOrNull($this->entityManager);
    }

    /**
     * @param string $searchString
     * @return array
     * @throws DqlBuilderException
     */
    public function findFilterCategories(string $searchString = ''): array
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->setValues([
                            'f' => [
                                ['id.id', 'id'],
                                'name',
                                'position',
                                ['identifier.value', 'identifier'],
                                'created'
                            ]
                        ])
            ->setSearch([
                            'f' => [
                                'id.id',
                                'name'
                            ]
                        ], $searchString)
            ->setOrderBy([
                             ['f.position', 'ASC']
                         ])
            ->setPostProcess([
                                 'f' => [
                                     'name' => [DqlQueryBuilder::class, 'decodeJson'],
                                     'position' => [DqlQueryBuilder::class, 'decodeInteger']
                                 ]
                             ]);

        return $builder->getResult($this->entityManager);
    }
}