<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm;

use Apto\Base\Application\Core\Query\AptoCustomProperty\AptoCustomPropertyFinder;
use Apto\Base\Domain\Core\Model\AptoCustomProperty;

class AptoCustomPropertyOrmFinder extends AptoOrmFinder implements AptoCustomPropertyFinder
{
    const ENTITY_CLASS = AptoCustomProperty::class;

    /**
     * @return array
     */
    public function findUsedKeys()
    {
        $dql = '
            SELECT
                cp.key
            FROM
                  ' . $this->entityClass . ' cp
            GROUP BY
                cp.key';

        $query = $this->entityManager->createQuery($dql);

        $results = [];

        foreach ($query->getArrayResult() as $result) {
            $results[] = $result['key'];
        }

        return $results;
    }

    /**
     * @return array
     * @throws DqlBuilderException
     */
    public function findCustomProperties(): array
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->setValues([
                'a' => [
                    ['id.id', 'id'],
                    'key',
                    'value',
                    'translatable',
                    'productConditionId'
                ]
            ])
            ->setPostProcess([
                'a' => [
                    'translatable' => [DqlQueryBuilder::class, 'decodeBool']
                ]
            ])
            ->setOrderBy([
                ['a.key', 'ASC']
            ]);

        return $builder->getResult($this->entityManager);
    }
}
