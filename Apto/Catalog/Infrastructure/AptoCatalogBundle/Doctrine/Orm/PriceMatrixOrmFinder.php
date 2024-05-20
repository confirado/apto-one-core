<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\Doctrine\Orm;

use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\AptoOrmFinder;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlBuilderException;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlPaginatorBuilder;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlQueryBuilder;
use Apto\Catalog\Application\Core\Query\PriceMatrix\PriceMatrixFinder;
use Apto\Catalog\Domain\Core\Model\PriceMatrix\PriceMatrix;

class PriceMatrixOrmFinder extends AptoOrmFinder implements PriceMatrixFinder
{
    const ENTITY_CLASS = PriceMatrix::class;

    const PRICE_MATRIX_ALL_VALUES = [
        ['id.id', 'id'],
        'name',
        'created'
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
                'p' => self::PRICE_MATRIX_ALL_VALUES
            ])
            ->setPostProcess([
                'p' => [
                    'name' => [DqlQueryBuilder::class, 'decodeJson']
                ]
            ]);

        return $builder->getSingleResultOrNull($this->entityManager);
    }

    /**
     * @param string $id
     * @param float $columnValue
     * @param float $rowValue
     * @param string $customerGroupId
     * @param string|null $fallbackCustomerGroupId
     * @param string $currencyCode
     * @param string $fallbackCurrencyCode
     * @return array
     */
    public function findNextHigherPriceByColumnRowValue(
        string $id,
        float $columnValue,
        float $rowValue,
        string $customerGroupId,
        string $fallbackCustomerGroupId = null,
        string $currencyCode,
        string $fallbackCurrencyCode
    ): array {
        $dql = '
            SELECT
                p.id.id as id,
                pe.id.id as priceMatrixElementId,
                ep.id.id as aptoPriceId,
                pe.position.rowValue as rowValue,
                pe.position.columnValue as columnValue,
                ep.price.amount as amount,
                ep.price.currency.code as currencyCode,
                ep.customerGroupId.id as customerGroupId
            FROM
                ' . $this->entityClass . ' p
            LEFT JOIN
                p.elements pe
            LEFT JOIN
                pe.aptoPrices ep
            WHERE
                p.id.id = :id AND
                pe.position.rowValue >= :rowValue AND
                pe.position.columnValue >= :columnValue AND
                ep.price.currency.code = :currencyCode AND
                ep.customerGroupId.id = :customerGroupId
            ORDER BY
                pe.position.rowValue, pe.position.columnValue ASC
        ';

        // @TODO: shouldn't e.g. the highest possible price be returned, instead of 0 amount? better pay too much instead of nothing?

        $query = $this->entityManager->createQuery($dql);
        $parameters = [
            'id' => $id,
            'columnValue' => $columnValue,
            'rowValue' => $rowValue,
            'currencyCode' => $currencyCode,
            'customerGroupId' => $customerGroupId
        ];

        $result = $query->setParameters($parameters)->setMaxResults(1)->getScalarResult();

        if (null !== $fallbackCustomerGroupId) {
            $parameters['customerGroupId'] = $fallbackCustomerGroupId;
            $resultFallbackCustomerGroup = $query->setParameters($parameters)->setMaxResults(1)->getScalarResult();
            $result = array_merge($result, $resultFallbackCustomerGroup);
        }

        if ($currencyCode !== $fallbackCurrencyCode) {
            $parameters['currencyCode'] = $fallbackCurrencyCode;
            $parameters['customerGroupId'] = $customerGroupId;
            $resultFallbackCurrency = $query->setParameters($parameters)->setMaxResults(1)->getScalarResult();
            $result = array_merge($result, $resultFallbackCurrency);
        }

        if (null !== $fallbackCustomerGroupId && $currencyCode !== $fallbackCurrencyCode) {
            $parameters['customerGroupId'] = $fallbackCustomerGroupId;
            $resultFallbackCustomerGroupFallbackCurrency = $query->setParameters($parameters)->setMaxResults(1)->getScalarResult();
            $result = array_merge($result, $resultFallbackCustomerGroupFallbackCurrency);
        }

        return $result;
    }

    /**
     * @param string $id
     * @param float $columnValue
     * @param float $rowValue
     * @param string $customerGroupId
     * @param string|null $fallbackCustomerGroupId
     * @param string $currencyCode
     * @param string $fallbackCurrencyCode
     * @return array
     */
    public function findAdditionalInformationByColumnRowValue(
        string $id,
        float $columnValue,
        float $rowValue,
        string $customerGroupId,
        string $fallbackCustomerGroupId = null,
        string $currencyCode,
        string $fallbackCurrencyCode
    ): array {
        // get prices
        $prices = $this->findNextHigherPriceByColumnRowValue($id, $columnValue, $rowValue, $customerGroupId, $fallbackCustomerGroupId, $currencyCode, $fallbackCurrencyCode);

        // get custom properties for each customer group
        $result = [];
        foreach ($prices as $price) {

            $dql = '
                SELECT
                    cp.key as key,
                    cp.value as value
                FROM
                    ' . $this->entityClass . ' p
                LEFT JOIN
                    p.elements pe
                JOIN
                    pe.customProperties cp
                WHERE
                    p.id.id = :id AND
                    pe.position.columnValue = :columnValue AND
                    pe.position.rowValue = :rowValue
            ';

            $query = $this->entityManager->createQuery($dql);
            $parameters = [
                'id' => $id,
                'columnValue' => $price['columnValue'],
                'rowValue' => $price['rowValue']
            ];

            $rawCustomProperties = $query->setParameters($parameters)->getScalarResult();

            // create an associative array by using column "keyname" as key and "value" as value
            $customProperties = array_combine(
                array_column($rawCustomProperties, 'key'),
                array_column($rawCustomProperties, 'value')
            );

            $result[$price['customerGroupId']] = $customProperties;
        }

        return $result;
    }

    /**
     * @param string $searchString
     * @return array
     * @throws DqlBuilderException
     */
    public function findPriceMatrices(string $searchString): array
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->setValues([
                'p' => self::PRICE_MATRIX_ALL_VALUES
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
                ]
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
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByListingPageNumber(int $pageNumber = 1, int $recordsPerPage = 50, string $searchString = ''): array
    {
        $builder = new DqlPaginatorBuilder($this->entityClass);
        $builder
            ->setPage($pageNumber)
            ->setRecordsPerPage($recordsPerPage)
            ->setValues([
                'p' => self::PRICE_MATRIX_ALL_VALUES
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
                ]
            ])
            ->setOrderBy([
                ['p.created', 'DESC']
            ]);

        return $builder->getResult($this->entityManager);
    }

    /**
     * @param string $id
     * @return array
     * @throws DqlBuilderException
     */
    public function findElements(string $id): array
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->findById($id)
            ->setValues([
                'p' => [
                ],
                'pe' => [
                    ['id.id', 'id'],
                    ['position.columnValue', 'columnValue'],
                    ['position.rowValue', 'rowValue']
                ],
                'pp' => [
                    ['id.id', 'id'],
                    ['price.amount', 'amount'],
                    ['price.currency.code', 'currencyCode'],
                    ['customerGroupId.id', 'customerGroupId']
                ],
                'cp' => [
                    ['id.id', 'id'],
                    'surrogateId',
                    'key',
                    'value',
                    'translatable',
                    'productConditionId'
                ]
            ])
            ->setPostProcess([
                'cp' => [
                    'value' => [DqlQueryBuilder::class, 'decodeCustomPropertyValue'],
                    'translatable' => [DqlQueryBuilder::class, 'decodeBool']
                ]
            ])
            ->setJoins([
                'p' => [
                    ['elements', 'pe', 'id']
                ],
                'pe' => [
                    ['aptoPrices', 'pp', 'id'],
                    ['customProperties', 'cp', 'surrogateId']
                ]
            ]);

        $result = $builder->getSingleResultOrNull($this->entityManager);

        if (null === $result) {
            return ['elements' => []];
        }

        return $result;
    }

    /**
     * @param string $id
     * @param string $elementId
     * @return array
     * @throws DqlBuilderException
     */
    public function findElementPrices(string $id, string $elementId): array
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->setValues([
                'p' => [
                ],
                'pe' => [
                    ['id.id', 'id'],
                    ['position.columnValue', 'columnValue'],
                    ['position.rowValue', 'rowValue']
                ],
                'pp' => [
                    ['id.id', 'id'],
                    ['price.amount', 'amount'],
                    ['price.currency.code', 'currencyCode'],
                    ['customerGroupId.id', 'customerGroupId']
                ]
            ])
            ->setWhere('p.id.id = :id AND pe.id.id = :elementId',
                [
                    'id' => $id,
                    'elementId' => $elementId
                ]
            )
            ->setJoins([
                'p' => [
                    ['elements', 'pe', 'id']
                ],
                'pe' => [
                    ['aptoPrices', 'pp', 'id']
                ]
            ]);

        return $builder->getSingleResultOrNull($this->entityManager);
    }

    /**
     * @param string $id
     * @param string $elementId
     * @return array
     * @throws DqlBuilderException
     */
    public function findElementCustomProperties(string $id, string $elementId): array
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->setValues([
                'p' => [
                ],
                'pe' => [
                    ['id.id', 'id'],
                    ['position.columnValue', 'columnValue'],
                    ['position.rowValue', 'rowValue']
                ],
                'cp' => [
                    ['id.id', 'id'],
                    'surrogateId',
                    'key',
                    'value',
                    'translatable',
                    'productConditionId'
                ]
            ])
            ->setWhere('p.id.id = :id AND pe.id.id = :elementId',
                [
                    'id' => $id,
                    'elementId' => $elementId
                ]
            )
            ->setPostProcess([
                'cp' => [
                    'value' => [DqlQueryBuilder::class, 'decodeCustomPropertyValue'],
                    'translatable' => [DqlQueryBuilder::class, 'decodeBool']
                ]
            ])
            ->setJoins([
                'p' => [
                    ['elements', 'pe', 'id']
                ],
                'pe' => [
                    ['customProperties', 'cp', 'surrogateId']
                ]
            ]);

        return $builder->getSingleResultOrNull($this->entityManager);
    }

    /**
     * first try to extract rules for price matrix empty cells, its just here as template should not used anymore because it not 100% correct
     *
     * @param string $id
     * @param string $currencyCode
     * @param string $customerGroupId
     * @param string|null $fallbackCustomerGroupId
     * @return array
     */
    public function findRules(string $id, string $currencyCode, string $customerGroupId, string $fallbackCustomerGroupId = null): array
    {
        $dql = '
            SELECT
                p.id.id as id,
                pe.id.id as priceMatrixElementId,
                ep.id.id as aptoPriceId,
                pe.position.rowValue as rowValue,
                pe.position.columnValue as columnValue,
                ep.price.amount as amount,
                ep.price.currency.code as currencyCode,
                ep.customerGroupId.id as customerGroupId
            FROM
                ' . $this->entityClass . ' p
            LEFT JOIN
                p.elements pe
            LEFT JOIN
                pe.aptoPrices ep
            WHERE
                p.id.id = :id AND
                ep.price.currency.code = :currencyCode AND
                ep.customerGroupId.id = :customerGroupId
            ORDER BY
                pe.position.rowValue, pe.position.columnValue ASC
        ';

        // @TODO: shouldn't e.g. the highest possible price be returned, instead of 0 amount? better pay too much instead of nothing?

        $query = $this->entityManager->createQuery($dql);
        $parameters = [
            'id' => $id,
            'currencyCode' => $currencyCode,
            'customerGroupId' => $customerGroupId
        ];
        $result = $query->setParameters($parameters)->getScalarResult();

        if (null !== $fallbackCustomerGroupId) {
            $parameters['customerGroupId'] = $fallbackCustomerGroupId;
            $resultFallback = $query->setParameters($parameters)->getScalarResult();
            $result = array_merge($result, $resultFallback);
        }

        // group by column and row value
        $grouped = [];
        foreach ($result as $price) {
            $grouped[$price['rowValue'] . '-' . $price['columnValue']][] = $price;
        }

        // group preferred prices by rowValue
        $prices = [];
        foreach ($grouped as $price) {
            $preferredPrice = $this->getPriceByPreferredCustomerGroup($price, $customerGroupId, $fallbackCustomerGroupId);

            if (!empty($preferredPrice)) {
                $prices[$preferredPrice['rowValue']][] = $preferredPrice;
            }
        }

        // sort rowValue groups by columnValue desc
        $rules = [];
        $maxColumnValue = null;
        foreach ($prices as &$row) {
            usort($row, function (array $a, array $b) {
                if ($a['columnValue'] === $b['columnValue']) {
                    return 0;
                }

                return ($a['columnValue'] < $b['columnValue']) ? 1 : -1;
            });

            if (null === $maxColumnValue || $row[0]['columnValue'] > $maxColumnValue) {
                $maxColumnValue = $row[0]['columnValue'];
            }

            if (isset($row[0])) {
                $rules[] = [
                    'maxRowValue' => $row[0]['rowValue'],
                    'maxColumnValue' => $row[0]['columnValue']
                ];
            }
        }

        return $rules;
    }

    /**
     * a lookup table to test values against matrix entries
     *
     * @param string $id
     * @param string $currencyCode
     * @param string $fallbackCurrencyCode
     * @param string $customerGroupId
     * @param string|null $fallbackCustomerGroupId
     * @return array
     */
    public function findMatrixLookupTable(string $id, string $currencyCode, string $fallbackCurrencyCode, string $customerGroupId, string $fallbackCustomerGroupId = null): array
    {
        // query all prices
        $prices = $this->findPrices($id, $currencyCode, $fallbackCurrencyCode, $customerGroupId, $fallbackCustomerGroupId);

        // query all row values
        $rows = $this->findRowValues($id);

        // query all column values
        $columns = $this->findColumnValues($id);

        // build cells
        $cells = [];
        foreach ($prices as $price) {
            $cells[$price['rowValue'] . '_' . $price['columnValue']][] = $price;
        }

        // build matrix lookup
        $matrix = [];
        foreach ($rows as $row) {
            foreach ($columns as $column) {
                $currentRow = $row['rowValue'];
                $currentColumn = $column['columnValue'];
                $cellId = $currentRow . '_' . $currentColumn;

                // if cell not exists this combination is false
                if (!array_key_exists($cellId, $cells)) {
                    $matrix[$currentRow][$currentColumn] = false;
                    continue;
                }

                // get price matching customer group
                $price = $this->getPriceByPreferredCustomerGroup($cells[$cellId], $customerGroupId, $fallbackCustomerGroupId);

                // if price not exists this combination is false
                if (empty($price)) {
                    $matrix[$currentRow][$currentColumn] = false;
                    continue;
                }

                // combination is true when no other rules injured
                $matrix[$currentRow][$currentColumn] = true;
            }
        }
        return $matrix;
    }

    /**
     * @param string $id
     * @return array
     */
    protected function findRowValues(string $id): array
    {
        $rowDql = '
            SELECT
                pe.position.rowValue as rowValue
            FROM
                ' . $this->entityClass . ' p
            LEFT JOIN
                p.elements pe
            WHERE
                p.id.id = :id
            GROUP BY
                pe.position.rowValue
            ORDER BY
                pe.position.rowValue ASC
        ';
        $rowQuery = $this->entityManager->createQuery($rowDql);
        $rowParameters = [
            'id' => $id
        ];
        return $rowQuery->setParameters($rowParameters)->getScalarResult();
    }

    /**
     * @param string $id
     * @return array
     */
    protected function findColumnValues(string $id): array
    {
        $columnDql = '
            SELECT
                pe.position.columnValue as columnValue
            FROM
                ' . $this->entityClass . ' p
            LEFT JOIN
                p.elements pe
            WHERE
                p.id.id = :id
            GROUP BY
                pe.position.columnValue
            ORDER BY
                pe.position.columnValue ASC
        ';
        $columnQuery = $this->entityManager->createQuery($columnDql);
        $columnParameters = [
            'id' => $id
        ];
        return $columnQuery->setParameters($columnParameters)->getScalarResult();
    }

    /**
     * @param string $id
     * @param string $currencyCode
     * @param string $fallbackCurrencyCode
     * @param string $customerGroupId
     * @param string|null $fallbackCustomerGroupId
     * @return array
     */
    protected function findPrices(string $id, string $currencyCode, string $fallbackCurrencyCode, string $customerGroupId, string $fallbackCustomerGroupId = null): array
    {
        $dql = '
            SELECT
                p.id.id as id,
                pe.id.id as priceMatrixElementId,
                pe.position.rowValue as rowValue,
                pe.position.columnValue as columnValue,
                ep.id.id as aptoPriceId,
                ep.price.amount as amount,
                ep.price.currency.code as currencyCode,
                ep.customerGroupId.id as customerGroupId
            FROM
                ' . $this->entityClass . ' p
            LEFT JOIN
                p.elements pe
            LEFT JOIN
                pe.aptoPrices ep
            WHERE
                p.id.id = :id AND
                ep.price.currency.code = :currencyCode AND
                ep.customerGroupId.id = :customerGroupId
            ORDER BY
                pe.position.rowValue, pe.position.columnValue ASC
        ';
        $query = $this->entityManager->createQuery($dql);

        $parameters = [
            'id' => $id,
            'currencyCode' => $currencyCode,
            'customerGroupId' => $customerGroupId
        ];
        $result = $query->setParameters($parameters)->getScalarResult();

        if (null !== $fallbackCustomerGroupId) {
            $parameters['customerGroupId'] = $fallbackCustomerGroupId;
            $resultFallback = $query->setParameters($parameters)->getScalarResult();
            $result = array_merge($result, $resultFallback);
        }

        if ($currencyCode !== $fallbackCurrencyCode) {
            $parameters['currencyCode'] = $fallbackCurrencyCode;
            $parameters['customerGroupId'] = $customerGroupId;
            $resultFallbackCurrency = $query->setParameters($parameters)->getScalarResult();
            $result = array_merge($result, $resultFallbackCurrency);
        }

        if (null !== $fallbackCustomerGroupId && $currencyCode !== $fallbackCurrencyCode) {
            $parameters['customerGroupId'] = $fallbackCustomerGroupId;
            $resultFallbackCustomerGroupFallbackCurrency = $query->setParameters($parameters)->getScalarResult();
            $result = array_merge($result, $resultFallbackCustomerGroupFallbackCurrency);
        }

        return $result;
    }

    /**
     * @param array $prices
     * @param string $customerGroupId
     * @param string|null $fallbackCustomerGroupId
     * @return array
     */
    protected function getPriceByPreferredCustomerGroup(array $prices, string $customerGroupId, string $fallbackCustomerGroupId = null): array
    {
        // lookup customer group id
        foreach ($prices as $price) {
            if ($price['customerGroupId'] === $customerGroupId) {
                return $price;
            }
        }

        // lookup fallback customer group id, if defined
        if (null !== $fallbackCustomerGroupId) {
            foreach ($prices as $price) {
                if ($price['customerGroupId'] === $fallbackCustomerGroupId) {
                    return $price;
                }
            }
        }

        return [];
    }

    /**
     * @param array $ids
     * @return array
     * @throws DqlBuilderException
     */
    public function findPriceMatricesByIds(array $ids): array
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->setValues([
                'p' => self::PRICE_MATRIX_ALL_VALUES
            ])
            ->setWhere('p.id.id IN (:ids)',
                [
                    'ids' => $ids
                ]
            )
            ->setPostProcess([
                'p' => [
                    'name' => [DqlQueryBuilder::class, 'decodeJson']
                ]
            ])
            ->setOrderBy([
                ['p.created', 'DESC']
            ]);

        return $builder->getResult($this->entityManager);
    }
}
