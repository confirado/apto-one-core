<?php

namespace Apto\Plugins\FloatInputElement\Infrastructure\FloatInputElementBundle\Doctrine\Orm;

use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\AptoOrmFinder;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlBuilderException;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlQueryBuilder;
use Apto\Plugins\FloatInputElement\Application\Core\Query\FloatInputItem\FloatInputItemFinder;
use Apto\Plugins\FloatInputElement\Domain\Core\Model\FloatInputItem\FloatInputItem;

class FloatInputItemOrmFinder extends AptoOrmFinder implements FloatInputItemFinder
{
    const ENTITY_CLASS = FloatInputItem::class;

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
                    'elementId'
                ]
            ]);

        $result = $builder->getSingleResultOrNull($this->entityManager);

        return $result;
    }

    /**
     * @param string $elementId
     * @return array|null
     * @throws DqlBuilderException
     */
    public function findByElementId(string $elementId)
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->findByProperty('elementId', $elementId)
            ->setValues([
                'f' => [
                    ['id.id', 'id'],
                    'elementId'
                ],
                'p' => [
                    ['id.id', 'id']
                ]
            ])
            ->setJoins([
                'f' => [
                    ['aptoPrices', 'p', 'id']
                ]
            ]);

        return $builder->getResult($this->entityManager);
    }

    /**
     * @param string $elementId
     * @return array
     * @throws DqlBuilderException
     */
    public function findPrices(string $elementId): array
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->findByProperty('elementId', $elementId)
            ->setValues([
                'f' => [
                ],
                'p' => [
                    ['id.id', 'id'],
                    ['price.amount', 'amount'],
                    ['price.currency.code', 'currencyCode'],
                    ['customerGroupId.id', 'customerGroupId']
                ]
            ])
            ->setJoins([
                'f' => [
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
     * @param string $elementId
     * @param string $customerGroupId
     * @param string|null $fallbackCustomerGroupId
     * @param string $currencyCode
     * @param string $fallbackCurrencyCode
     * @return array
     */
    public function findPrice(string $elementId, string $customerGroupId, string $fallbackCustomerGroupId = null, string $currencyCode, string $fallbackCurrencyCode): array
    {
        $dql = '
            SELECT
                f.id.id as id,
                p.id.id as aptoPriceId,
                p.price.amount as amount,
                p.price.currency.code as currencyCode,
                p.customerGroupId.id as customerGroupId
            FROM
                ' . $this->entityClass . ' f
            LEFT JOIN
                f.aptoPrices p
            WHERE
                f.elementId = :elementId AND 
                p.price.currency.code in (:currencyCodes) AND
                p.customerGroupId.id in (:customerGroupIds)
        ';

        $query = $this->entityManager->createQuery($dql);
        $query->setParameters([
            'elementId' => $elementId,
            'currencyCodes' => [$currencyCode, $fallbackCurrencyCode],
            'customerGroupIds' => null === $fallbackCustomerGroupId ? [$customerGroupId] : [$customerGroupId, $fallbackCustomerGroupId]
        ]);

        return $query->getScalarResult();
    }
}