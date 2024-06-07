<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\Doctrine\Orm;

use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\AptoOrmFinder;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlQueryBuilder;
use Apto\Catalog\Application\Core\Query\Shop\ShopFinder;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlBuilderException;
use Apto\Catalog\Domain\Core\Model\Shop\Shop;

class ShopOrmFinder extends AptoOrmFinder implements ShopFinder
{
    const ENTITY_CLASS = Shop::class;

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
                's' => [
                    ['id.id', 'id'],
                    'name',
                    'description',
                    'domain',
                    'connectorUrl',
                    'connectorToken',
                    'templateId',
                    'operatorName',
                    ['operatorEmail.email', 'operatorEmail'],
                    ['currency.code', 'currency'],
                    'created'
                ],
                'c' => [
                    ['id.id', 'id'],
                    'name',
                    'description',
                    'created'
                ],
                'l' => [
                    ['id.id', 'id'],
                    'name',
                    ['isocode.name', 'isocode'],
                    'created'
                ]
            ])
            ->setJoins([
                's' => [
                    ['categories', 'c', 'id'],
                    ['languages', 'l', 'id']
                ]
            ])
            ->setPostProcess([
                's' => [
                    'connectorUrl' => [DqlQueryBuilder::class, 'decodeJson']
                ],
                'c' => [
                    'name' => [DqlQueryBuilder::class, 'decodeJson'],
                    'description' => [DqlQueryBuilder::class, 'decodeJson']
                ],
                'l' => [
                    'name' => [DqlQueryBuilder::class, 'decodeJson']
                ]
            ]);

        return $builder->getSingleResultOrNull($this->entityManager);
    }

    /**
     * @param string $domain
     * @return array|null
     * @throws DqlBuilderException
     */
    public function findByDomain(string $domain)
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->findByProperty('domain', $domain)
            ->setValues([
                's' => [
                    ['id.id', 'id'],
                    'name',
                    'description',
                    'domain',
                    'connectorUrl',
                    'connectorToken',
                    'templateId',
                    'operatorName',
                    ['operatorEmail.email', 'operatorEmail'],
                    ['currency.code', 'currency'],
                    'created'
                ],
                'c' => [
                    ['id.id', 'id'],
                    'name',
                    'description',
                    'created'
                ],
                'l' => [
                    ['id.id', 'id'],
                    'name',
                    ['isocode.name', 'isocode'],
                    'created'
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
            ->setJoins([
                's' => [
                    ['categories', 'c', 'id'],
                    ['languages', 'l', 'id'],
                    ['customProperties', 'cp', 'surrogateId']
                ]
            ])
            ->setPostProcess([
                's' => [
                    'connectorUrl' => [DqlQueryBuilder::class, 'decodeJson']
                ],
                'c' => [
                    'name' => [DqlQueryBuilder::class, 'decodeJson'],
                    'description' => [DqlQueryBuilder::class, 'decodeJson']
                ],
                'l' => [
                    'name' => [DqlQueryBuilder::class, 'decodeJson']
                ],
                'cp' => [
                    'value' => [DqlQueryBuilder::class, 'decodeCustomPropertyValue'],
                    'translatable' => [DqlQueryBuilder::class, 'decodeBool']
                ]
            ]);

        return $builder->getSingleResultOrNull($this->entityManager);
    }

    /**
     * @param string $domain
     * @return array|null
     * @throws DqlBuilderException
     */
    public function findContextByDomain(string $domain)
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->findByProperty('domain', $domain)
            ->setValues([
                's' => [
                    ['id.id', 'id'],
                    'name',
                    'description',
                    'domain',
                    'connectorUrl',
                    'templateId',
                    ['currency.code', 'currency']
                ],
                'l' => [
                    ['id.id', 'id'],
                    'name',
                    ['isocode.name', 'isocode'],
                    'created'
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
            ->setJoins([
                's' => [
                    ['languages', 'l', 'id'],
                    ['customProperties', 'cp', 'surrogateId']
                ]
            ])
            ->setPostProcess([
                's' => [
                    'connectorUrl' => [DqlQueryBuilder::class, 'decodeJson']
                ],
                'l' => [
                    'name' => [DqlQueryBuilder::class, 'decodeJson']
                ],
                'cp' => [
                    'value' => [DqlQueryBuilder::class, 'decodeCustomPropertyValue'],
                    'translatable' => [DqlQueryBuilder::class, 'decodeBool']
                ]
            ]);

        return $builder->getSingleResultOrNull($this->entityManager);
    }

    /**
     * @param string $searchString
     * @return array
     * @throws DqlBuilderException
     */
    public function findShops(string $searchString = ''): array
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->setValues([
                's' => [
                    ['id.id', 'id'],
                    'name',
                    'description',
                    'domain',
                    'connectorUrl',
                    'connectorToken',
                    'templateId',
                    'operatorName',
                    ['operatorEmail.email', 'operatorEmail'],
                    ['currency.code', 'currency'],
                    'created'
                ]
            ])
            ->setSearch([
                's' => [
                    'id.id',
                    'name',
                    'domain'
                ]
            ], $searchString)
            ->setPostProcess([
                's' => [
                    'connectorUrl' => [DqlQueryBuilder::class, 'decodeJson'],
                ]
            ])
            ->setOrderBy([
                ['s.created', 'ASC']
            ]);

        return $builder->getResult($this->entityManager);
    }

    /**
     * @param string $domain
     * @return array|null
     * @throws DqlBuilderException
     */
    public function findConnectorConfigByDomain(string $domain)
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->findByProperty('domain', $domain)
            ->setValues([
                's' => [
                    ['id.id', 'shopId'],
                    'connectorUrl',
                    'connectorToken',
                    ['currency.code', 'currency']
                ]
            ])
            ->setPostProcess([
                's' => [
                    'connectorUrl' => [DqlQueryBuilder::class, 'decodeJson']
                ]
            ]);

        return $builder->getSingleResultOrNull($this->entityManager);
    }

    /**
     * @param string $id
     * @return array|null
     * @throws DqlBuilderException
     */
    public function findCustomProperties(string $id)
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->findById($id)
            ->setValues([
                's' => [
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
                's' => [
                    ['customProperties', 'cp', 'surrogateId']
                ]
            ]);

        return $builder->getSingleResultOrNull($this->entityManager);;
    }
}
