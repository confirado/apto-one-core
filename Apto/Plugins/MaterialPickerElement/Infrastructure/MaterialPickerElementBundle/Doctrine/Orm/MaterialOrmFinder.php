<?php

namespace Apto\Plugins\MaterialPickerElement\Infrastructure\MaterialPickerElementBundle\Doctrine\Orm;

use Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Material\Material;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

use Apto\Base\Domain\Core\Model\Color;
use Apto\Base\Domain\Core\Model\FileSystem\Directory\Directory;
use Apto\Base\Domain\Core\Model\FileSystem\File\File;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\AptoOrmFinder;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlBuilderException;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlPaginatorBuilder;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlQueryBuilder;
use Apto\Plugins\MaterialPickerElement\Application\Core\Query\Material\MaterialFinder;

class MaterialOrmFinder extends AptoOrmFinder implements MaterialFinder
{
    const ENTITY_CLASS = Material::class;

    const MATERIAL_ALL_VALUES = [
        ['id.id', 'id'],
        'active',
        'isNotAvailable',
        'identifier',
        'name',
        'description',
        'clicks',
        'reflection',
        'transmission',
        'absorption',
        'created',
        'position',
        'conditionSets',
    ];

    const MATERIAL_ALL_POST_PROCESSES = [
        'active' => [DqlQueryBuilder::class, 'decodeBool'],
        'isNotAvailable' => [DqlQueryBuilder::class, 'decodeBool'],
        'name' => [DqlQueryBuilder::class, 'decodeJson'],
        'description' => [DqlQueryBuilder::class, 'decodeJson'],
        'clicks' => [DqlQueryBuilder::class, 'decodeInteger'],
        'reflection' => [DqlQueryBuilder::class, 'decodeIntegerOrNull'],
        'transmission' => [DqlQueryBuilder::class, 'decodeIntegerOrNull'],
        'absorption' => [DqlQueryBuilder::class, 'decodeIntegerOrNull'],
        'position' => [DqlQueryBuilder::class, 'decodeInteger'],
        'conditionSets' => [DqlQueryBuilder::class, 'decodeSerialized'],
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
     * @throws DqlBuilderException
     */
    public function findById(string $id)
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->findById($id)
            ->setValues([
                'm' => self::MATERIAL_ALL_VALUES,
                'mf' => [
                    ['id.id', 'id'],
                    ['file.directory.path', 'directory'],
                    ['file.filename', 'filename'],
                    ['file.extension', 'extension']
                ]
            ])
            ->setJoins([
                'm' => [
                    ['previewImage', 'mf', 'id']
                ]
            ])
            ->setPostProcess([
                'm' => self::MATERIAL_ALL_POST_PROCESSES
            ]);

        $result = $builder->getSingleResultOrNull($this->entityManager);

        if (isset($result['previewImage'][0])) {
            $result['previewImage'] = $result['previewImage'][0];
            $file = new File(
                new Directory(
                    $result['previewImage']['directory']
                ),
                $result['previewImage']['filename'] . '.' . $result['previewImage']['extension']
            );
            $result['previewImage']['path'] = $file->getPath();
        }

        return $result;
    }

    /**
     * @param string $searchString
     * @return array
     * @throws DqlBuilderException
     */
    public function findMaterials(string $searchString): array
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->setValues([
                'm' => self::MATERIAL_ALL_VALUES
            ])
            ->setSearch([
                'm' => [
                    'id.id',
                    'identifier',
                    'name'
                ]
            ], $searchString)
            ->setPostProcess([
                'm' => self::MATERIAL_ALL_POST_PROCESSES
            ])
            ->setOrderBy([
                ['m.created', 'DESC']
            ]);

        return $builder->getResult($this->entityManager);
    }

    /**
     * @param int $pageNumber
     * @param int $recordsPerPage
     * @param string $searchString
     * @return array
     * @throws DqlBuilderException
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function findByListingPageNumber(int $pageNumber = 1, int $recordsPerPage = 20, string $searchString = ''): array
    {
        $builder = new DqlPaginatorBuilder($this->entityClass);
        $builder
            ->setPage($pageNumber)
            ->setRecordsPerPage($recordsPerPage)
            ->setValues([
                'm' => self::MATERIAL_ALL_VALUES
            ])
            ->setSearch([
                'm' => [
                    'id.id',
                    'identifier',
                    'name'
                ]
            ], $searchString)
            ->setPostProcess([
                'm' => self::MATERIAL_ALL_POST_PROCESSES
            ])
            ->setOrderBy([
                ['m.created', 'DESC']
            ]);

        return $builder->getResult($this->entityManager);
    }

    /**
     * @param string $id
     * @return array
     * @throws DqlBuilderException
     */
    public function findPrices(string $id): array
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->findById($id)
            ->setValues([
                'm' => [
                ],
                'mp' => [
                    ['id.id', 'id'],
                    ['price.amount', 'amount'],
                    ['price.currency.code', 'currencyCode'],
                    ['customerGroupId.id', 'customerGroupId']
                ]
            ])
            ->setJoins([
                'm' => [
                    ['aptoPrices', 'mp', 'id']
                ]
            ]);

        $result = $builder->getSingleResultOrNull($this->entityManager);

        if (null === $result) {
            return [];
        }

        return $result['aptoPrices'];
    }

    /**
     * @param string $id
     * @param string $customerGroupId
     * @param string|null $fallbackCustomerGroupId
     * @param string $currencyCode
     * @param string $fallbackCurrencyCode
     * @return array
     */
    public function findPrice(string $id, string $customerGroupId, string $fallbackCustomerGroupId = null, string $currencyCode, string $fallbackCurrencyCode): array
    {
        $dql = '
            SELECT
                m.id.id as id,
                p.id.id as aptoPriceId,
                p.price.amount as amount,
                p.price.currency.code as currencyCode,
                p.customerGroupId.id as customerGroupId
            FROM
                ' . $this->entityClass . ' m
            LEFT JOIN
                m.aptoPrices p
            WHERE
                m.id.id = :id AND
                p.price.currency.code in (:currencyCodes) AND
                p.customerGroupId.id in (:customerGroupIds)
        ';

        $query = $this->entityManager->createQuery($dql);
        $query->setParameters([
            'id' => $id,
            'currencyCodes' => [$currencyCode, $fallbackCurrencyCode],
            'customerGroupIds' => null === $fallbackCustomerGroupId ? [$customerGroupId] : [$customerGroupId, $fallbackCustomerGroupId]
        ]);

        return $query->getScalarResult();
    }

    /**
     * @param string $id
     * @return array
     * @throws DqlBuilderException
     */
    public function findGalleryImages(string $id): array
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->findById($id)
            ->setValues([
                'm' => [],
                'g' => [
                    ['id.id', 'id'],
                    ['file.directory.path', 'directory'],
                    ['file.filename', 'filename'],
                    ['file.extension', 'extension']
                ]
            ])
            ->setJoins([
                'm' => [
                    ['galleryImages', 'g', 'id']
                ]
            ]);

        $queryResult = $builder->getSingleResultOrNull($this->entityManager);

        $result = [
            'numberOfRecords' => 0,
            'data' => []
        ];

        if (null !== $queryResult && isset($queryResult['galleryImages'])) {
            $queryResult = $queryResult['galleryImages'];

            foreach ($queryResult as &$galleryImage) {
                $file = new File(
                    new Directory(
                        $galleryImage['directory']
                    ),
                    $galleryImage['filename'] . '.' . $galleryImage['extension']
                );
                $galleryImage['path'] = $file->getPath();
            }

            $result = [
                'numberOfRecords' => count($queryResult),
                'data' => $queryResult
            ];
        }

        return $result;
    }

    /**
     * @param string $id
     * @return array
     * @throws DqlBuilderException
     */
    public function findMaterialProperties(string $id): array
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->findById($id)
            ->setValues([
                'm' => [],
                'p' => [
                    ['id.id', 'id'],
                    'name'
                ],
                'g' => [
                    ['id.id', 'id'],
                    'name'
                ]
            ])
            ->setJoins([
                'm' => [
                    ['properties', 'p', 'id']
                ],
                'p' => [
                    ['group', 'g', 'id']
                ]
            ])
            ->setPostProcess([
                'p' => [
                    'name' => [DqlQueryBuilder::class, 'decodeJson']
                ],
                'g' => [
                    'name' => [DqlQueryBuilder::class, 'decodeJson']
                ]
            ]);

        $queryResult = $builder->getSingleResultOrNull($this->entityManager);

        $result = [
            'numberOfRecords' => 0,
            'data' => []
        ];

        if (null !== $queryResult && isset($queryResult['properties'])) {
            $queryResult = $queryResult['properties'];

            foreach ($queryResult as &$property) {
                if (isset($property['group'][0])) {
                    $property['group'] = $property['group'][0];
                }
            }

            $result = [
                'numberOfRecords' => count($queryResult),
                'data' => $queryResult
            ];
        }

        return $result;
    }

    /**
     * @param string $materialId
     * @param string $searchString
     * @return array
     * @throws DqlBuilderException
     */
    public function findNotAssignedMaterialProperties(string $materialId, string $searchString): array
    {
        $builder = new DqlQueryBuilder($this->propertyEntityClass);
        $builder
            ->setWhere('p.id.id NOT IN (
                SELECT pSub.id.id
                FROM ' . $this->entityClass . ' mSub
                JOIN mSub.properties pSub
                WHERE mSub.id.id = :materialId
            )', ['materialId' => $materialId])
            ->setValues([
                'p' => [
                    ['id.id', 'id'],
                    'name'
                ],
                'g' => [
                    ['id.id', 'id'],
                    'name'
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
                'p' => [
                    'name' => [DqlQueryBuilder::class, 'decodeJson']
                ],
                'g' => [
                    'name' => [DqlQueryBuilder::class, 'decodeJson']
                ]
            ])
            ->setOrderBy([
                ['p.created', 'DESC']
            ]);

        $result = $builder->getResult($this->entityManager);

        foreach ($result['data'] as &$property) {
            if (isset($property['group'][0])) {
                $property['group'] = $property['group'][0];
            }
        }

        return $result;
    }

    /**
     * @param string $id
     * @return array
     * @throws DqlBuilderException
     */
    public function findColorRatings(string $id): array
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->findById($id)
            ->setValues([
                'm' => [],
                'c' => [
                    ['id.id', 'id'],
                    ['color.red', 'color_red'],
                    ['color.green', 'color_green'],
                    ['color.blue', 'color_blue'],
                    'rating'
                ]
            ])
            ->setJoins([
                'm' => [
                    ['colorRatings', 'c', 'id']
                ]
            ]);

        $queryResult = $builder->getSingleResultOrNull($this->entityManager);

        $result = [
            'numberOfRecords' => 0,
            'data' => []
        ];

        if (null !== $queryResult && isset($queryResult['colorRatings'])) {
            $queryResult = $queryResult['colorRatings'];

            foreach ($queryResult as &$colorRating) {
                $color = new Color($colorRating['color_red'], $colorRating['color_green'], $colorRating['color_blue']);
                $colorRating = [
                    'id' => $colorRating['id'],
                    'color' => $color->getHex(),
                    'rating' => $colorRating['rating']
                ];
            }

            $result = [
                'numberOfRecords' => count($queryResult),
                'data' => $queryResult
            ];
        }

        return $result;
    }

    /**
     * @param string $id
     * @return array
     * @throws DqlBuilderException
     */
    public function findRenderImages(string $id): array
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->findById($id)
            ->setValues([
                'm' => [
                ],
                'r' => [
                    ['id.id', 'id'],
                    'layer',
                    'perspective',
                    'offsetX',
                    'offsetY'
                ],
                'p' => [
                    ['id.id', 'id'],
                    'name',
                ],
                'mf' => [
                    ['id.id', 'id'],
                    ['file.directory.path', 'path'],
                    ['file.filename', 'filename'],
                    ['file.extension', 'extension']
                ]
            ])
            ->setJoins([
                'm' => [
                    ['renderImages', 'r', 'id']
                ],
                'r' => [
                    ['pool', 'p', 'id'],
                    ['mediaFile', 'mf', 'id']
                ]
            ])
            ->setPostProcess([
                'p' => [
                    'name' => [DqlQueryBuilder::class, 'decodeJson']
                ]
            ])
            ->setOrderBy([
                ['p.created', 'DESC']
            ]);

        $result = $builder->getSingleResultOrNull($this->entityManager);

        return $result === null ? [] : $result['renderImages'];
    }
}
