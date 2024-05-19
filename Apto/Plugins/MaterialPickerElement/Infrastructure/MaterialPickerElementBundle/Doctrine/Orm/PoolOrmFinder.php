<?php

namespace Apto\Plugins\MaterialPickerElement\Infrastructure\MaterialPickerElementBundle\Doctrine\Orm;

use Apto\Base\Application\Core\Service\RequestStore;
use Apto\Base\Domain\Core\Model\AptoLocale;
use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Apto\Base\Domain\Core\Model\Color;
use Apto\Base\Domain\Core\Model\FileSystem\Directory\Directory;
use Apto\Base\Domain\Core\Model\FileSystem\File\File;
use Apto\Base\Domain\Core\Model\FileSystem\MediaFileSystemConnector;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\AptoOrmFinder;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlBuilderException;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlPaginatorBuilder;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlQueryBuilder;
use Apto\Plugins\MaterialPickerElement\Application\Core\Query\Pool\PoolFinder;
use Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Pool\Pool;

class PoolOrmFinder extends AptoOrmFinder implements PoolFinder
{
    const ENTITY_CLASS = Pool::class;

    const ALL_VALUES = [
        ['id.id', 'id'],
        'name',
        'created'
    ];

    const ALL_POST_PROCESSES = [
        'name' => [DqlQueryBuilder::class, 'decodeJson']
    ];

    const MATERIAL_ALL_VALUES = MaterialOrmFinder::MATERIAL_ALL_VALUES;
    const MATERIAL_ALL_POST_PROCESSES = MaterialOrmFinder::MATERIAL_ALL_POST_PROCESSES;

    /**
     * @var string
     */
    protected string $itemEntityClass;

    /**
     * @var string
     */
    protected string $materialEntityClass;

    /**
     * @var MediaFileSystemConnector
     */
    protected MediaFileSystemConnector $fileSystemConnector;

    /**
     * @var RequestStore
     */
    protected RequestStore $requestStore;

    /**
     * @param EntityManagerInterface $entityManager
     * @param MediaFileSystemConnector $fileSystemConnector
     * @param RequestStore $requestStore
     */
    public function __construct(EntityManagerInterface $entityManager, MediaFileSystemConnector $fileSystemConnector, RequestStore $requestStore)
    {
        parent::__construct($entityManager);
        $this->itemEntityClass = 'Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Pool\PoolItem';
        $this->materialEntityClass = 'Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Material\Material';
        $this->fileSystemConnector = $fileSystemConnector;
        $this->requestStore = $requestStore;
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
                'p' => self::ALL_VALUES
            ])
            ->setPostProcess([
                'p' => self::ALL_POST_PROCESSES
            ]);

        return $builder->getSingleResultOrNull($this->entityManager);
    }

    /**
     * @param string $searchString
     * @return array
     * @throws DqlBuilderException
     */
    public function findPools(string $searchString): array
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

        return $builder->getResult($this->entityManager);
    }

    /**
     * @param string $materialId
     * @param string $searchString
     * @return array
     * @throws DqlBuilderException
     */
    public function findPoolsWithoutMaterial(string $materialId, string $searchString): array
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->setWhere('p.id.id NOT IN (
                SELECT pSub.id.id
                FROM ' . $this->entityClass . ' pSub
                JOIN pSub.items piSub
                JOIN piSub.material mSub
                WHERE mSub.id.id = :materialId
            )', ['materialId' => $materialId])
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

        return $builder->getResult($this->entityManager);
    }

    /**
     * @param string $poolId
     * @return array
     * @throws DqlBuilderException
     */
    public function findPoolItems(string $poolId): array
    {
        return $this->findPoolItemsByWhere('po.id.id = :poolId', ['poolId' => $poolId]);
    }

    /**
     * @param string $materialId
     * @return array
     * @throws DqlBuilderException
     */
    public function findPoolItemsByMaterial(string $materialId): array
    {
        return $this->findPoolItemsByWhere('m.id.id = :materialId', ['materialId' => $materialId]);
    }

    /**
     * @param string $poolId
     * @param string $searchString
     * @return array
     * @throws DqlBuilderException
     */
    public function findNotInPoolMaterials(string $poolId, string $searchString): array
    {
        $builder = new DqlQueryBuilder($this->materialEntityClass);
        $builder
            ->setWhere('m.id.id NOT IN (
                SELECT mSub.id.id
                FROM ' . $this->materialEntityClass . ' mSub
                JOIN mSub.poolItems pi
                JOIN pi.pool p
                WHERE p.id.id = :poolId
            )', ['poolId' => $poolId])
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
     * @param string $poolId
     * @param array $filter
     * @param string $sortBy clicks|position|pricegroup
     * @param string $orderBy
     * @return array
     * @throws DqlBuilderException
     */
    public function findPoolItemsFiltered(string $poolId, array $filter, string $sortBy = 'clicks', string $orderBy = 'asc'): array
    {
        /**
         * @todo find only pool items who contains all selected properties
         * @todo UPDATE: implemented (see block "if (count($filter['properties']) > 0)" ) but i let this comment as example for the moment
         * the following mysql query returns the expected result:
         */
        $mysqlQuery = '
            SELECT
                m.surrogate_id as MaterialId, m.name as MaterialName, p.surrogate_id as PropertyId, p.name as PropertyName
            FROM
                plugin_material_picker_material m
            INNER JOIN
                plugin_material_picker_material_to_property mtp ON m.surrogate_id = mtp.material_surrogate_id
            INNER JOIN
                plugin_material_picker_property p ON p.surrogate_id = mtp.property_surrogate_id
            WHERE
                m.surrogate_id IN (
                    SELECT
                        mtp.material_surrogate_id
                    FROM
                        plugin_material_picker_material_to_property mtp
                    INNER JOIN
                        plugin_material_picker_material m ON m.surrogate_id = mtp.material_surrogate_id
                    INNER JOIN
                        plugin_material_picker_property p ON p.surrogate_id = mtp.property_surrogate_id
                    WHERE
                        p.surrogate_id IN (9,11)
                    GROUP BY
                        mtp.material_surrogate_id
                    HAVING
                        Count(mtp.material_surrogate_id) = 2
                )
        ';


        $where = 'po.id.id = :poolId AND m.active = 1';
        $parameters = [
            'poolId' => $poolId
        ];

        $orderByCommands = [];

        if ($filter['colorRating']) {
            $color = Color::fromHex($filter['colorRating']);
            $where .= ' AND mc.color.red = :colorRed AND mc.color.green = :colorGreen AND mc.color.blue = :colorBlue';
            $parameters['colorRed'] = $color->getRed();
            $parameters['colorGreen'] = $color->getGreen();
            $parameters['colorBlue'] = $color->getBlue();
            $orderByCommands[] = ['mc.rating', 'DESC'];
        }

        if ($filter['priceGroup']) {
            $where .= ' AND pg.id.id = :priceGroupId';
            $parameters['priceGroupId'] = $filter['priceGroup'];
        }

        if (count($filter['properties']) > 0) {
            // use this to find material items where the material has at least one search property assigned
            //$where .= ' AND mp.id.id in (:properties)';
            //$parameters['properties'] = $filter['properties'];

            // use this to find only material items where the material has all search properties assigned
            $where .= ' AND m.id.id in (
                SELECT
                    mSub.id.id
                FROM ' . $this->materialEntityClass . ' mSub
                JOIN
                    mSub.properties pSub
                WHERE
                    pSub.id.id IN (:properties)
                GROUP BY
                    mSub.id.id
                HAVING
                    Count(mSub.id.id) = :propertiesCount
            )';
            $parameters['properties'] = $filter['properties'];
            $parameters['propertiesCount'] = count($filter['properties']);
        }

        switch ($sortBy) {
            case 'position': {
                $orderByCommands[] = ['m.position', strtoupper($orderBy)];
                break;
            }
            case 'pricegroup': {
                // to have always the same list for later manual pricegroup sorting we sort pricegroup in mysql by created date ascending
                $orderByCommands[] = ['m.clicks', 'DESC'];
                break;
            }
            default: {
                $orderByCommands[] = ['m.clicks', strtoupper($orderBy)];
            }
        }

        $builder = new DqlQueryBuilder($this->itemEntityClass);
        $builder
            ->setWhere($where, $parameters)
            ->setValues([
                'p' => [
                    ['id.id', 'id'],
                    'created'
                ],
                'po' => [
                    ['id.id', 'id'],
                    'name'
                ],
                'm' => self::MATERIAL_ALL_VALUES,
                'pg' => [
                    ['id.id', 'id'],
                    'name',
                    'additionalCharge',
                    'internalName'
                ],
                'mp' => [
                    ['id.id', 'id'],
                    'name'
                ],
                'mf' => [
                    ['id.id', 'id'],
                    ['file.directory.path', 'directory'],
                    ['file.filename', 'filename'],
                    ['file.extension', 'extension']
                ],
                'mc' => [
                    ['id.id', 'id'],
                    ['color.red', 'color_red'],
                    ['color.green', 'color_green'],
                    ['color.blue', 'color_blue'],
                    'rating'
                ],
                'mpcp' => [
                    ['id.id', 'id'],
                    'surrogateId',
                    'key',
                    'value'
                ],
                'mpg' => [
                    ['id.id', 'id'],
                    'name'
                ],
                'mgi' => [
                    ['id.id', 'id'],
                    ['file.directory.path', 'directory'],
                    ['file.filename', 'filename'],
                    ['file.extension', 'extension']
                ]
            ])
            ->setJoins([
                'p' => [
                    ['pool', 'po', 'id'],
                    ['material', 'm', 'id'],
                    ['priceGroup', 'pg', 'id']
                ],
                'm' => [
                    ['properties', 'mp', 'id'],
                    ['previewImage', 'mf', 'id'],
                    ['galleryImages', 'mgi', 'id'],
                    ['colorRatings', 'mc', 'id']
                ],
                'mp' => [
                    ['customProperties', 'mpcp', 'surrogateId'],
                    ['group', 'mpg', 'id']
                ]
            ])
            ->setPostProcess([
                'm' => self::MATERIAL_ALL_POST_PROCESSES,
                'pg' => [
                    'name' => [DqlQueryBuilder::class, 'decodeJson'],
                    'internalName' => [DqlQueryBuilder::class, 'decodeJson'],
                    'additionalCharge' => [DqlQueryBuilder::class, 'decodeFloat']
                ],
                'mp' => [
                    'name' => [DqlQueryBuilder::class, 'decodeJson']
                ],
                'mpg' => [
                    'name' => [DqlQueryBuilder::class, 'decodeJson']
                ]
            ])
            ->setOrderBy($orderByCommands);

        if (array_key_exists('searchString', $filter) && trim($filter['searchString']) !== '') {
            $builder->setSearch([
                'm' => [
                    'name'
                ]
            ], $filter['searchString']);
        }

        $result = $builder->getResult($this->entityManager);

        foreach ($result['data'] as &$poolItem) {
            // assign pool
            if (isset($poolItem['pool'][0])) {
                $poolItem['pool'] = $poolItem['pool'][0];
            }

            // assign material
            if (isset($poolItem['material'][0])) {
                $poolItem['material'] = $poolItem['material'][0];

                // assign material previewImage
                if (isset($poolItem['material']['previewImage'][0])) {
                    $poolItem['material']['previewImage'] = $poolItem['material']['previewImage'][0];
                    $previewImageFile = new File(
                        new Directory(
                            $poolItem['material']['previewImage']['directory']
                        ),
                        $poolItem['material']['previewImage']['filename'] . '.' . $poolItem['material']['previewImage']['extension']
                    );
                    $poolItem['material']['previewImage']['path'] = $previewImageFile->getPath();
                    $poolItem['material']['previewImage']['fileUrl'] = $this->fileSystemConnector->getFileUrl($previewImageFile);
                }

                // assign material galleryImages
                if (isset($poolItem['material']['galleryImages'])) {
                    foreach ($poolItem['material']['galleryImages'] as &$galleryImage) {
                        $galleryImageFile = new File(
                            new Directory(
                                $galleryImage['directory']
                            ),
                            $galleryImage['filename'] . '.' . $galleryImage['extension']
                        );
                        $galleryImage['path'] = $galleryImageFile->getPath();
                        $galleryImage['fileUrl'] = $this->fileSystemConnector->getFileUrl($galleryImageFile);
                    }
                }

                // assign material colorRatings
                if (isset($poolItem['material']['colorRatings'])) {
                    foreach ($poolItem['material']['colorRatings'] as &$colorRating) {
                        $color = new Color($colorRating['color_red'], $colorRating['color_green'], $colorRating['color_blue']);
                        $colorRating = [
                            'id' => $colorRating['id'],
                            'color' => $color->getHex(),
                            'rating' => $colorRating['rating']
                        ];
                    }
                }

                // assign material properties icon
                if (isset($poolItem['material']['properties'])) {
                    foreach ($poolItem['material']['properties'] as &$materialProperty) {
                        if (isset($materialProperty['group'][0])) {
                            $materialProperty['group'] = $materialProperty['group'][0];
                        }
                        foreach ($materialProperty['customProperties'] as &$propertyCustomProperty) {
                            if ($propertyCustomProperty['key'] === 'icon') {
                                $iconFile = File::createFromPath($propertyCustomProperty['value']);
                                $materialProperty['icon'] = [
                                    'directory' => $iconFile->getDirectory()->getPath(),
                                    'extension' => $iconFile->getExtension(),
                                    'path' => $iconFile->getPath(),
                                    'fileUrl' => $this->fileSystemConnector->getFileUrl($iconFile)
                                ];
                                $poolItem['material']['hasPropertyIcons'] = true;
                            }
                        }
                    }
                }
            }

            // assign priceGroup
            if (isset($poolItem['priceGroup'][0])) {
                $poolItem['priceGroup'] = $poolItem['priceGroup'][0];
            }
        }

        // sort by pricegroup name in a natural way
        if ($sortBy === 'pricegroup') {
            $locale = new AptoLocale($this->requestStore->getLocale());
            usort($result['data'], function($val1, $val2) use ($locale, $orderBy) {
                $val1Name = AptoTranslatedValue::fromArray($val1['priceGroup']['name'])->getTranslation($locale, new AptoLocale('de_DE'), true)->getValue();
                $val2Name = AptoTranslatedValue::fromArray($val2['priceGroup']['name'])->getTranslation($locale, new AptoLocale('de_DE'), true)->getValue();
                $val1Clicks = $val1['material']['clicks'];
                $val2Clicks = $val2['material']['clicks'];
                $values = [$val1Name, $val2Name];

                if ($values[0] === $values[1]) {
                    // sort by clicks within pricegroup
                    return $val2Clicks <=> $val1Clicks;
                }

                sort($values, SORT_NATURAL);

                if ($val1Name === $values[0]) {
                    return strtoupper($orderBy) === 'ASC' ? -1 : 1;
                }

                return strtoupper($orderBy) === 'ASC' ? 1 : -1;
            });
        }

        return $result;
    }

    /**
     * @param string $poolId
     * @return array
     */
    public function findPoolPriceGroups(string $poolId): array
    {
        $dql = '
            SELECT DISTINCT pg.id.id as id, pg.name
            FROM '.$this->itemEntityClass.' pi
            JOIN pi.pool po
            JOIN pi.priceGroup pg
            WHERE po.id.id = :poolId
        ';

        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('poolId', $poolId);

        $result = $query->getScalarResult();
        foreach ($result as &$priceGroup) {
            $priceGroup['name'] = DqlQueryBuilder::decodeJson($priceGroup['name']);
        }
        return $result;
    }

    /**
     * @param string $poolId
     * @return array
     */
    public function findPoolPropertyGroups(string $poolId): array
    {
        $dql = '
            SELECT DISTINCT
                propertyGroup.id.id as groupId, propertyGroup.name as groupName, propertyGroup.allowMultiple as groupAllowMultiple,
                properties.id.id as propertyId, properties.name as propertyName, properties.isDefault as isDefault
            FROM '.$this->itemEntityClass.' pi
            JOIN pi.pool pool
            JOIN pi.material material
            JOIN material.properties properties
            JOIN properties.group propertyGroup
            WHERE pool.id.id = :poolId AND material.active = 1
        ';

        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('poolId', $poolId);

        $result = $query->getScalarResult();

        $nestedResult = [];

        foreach ($result as $row) {
            if (!array_key_exists($row['groupId'], $nestedResult)) {
                $nestedResult[$row['groupId']]['id'] = $row['groupId'];
                $nestedResult[$row['groupId']]['name'] = DqlQueryBuilder::decodeJson($row['groupName']);
                $nestedResult[$row['groupId']]['allowMultiple'] = DqlQueryBuilder::decodeBool($row['groupAllowMultiple']);
            }
            $nestedResult[$row['groupId']]['properties'][] = [
                'id' => $row['propertyId'],
                'name' => DqlQueryBuilder::decodeJson($row['propertyName']),
                'isDefault' => DqlQueryBuilder::decodeJson($row['isDefault']),
            ];
        }

        return array_values($nestedResult);
    }

    /**
     * @param string $poolId
     * @param array $materials
     * @param string $perspective
     * @return array
     */
    public function findRenderImagesByMaterials(string $poolId, array $materials, string $perspective): array
    {
        $parameters = [
            'poolId' => $poolId,
            'materialIds' => $materials,
            'perspective' => $perspective
        ];

        $dql = 'SELECT
                  r.id.id as renderImageId,
                  r.layer,
                  r.perspective,
                  r.offsetX,
                  r.offsetY,
                  mf.file.directory.path as path,
                  mf.file.filename as filename,
                  mf.file.extension as extension
              FROM
                  ' . $this->entityClass . ' p
              LEFT JOIN
                  p.items pi
              LEFT JOIN
                  pi.material m
              LEFT JOIN
                  m.renderImages r
              LEFT JOIN
                  r.mediaFile mf
              WHERE
                  p.id.id = :poolId AND m.id.id IN (:materialIds) AND r.perspective = :perspective
              ORDER BY
                  r.layer ASC';

        $query = $this->entityManager->createQuery($dql);
        $query->setParameters($parameters);

        return $query->getScalarResult();
    }

    /**
     * @param string $where
     * @param array $parameters
     * @return array
     * @throws DqlBuilderException
     */
    private function findPoolItemsByWhere(string $where, array $parameters = []): array
    {
        $builder = new DqlQueryBuilder($this->itemEntityClass);

        $builder
            ->setWhere($where, $parameters)
            ->setValues([
                'p' => [
                    ['id.id', 'id'],
                    'created'
                ],
                'po' => [
                    ['id.id', 'id'],
                    'name'
                ],
                'm' => [
                    ['id.id', 'id'],
                    'identifier',
                    'name'
                ],
                'pg' => [
                    ['id.id', 'id'],
                    'name',
                    'internalName',
                    'additionalCharge'
                ]
            ])
            ->setJoins([
                'p' => [
                    ['pool', 'po', 'id'],
                    ['material', 'm', 'id'],
                    ['priceGroup', 'pg', 'id']
                ]
            ])
            ->setPostProcess([
                'po' => [
                    'name' => [DqlQueryBuilder::class, 'decodeJson']
                ],
                'm' => [
                    'name' => [DqlQueryBuilder::class, 'decodeJson']
                ],
                'pg' => [
                    'name' => [DqlQueryBuilder::class, 'decodeJson'],
                    'internalName' => [DqlQueryBuilder::class, 'decodeJson'],
                    'additionalCharge' => [DqlQueryBuilder::class, 'decodeFloat']
                ]
            ])
            ->setOrderBy([
                ['p.created', 'DESC']
            ]);

        $result = $builder->getResult($this->entityManager);

        foreach ($result['data'] as &$poolItem) {
            if (isset($poolItem['pool'][0])) {
                $poolItem['pool'] = $poolItem['pool'][0];
            }

            if (isset($poolItem['material'][0])) {
                $poolItem['material'] = $poolItem['material'][0];
            }

            if (isset($poolItem['priceGroup'][0])) {
                $poolItem['priceGroup'] = $poolItem['priceGroup'][0];
            }
        }

        return $result;
    }

    /**
     * @param string $poolId
     * @param string $materialId
     * @return array|null
     */
    public function findPriceGroup(string $poolId, string $materialId)
    {
        // pg - plugin_material_picker_price_group
        // pi - plugin_material_picker_pool_item

        $dql = '
            SELECT pg.id.id as id, pg.additionalCharge, pg.priceMatrix.id as priceMatrixId, pg.priceMatrix.row as priceMatrixRow, pg.priceMatrix.column as priceMatrixColumn, pg.priceMatrix.pricePostProcess as priceMatrixPricePostProcess
            FROM ' . $this->itemEntityClass . ' pi
            JOIN pi.pool p
            JOIN pi.material m
            JOIN pi.priceGroup pg
            WHERE p.id.id = :poolId AND m.id.id = :materialId
        ';

        $query = $this->entityManager->createQuery($dql);
        $query
            ->setParameters([
                'poolId' => $poolId,
                'materialId' => $materialId
            ]);

        $result = $query->getScalarResult();

        if (isset($result[0])) {
            $result = $result[0];
        }

        if (count($result) < 1) {
            $result = null;
        }

        return $result;
    }

    /**
     * @param string $poolId
     * @param array $filter
     * @return array|array[]
     * @throws DqlBuilderException
     */
    public function findPoolColors(string $poolId, array $filter): array
    {
        $filter['colorRating'] = false;
        $poolItems = $this->findPoolItemsFiltered($poolId, $filter);
        $colors = [
            '#000000' => [
                'name'=> 'Schwarz',
                'hex'=> '#000000',
                'visibleHex'=> '#404040',
                'inPool'=> false,
            ],
            '#ff0000' => [
                'name'=> 'Rot',
                'hex'=> '#ff0000',
                'visibleHex'=> '#F07B7B',
                'inPool'=> false,
            ],
            '#ffff00' => [
                'name'=> 'Gelb',
                'hex'=> '#ffff00',
                'visibleHex'=> '#F0F07B',
                'inPool'=> false,
            ],
            '#00ff00' => [
                'name'=> 'Grün',
                'hex'=> '#00ff00',
                'visibleHex'=> '#9AF07B',
                'inPool'=> false,
            ],
            '#0000ff' => [
                'name'=> 'Blau',
                'hex'=> '#0000ff',
                'visibleHex'=> '#7BB1F0',
                'inPool'=> false,
            ],
            '#ffa500' => [
                'name'=> 'Orange',
                'hex'=> '#ffa500',
                'visibleHex'=> '#F0D17B',
                'inPool'=> false,
            ],
            '#ffffff' => [
                'name'=> 'Weiß',
                'hex'=> '#ffffff',
                'visibleHex'=> '#f4f4f4',
                'inPool'=> false
            ],
            '#888888' => [
                'name'=> 'Grau',
                'hex'=> '#888888',
                'visibleHex'=> '#868686',
                'inPool'=> false
            ],
            '#f5f5dc' => [
                'name'=> 'Beige',
                'hex'=> '#f5f5dc',
                'visibleHex'=> '#F0E4C1',
                'inPool'=> false
            ],
            '#b47d49' => [
                'name'=> 'Braun',
                'hex'=> '#b47d49',
                'visibleHex'=> '#CBA46E',
                'inPool'=> false
            ],
            '#3f888f' => [
                'name'=> 'Türkis',
                'hex'=> '#3f888f',
                'visibleHex'=> '#3f888f',
                'inPool'=> false
            ],
            '#8800ff' => [
                'name'=> 'Violett',
                'hex'=> '#8800ff',
                'visibleHex'=> '#9F7BF0',
                'inPool'=> false
            ]
        ];

        foreach ($poolItems['data'] as $poolItemData) {
            foreach ($poolItemData['material']['colorRatings'] as $poolItem) {
                if($colors[strtolower(strval($poolItem['color']))]['inPool'] === false) {
                    $colors[$poolItem['color']]['inPool'] = true;
                }
            }
        }
        return $colors;
    }
}
