<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\Doctrine\Orm;

use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\AptoOrmFinder;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlQueryBuilder;
use Apto\Catalog\Application\Core\Query\Filter\FilterPropertyFinder;

use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlBuilderException;
use Apto\Catalog\Domain\Core\Model\Filter\FilterProperty;

class FilterPropertyOrmFinder extends AptoOrmFinder implements FilterPropertyFinder
{
    const ENTITY_CLASS = FilterProperty::class;

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
                    ['identifier.value', 'identifier'],
                    'created'
                ],
                'fc' => [
                    ['id.id', 'id'],
                    'name'
                ]
            ])
            ->setJoins([
               'f' => [
                   ['filterCategories', 'fc', 'id']
               ]
            ])
            ->setPostProcess([
                 'f' => [
                     'name' => [DqlQueryBuilder::class, 'decodeJson']
                 ],
                 'fc' => [
                     'name' => [DqlQueryBuilder::class, 'decodeJson']
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
                                ['identifier.value', 'identifier']
                            ]
                        ]);

        return $builder->getSingleResultOrNull($this->entityManager);
    }

    /**
     * @param string $searchString
     * @return array
     * @throws DqlBuilderException
     */
    public function findFilterProperties(string $searchString = ''): array
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->setValues([
                            'f' => [
                                ['id.id', 'id'],
                                'name',
                                ['identifier.value', 'identifier'],
                                'created'
                            ],
                            'fc' => [
                                ['id.id', 'id'],
                                'name',
                                ['identifier.value', 'identifier'],
                            ]
                        ])
            ->setJoins([
                           'f' => [
                               ['filterCategories', 'fc', 'id']
                           ]

                        ])
            ->setSearch([
                            'f' => [
                                'id.id',
                                'name'
                            ]
                        ], $searchString)
            ->setOrderBy([
                             ['f.created', 'DESC']
                         ])
            ->setPostProcess([
                                 'f' => [
                                     'name' => [DqlQueryBuilder::class, 'decodeJson']
                                 ],
                                 'fc' => [
                                     'name' => [DqlQueryBuilder::class, 'decodeJson']
                                 ]
                             ]);

        return $builder->getResult($this->entityManager);
    }
}