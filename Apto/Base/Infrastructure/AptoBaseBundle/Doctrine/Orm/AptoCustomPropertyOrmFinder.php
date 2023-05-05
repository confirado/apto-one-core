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
}