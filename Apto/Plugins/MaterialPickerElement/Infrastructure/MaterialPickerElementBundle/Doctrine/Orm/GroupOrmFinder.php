<?php

namespace Apto\Plugins\MaterialPickerElement\Infrastructure\MaterialPickerElementBundle\Doctrine\Orm;

use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\AptoOrmFinder;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlPaginatorBuilder;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlQueryBuilder;
use Apto\Plugins\MaterialPickerElement\Application\Core\Query\Property\GroupFinder;
use Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Property\Group;
use Doctrine\ORM\EntityManagerInterface;

class GroupOrmFinder extends AptoOrmFinder implements GroupFinder
{
    const ENTITY_CLASS = Group::class;

    const ALL_VALUES = [
        ['id.id', 'id'],
        'name',
        'allowMultiple',
        'created'
    ];

    const ALL_POST_PROCESSES = [
        'name' => [DqlQueryBuilder::class, 'decodeJson'],
        'allowMultiple' => [DqlQueryBuilder::class, 'decodeBool']
    ];

    const PROPERTY_ALL_VALUES = [
        ['id.id', 'id'],
        'name',
        'created',
        'isDefault'
    ];

    const PROPERTY_ALL_POST_PROCESSES = [
        'name' => [DqlQueryBuilder::class, 'decodeJson'],
        'isDefault' => [DqlQueryBuilder::class, 'decodeBool']
    ];

    /**
     * @var string
     */
    protected $propertyEntityClass;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager);
        $this->propertyEntityClass = 'Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Property\Property';
    }

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
                'g' => self::ALL_VALUES
            ])
            ->setPostProcess([
                'g' => self::ALL_POST_PROCESSES
            ]);

        return $builder->getSingleResultOrNull($this->entityManager);
    }

    /**
     * @param string $id
     * @return array|null
     */
    public function findPropertyById(string $id)
    {
        $builder = new DqlQueryBuilder($this->propertyEntityClass);
        $builder
            ->findById($id)
            ->setValues([
                'p' => self::PROPERTY_ALL_VALUES
            ])
            ->setPostProcess([
                'p' => self::PROPERTY_ALL_POST_PROCESSES
            ]);

        return $builder->getSingleResultOrNull($this->entityManager);
    }

    /**
     * @param string $searchString
     * @return array
     */
    public function findGroups(string $searchString): array
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->setValues([
                'g' => self::ALL_VALUES
            ])
            ->setSearch([
                'g' => [
                    'id.id',
                    'name'
                ]
            ], $searchString)
            ->setPostProcess([
                'g' => self::ALL_POST_PROCESSES
            ])
            ->setOrderBy([
                ['g.created', 'DESC']
            ]);

        return $builder->getResult($this->entityManager);
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
                'g' => self::ALL_VALUES
            ])
            ->setSearch([
                'g' => [
                    'id.id',
                    'name'
                ]
            ], $searchString)
            ->setPostProcess([
                'g' => self::ALL_POST_PROCESSES
            ])
            ->setOrderBy([
                ['g.created', 'DESC']
            ]);

        return $builder->getResult($this->entityManager);
    }

    /**
     * @param string $id
     * @param string $searchString
     * @return array
     */
    public function findGroupProperties(string $id, string $searchString): array
    {
        $builder = new DqlQueryBuilder($this->propertyEntityClass);
        $builder
            ->setWhere('g.id.id = :id', ['id' => $id])
            ->setValues([
                'p' => self::PROPERTY_ALL_VALUES,
                'g' => [
                    ['id.id', 'id']
                ]
            ])
            ->setJoins([
                'p' => [
                    ['group', 'g', 'id']
                ]
            ])
            ->setSearch([
                'p' => [
                    'id.id',
                    'name'
                ]
            ], $searchString)
            ->setPostProcess([
                'p' => self::PROPERTY_ALL_POST_PROCESSES
            ])
            ->setOrderBy([
                ['p.created', 'DESC']
            ]);

        return $builder->getResult($this->entityManager);
    }

    /**
     * @param string $id
     * @return array
     */
    public function findPropertyCustomProperties(string $id): array
    {
        $builder = new DqlQueryBuilder($this->propertyEntityClass);
        $builder
            ->findById($id)
            ->setValues([
                'p' => [
                ],
                'cp' => [
                    'key',
                    'value',
                    'surrogateId'
                ]
            ])
            ->setJoins([
                'p' => [
                    ['customProperties', 'cp', 'surrogateId']
                ]
            ]);

        $result = $builder->getSingleResultOrNull($this->entityManager);

        if (array_key_exists('customProperties', $result)) {
            $result = $result['customProperties'];
        }

        if (null === $result) {
            $result = [];
        }

        return $result;
    }
}