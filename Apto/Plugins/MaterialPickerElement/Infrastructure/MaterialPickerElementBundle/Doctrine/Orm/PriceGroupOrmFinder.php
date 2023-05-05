<?php

namespace Apto\Plugins\MaterialPickerElement\Infrastructure\MaterialPickerElementBundle\Doctrine\Orm;

use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\AptoOrmFinder;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlPaginatorBuilder;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlQueryBuilder;
use Apto\Plugins\MaterialPickerElement\Application\Core\Query\PriceGroup\PriceGroupFinder;
use Apto\Plugins\MaterialPickerElement\Domain\Core\Model\PriceGroup\PriceGroup;

class PriceGroupOrmFinder extends AptoOrmFinder implements PriceGroupFinder
{
    const ENTITY_CLASS = PriceGroup::class;

    const ALL_VALUES = [
        ['id.id', 'id'],
        'name',
        'internalName',
        'additionalCharge',
        ['priceMatrix.id', 'priceMatrixId'],
        ['priceMatrix.row', 'priceMatrixRow'],
        ['priceMatrix.column', 'priceMatrixColumn'],
        ['priceMatrix.pricePostProcess', 'priceMatrixPricePostProcess'],
        'created'
    ];

    const ALL_POST_PROCESSES = [
        'name' => [DqlQueryBuilder::class, 'decodeJson'],
        'internalName' => [DqlQueryBuilder::class, 'decodeJson'],
        'additionalCharge' => [DqlQueryBuilder::class, 'decodeFloat']
    ];

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
                'p' => self::ALL_VALUES
            ])
            ->setPostProcess([
                'p' => self::ALL_POST_PROCESSES
            ]);

        $result = $builder->getSingleResultOrNull($this->entityManager);
        $this->setEntityPriceMatrix($result);

        return $result;
    }

    /**
     * @param string $searchString
     * @return array
     */
    public function findPriceGroups(string $searchString): array
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->setValues([
                'p' => self::ALL_VALUES
            ])
            ->setSearch([
                'p' => [
                    'id.id',
                    'name'
                ]
            ], $searchString)
            ->setPostProcess([
                'p' => self::ALL_POST_PROCESSES
            ])
            ->setOrderBy([
                ['p.created', 'DESC']
            ]);

        $result = $builder->getResult($this->entityManager);
        $this->setEntitiesPriceMatrix($result['data']);

        return $result;
    }

    /**
     * @param int $pageNumber
     * @param int $recordsPerPage
     * @param string $searchString
     * @return array
     */
    public function findByListingPageNumber(int $pageNumber = 1, int $recordsPerPage = 20, string $searchString = ''): array
    {
        $builder = new DqlPaginatorBuilder($this->entityClass);
        $builder
            ->setPage($pageNumber)
            ->setRecordsPerPage($recordsPerPage)
            ->setValues([
                'p' => self::ALL_VALUES
            ])
            ->setSearch([
                'p' => [
                    'id.id',
                    'name'
                ]
            ], $searchString)
            ->setPostProcess([
                'p' => self::ALL_POST_PROCESSES
            ])
            ->setOrderBy([
                ['p.created', 'DESC']
            ]);

        $result = $builder->getResult($this->entityManager);
        $this->setEntitiesPriceMatrix($result['data']);

        return $result;
    }

    /**
     * @param array $entities
     */
    private function setEntitiesPriceMatrix(array &$entities)
    {
        foreach ($entities as &$entity) {
            $this->setEntityPriceMatrix($entity);
        }
    }

    /**
     * @param array|null $entity
     */
    private function setEntityPriceMatrix(?array &$entity)
    {
        if (null === $entity) {
            return;
        }

        $priceMatrix = [
            'id' => null,
            'row' => null,
            'column' => null,
            'pricePostProcess' => null
        ];

        if (array_key_exists('priceMatrixId', $entity)) {
            $priceMatrix['id'] = $entity['priceMatrixId'];
            unset($entity['priceMatrixId']);
        }

        if (array_key_exists('priceMatrixRow', $entity)) {
            $priceMatrix['row'] = $entity['priceMatrixRow'];
            unset($entity['priceMatrixRow']);
        }

        if (array_key_exists('priceMatrixColumn', $entity)) {
            $priceMatrix['column'] = $entity['priceMatrixColumn'];
            unset($entity['priceMatrixColumn']);
        }

        if (array_key_exists('priceMatrixPricePostProcess', $entity)) {
            $priceMatrix['pricePostProcess'] = $entity['priceMatrixPricePostProcess'];
            unset($entity['priceMatrixPricePostProcess']);
        }

        $entity['priceMatrix'] = $priceMatrix;
    }
}
