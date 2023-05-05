<?php

namespace Apto\Plugins\SelectBoxElement\Infrastructure\SelectBoxElementBundle\Doctrine\Orm;

use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\AptoOrmFinder;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlBuilderException;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlQueryBuilder;
use Apto\Plugins\SelectBoxElement\Application\Core\Query\SelectBoxItem\SelectBoxItemFinder;
use Apto\Plugins\SelectBoxElement\Domain\Core\Model\SelectBoxItem\SelectBoxItem;

class SelectBoxItemOrmFinder extends AptoOrmFinder implements SelectBoxItemFinder
{
    const ENTITY_CLASS = SelectBoxItem::class;

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
                    'elementId',
                    'name',
                    'isDefault'
                ]
            ])
            ->setPostProcess([
                's' => [
                    'name' => [DqlQueryBuilder::class, 'decodeJson'],
                    'isDefault' => [DqlQueryBuilder::class, 'decodeBool']
                ]
            ]);

        $result = $builder->getSingleResultOrNull($this->entityManager);

        return $result;
    }

    /**
     * @param string $elementId
     * @return array
     * @throws DqlBuilderException
     */
    public function findByElementId(string $elementId)
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->findByProperty('elementId', $elementId)
            ->setValues([
                's' => [
                    ['id.id', 'id'],
                    'elementId',
                    'name',
                    'isDefault'
                ],
                'p' => [
                    ['id.id', 'id']
                ]
            ])
            ->setJoins([
                's' => [
                    ['aptoPrices', 'p', 'id']
                ]
            ])
            ->setPostProcess([
                's' => [
                    'name' => [DqlQueryBuilder::class, 'decodeJson'],
                    'isDefault' => [DqlQueryBuilder::class, 'decodeBool']
                ]
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
                's' => [
                ],
                'p' => [
                    ['id.id', 'id'],
                    ['price.amount', 'amount'],
                    ['price.currency.code', 'currencyCode'],
                    ['customerGroupId.id', 'customerGroupId']
                ]
            ])
            ->setJoins([
                's' => [
                    ['aptoPrices', 'p', 'id']
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
                s.id.id as id,
                p.id.id as aptoPriceId,
                p.price.amount as amount,
                p.price.currency.code as currencyCode,
                p.customerGroupId.id as customerGroupId
            FROM
                ' . $this->entityClass . ' s
            LEFT JOIN
                s.aptoPrices p
            WHERE
                s.id.id = :id AND 
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
}