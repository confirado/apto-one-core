<?php

namespace Apto\Plugins\PricePerUnitElement\Infrastructure\PricePerUnitElementBundle\Doctrine\Orm;

use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\AptoOrmFinder;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlBuilderException;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlQueryBuilder;
use Apto\Plugins\PricePerUnitElement\Application\Core\Query\PricePerUnitItem\PricePerUnitItemFinder;
use Apto\Plugins\PricePerUnitElement\Domain\Core\Model\PricePerUnitItem\PricePerUnitItem;

class PricePerUnitItemOrmFinder extends AptoOrmFinder implements PricePerUnitItemFinder
{
    const ENTITY_CLASS = PricePerUnitItem::class;

    /**
     * @param string $id
     * @return array|null
     * @throws DqlBuilderException
     */
    public function findById(string $id)
    {
        $builder = new DqlQueryBuilder($this->entityClass, 'ppu');
        $builder
            ->findById($id)
            ->setValues([
                'ppu' => [
                    ['id.id', 'id'],
                    'elementId'
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
        $builder = new DqlQueryBuilder($this->entityClass, 'ppu');
        $builder
            ->findByProperty('elementId', $elementId)
            ->setValues([
                'ppu' => [
                    ['id.id', 'id'],
                    'elementId'
                ],
                'p' => [
                    ['id.id', 'id']
                ]
            ])
            ->setJoins([
                'ppu' => [
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
        $builder = new DqlQueryBuilder($this->entityClass, 'ppu');
        $builder
            ->findByProperty('elementId', $elementId)
            ->setValues([
                'ppu' => [
                ],
                'p' => [
                    ['id.id', 'id'],
                    ['price.amount', 'amount'],
                    ['price.currency.code', 'currencyCode'],
                    ['customerGroupId.id', 'customerGroupId']
                ]
            ])
            ->setJoins([
                'ppu' => [
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
                ppu.id.id as id,
                p.id.id as aptoPriceId,
                p.price.amount as amount,
                p.price.currency.code as currencyCode,
                p.customerGroupId.id as customerGroupId
            FROM
                ' . $this->entityClass . ' ppu
            LEFT JOIN
                ppu.aptoPrices p
            WHERE
                ppu.elementId = :elementId AND 
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