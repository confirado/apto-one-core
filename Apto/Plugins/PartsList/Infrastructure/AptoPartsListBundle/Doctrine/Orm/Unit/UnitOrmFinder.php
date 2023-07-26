<?php

namespace Apto\Plugins\PartsList\Infrastructure\AptoPartsListBundle\Doctrine\Orm\Unit;

use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\AptoOrmFinder;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlPaginatorBuilder;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlQueryBuilder;
use Apto\Plugins\PartsList\Application\Core\Query\Unit\UnitFinder;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlBuilderException;
use Apto\Plugins\PartsList\Domain\Core\Model\Unit\Unit;

class UnitOrmFinder extends AptoOrmFinder implements UnitFinder
{
    const ENTITY_CLASS = Unit::class;

    const MODEL_VALUES = [
        ['id.id', 'id'],
        'unit'
    ];

    const MODEL_POST_PROCESSES = [
    ];

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
                'u' => self::MODEL_VALUES
            ])
            ->setPostProcess([
                'u' => self::MODEL_POST_PROCESSES
            ]);

        $result = $builder->getSingleResultOrNull($this->entityManager);

        if (null === $result) {
            return null;
        }

        return $result;
    }

    /**
     * @param int $pageNumber
     * @param int $recordsPerPage
     * @param string $searchString
     * @return array
     * @throws DqlBuilderException
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByListingPageNumber(int $pageNumber = 1, int $recordsPerPage = 20, string $searchString = ''): array
    {
        $builder = new DqlPaginatorBuilder($this->entityClass);
        $builder
            ->setPage($pageNumber)
            ->setRecordsPerPage($recordsPerPage)
            ->setValues([
                'u' => self::MODEL_VALUES
            ])
            ->setSearch([
                'u' => [
                    'id.id',
                    'unit'
                ]
            ], $searchString)
            ->setPostProcess([
                'u' => self::MODEL_POST_PROCESSES
            ])
            ->setOrderBy([
                ['u.created', 'DESC']
            ]);

        return $builder->getResult($this->entityManager);
    }
}